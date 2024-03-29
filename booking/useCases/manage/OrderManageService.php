<?php

namespace booking\useCases\manage;

use AmoCRM\Exceptions\AmoCRMApiErrorResponseException;
use AmoCRM\OAuth2\Client\Provider\AmoCRMException;
use booking\entities\AmoCRM\Credential;
use booking\entities\Order\Order;
use booking\entities\Order\OrderItem;
use booking\entities\Schedule\Schedule;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use booking\forms\AmoCRM\hipsorurzu\LeadPipeline7665106;
use booking\forms\manage\Order\CustomerForm;
use booking\forms\manage\Order\LicenseForm;
use booking\forms\manage\Order\OrderCreateForm;
use booking\forms\manage\Order\OrderEditForm;
use booking\forms\manage\Order\OrderItemForm;
use booking\forms\manage\Order\RacersForm;
use booking\forms\manage\Order\SlotCreateForm;
use booking\forms\manage\Slot\SlotForm;
use booking\helpers\AppHelper;
use booking\helpers\DateHelper;
use booking\repositories\CarTypeRepository;
use booking\repositories\CredentialRepository;
use booking\repositories\LicenseRepository;
use booking\repositories\OrderRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\SlotRepository;
use booking\repositories\UserRepository;
use booking\services\TransactionManager;
use booking\useCases\AmoCRM\AmoCRMService;
use Yii;

class OrderManageService
{
    private OrderRepository $repository;
    private UserRepository $userRepository;
    private LicenseRepository $licenseRepository;
    private AmoCRMService $amoCRMService;
    private CredentialRepository $credentialRepository;
    private TransactionManager $transaction;

    public function __construct(
        OrderRepository $repository,
        UserRepository $userRepository,
        LicenseRepository   $licenseRepository,
        AmoCRMService $amoCRMService,
        CredentialRepository $credentialRepository,
        TransactionManager $transaction
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->licenseRepository = $licenseRepository;
        $this->amoCRMService = $amoCRMService;
        $this->credentialRepository = $credentialRepository;
        $this->transaction = $transaction;
    }

    public function create(OrderCreateForm $form): Order
    {
        $this->guardCanCreate();
        $entity = Order::create(
            (int)$form->status,
            $form->note,
        );
        #customer
        if (!($customer=$this->userRepository->findByTelephone($form->customer->telephone))) {
            $customer=User::createCustomer(
                $form->customer->name,
                $form->customer->telephone,
            );
        }

        $this->transaction->wrap(function () use ($entity, $customer,$form) {

            $this->userRepository->save($customer);

            $entity->setCustomer($customer);
            #items
            if ($form->items) {
                foreach ($form->items as $item) {
                    if ($item->qty) {
                        $entity->changeItem($item->carType_id, intval($item->qty));
                    }
                }
            }
            $this->repository->save($entity);
        });

        return $entity;
    }
    public function createEmpty(): Order
    {
        $this->guardCanCreate();
        $entity = Order::create();
        $this->repository->save($entity);
        return $entity;
    }
    public function edit($entityOrId, OrderEditForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            (int)$form->status,
            (string)$form->note,
        );
        #customer
        if (!($customer=$this->userRepository->findByTelephone($form->customer->telephone))) {
            $customer=User::createCustomer(
                $form->customer->name,
                $form->customer->telephone,
            );
        }
        $this->transaction->wrap(function () use ($entity, $customer,$form) {
            $this->userRepository->save($customer);
            $entity->setCustomer($customer);
            #items
            foreach ($form->items as $item) {
                if ($item->_orderItem) {
                    $entity->editItem($item->_orderItem->id,$item->qty);
                } else {
                    $entity->changeItem($item->carType_id,$item->qty);
                }
            }
            $this->repository->save($entity);
        });
    }

    /**
     * Добавляет(изменяет заказ)
     * @param SlotCreateForm $form
     * @return Order
     */
    public function addToOrder(SlotCreateForm $form):Order
    {
        if (!$entity=$form->_order) {
            $entity = Order::create();
        }

        $this->guardCanAddItem($entity);

        foreach ($form->items as $item) {
            $entity->changeItem($form->slot_id, $item->carType_id, intval($item->qty));
        }
        if (!$entity->isReservationProcess()) {
            $entity->onReserved();
        }
        $this->repository->save($entity);
        return $entity;
    }
    public function changeItem($itemOrItemId,int $qty):?Order
    {
        $item=$this->repository->getItem($itemOrItemId);
        if ($this->guardCanChangeItem($itemOrItemId,$qty,true)) {
            $items=$item->order->items;
            foreach ($items as $orderItem) {
                if ($orderItem->isIdEqualTo($item->id)) {
                    $orderItem->qty=$qty;
                }
            }
            $item->order->items=$items;

            $this->repository->save($item->order);
            return $item->order;
        }
        dump($this->guardCanChangeItem($itemOrItemId,$qty,true));exit;
        return null;


    }
    public function addItem($entityOrId, OrderItemForm $orderItemForm):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanAddItem($entity);
        $entity->changeItem($orderItemForm->carType_id,$orderItemForm->qty);
        $this->repository->save($entity);
    }

    /**
     * @param Order|int $entityOrId
     * @param int $itemId
     * @return void
     */
    public function removeItem($entityOrId, int $itemId)
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanRemoveItem($entity);
        $entity->removeItem($itemId);
        if (empty($entity->items)) {
            $entity->date_begin_reserve=null;
            $entity->onNew();
        }
        $this->repository->save($entity);
    }
    public function removeSlot($entityOrId,int $slotId)
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanRemoveSlot($entity);
        foreach ($entity->items as $item) {
            if ($item->slot->isIdEqualTo($slotId)) {
                $this->removeItem($entity,$item->id);
            }
        }
        $this->repository->save($entity);
    }

    public function remove($entityOrId): void
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanRemove($entity);
        $entity->onDeleted();
        $this->repository->save($entity);
    }
    public function removeHard($entityOrId): void
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanRemoveHard($entity);
        $entity->delete();
    }
    public function clear(?int $unixTime=null)
    {
        $this->guardCanClear();
        if ($unixTime) {
            $entities=$this->repository->findSlotsByDay($unixTime);
        } else {
            $entities=$this->repository->findAll();
        }

        foreach ($entities as $entity) {
            if ($entity->hasReserved()) {
                throw new \DomainException('Нельзя очистить слоты имеются брони на этот день: '.date('d.m.Y',$entity->date));
            } else {
                $this->removeHard($entity);
            }
        }
    }

    /**
     * Оформление заказа
     * @param Order $order
     * @param CustomerForm $customerOrder
     * @return void
     */
    public function checkout(Order $order, CustomerForm $customerOrder)
    {
        $this->guardCanCheckout($order);
        if (!$customer=$this->userRepository->findByTelephone($customerOrder->telephone)) {
            $customer=User::createCustomer(
                $customerOrder->name,
                $customerOrder->telephone,
                $customerOrder->email,
                $customerOrder->surname,
            );
        }
        $this->transaction->wrap(function () use ($order, $customer) {
            $this->userRepository->save($customer);
            $order->setCustomer($customer);
            $order->onCheckout();
            $this->repository->save($order);
            //очищаем куки
        });


        //отправляем в АмоЦРМ
        if ($credential=$this->credentialRepository->find(Credential::MAIN_ID)) {
            $this->amoCRMService->setCredential($credential);

            if ($order->items) {
                $racers=[];
                foreach ($order->items as $item) {
                    if (!array_key_exists($item->slot_id, $racers)) {
                        $racers[$item->slot_id]=[
                            'cars'=>[],
                            'qty'=>0,
                            'total'=>0,
                            'slot'=>$item->slot
                        ];
                    }
                    if (!array_key_exists($item->carType_id, $racers[$item->slot_id]['cars'])) {
                        $racers[$item->slot_id]['cars'][$item->carType_id]['qty']=0;
                        $racers[$item->slot_id]['cars'][$item->carType_id]['carType']=$item->carType;
                    }
                    $racers[$item->slot_id]['cars'][$item->carType_id]['qty']+=$item->qty;
                    $racers[$item->slot_id]['qty']+=$item->qty;
                    $racers[$item->slot_id]['total']+=$item->total;
                }

                foreach ($racers as $racer) {
                    try {
                        $amocrm_leadId=$this->amoCRMService->addLead(
                            new LeadPipeline7665106([
                                'title'=>$order->getName().', заезд: '. $racer['slot']->getName(),
                                'contact_name' => $order->customer->name,
                                'contact_secondName' => $order->customer->surname,
                                'contact_lastName' => $order->customer->patronymic,
                                'contact_telephone' => $order->customer->telephone,
                                'contact_email' => $order->customer->email,
                                'budget' => $racer['total'],
                                'notes' => ['Заказ на '. count($racers) . ' заезда'],
                                'dateTimeSlot'=>$racer['slot']->getBeginUT(),
                                'typeSlot'=>$racer['slot']->type,
                                'qty'=>$racer['qty'],
                                'cars'=>$racer['cars']
                            ])
                        );
                        $order->setAmoCRMLeadId($amocrm_leadId,$racer['slot']->id);
                        $this->repository->save($order);
                    } catch (AmoCRMApiErrorResponseException  $exception) {
                        dump($exception->getMessage());
                        dump($exception->getLastRequestInfo());
                        dump($exception->getTraceAsString());
                        dump($exception->getDescription());
                    }
                }
            }
            $order->onSentAmoCRM();
            $this->repository->save($order);
        }

    }

    /**
     * Сохранение дополнительной информации по заезду(имен гонщиков, вес и т.д.)
     * Предыдущая информация удаляется
     * @param Order $order
     * @param RacersForm $racersForm
     * @return void
     */
    public function addAdditionalInfo(Order $order, RacersForm $racersForm)
    {
        $this->guardCanAddAdditionalInfo($order);
        $order->additionalInfo=[];
        foreach ($racersForm->items as $item) {
            if (
                $item->name AND
                (
                    $item->weight OR
                    $item->height OR
                    $item->birthday
                )
            ) {
                if (!array_key_exists($item->slot_id,$order->additionalInfo)) {
                    $order->additionalInfo[$item->slot_id]=[];
                }

                $order->additionalInfo[$item->slot_id][]=[
                    'name' => $item->name,
                    'weight' => $item->weight,
                    'height' => $item->height,
                    'birthday' => $item->birthday,
                ];
            }
        }

        $order->onSavedAdditionInfo();
        $this->repository->save($order);

        //обновляем данные в амоцрм по гонщикам
        if ($credential=$this->credentialRepository->find(Credential::MAIN_ID)) {
            $this->amoCRMService->setCredential($credential);
            $allContacts = [];
            $currentSlotId = null;
            foreach ($order->items as $item) {
                if ($item->slot_id == $currentSlotId) continue;
                $currentSlotId = $item->slot_id;

                $amocrm_leadId = $item->amocrm_lead_id;

                if (array_key_exists($currentSlotId, $order->additionalInfo)) {
                    if ($order->additionalInfo[$currentSlotId]) {
                        foreach ($order->additionalInfo[$currentSlotId] as $item) {
                            if (!array_key_exists($amocrm_leadId, $allContacts)) {
                                $allContacts[$amocrm_leadId] = [];
                            }
                            $allContacts[$amocrm_leadId][] = $item;
                        }
                    }
                }
            }
            foreach ($allContacts as $amocrm_leadId => $contacts) {
                $this->amoCRMService->addContacts($amocrm_leadId, $contacts);
            }
        }

    }
    public function checkLicense(LicenseForm $form,?Order $order=null):bool
    {
        $this->guardCanCheckLicense();
        if ($license=$this->licenseRepository->findByNumber($form->number)) {
            if ($order) {
                $order->customer_id=$license->user_id;
                $this->repository->save($order);
            }
            return true;
        }
        return false;
    }

    /**
     * Удаляем заказы со статусом NEW, которые редактировались больше чем $seconds. По умолчанию 3 дня
     * @return void
     */
    public function clearOrders(?int $seconds=60*60*24*3):int
    {
        $orders=Order::find()
            ->where(['status'=>Order::STATUS_NEW])
            ->andWhere(['<=','updated_at',(time()-$seconds)])
            ->all();
        $count=0;
        foreach ($orders as $order) {
            $this->removeHard($order);
            $count++;
        }
        return $count;
    }

    /**
     * Проверяем заказы, на время бронирования и оплаты, если время закончилось тогда убираем бронь и статус ставим NEW
     * @return int
     */
    public function checkOrders():int
    {
        $orders=Order::find()
            ->where(['status'=>[Order::STATUS_CHECKOUT,Order::STATUS_RESERVATION_PROCESS]])
            ->all();
        $count=0;
        /** @var Order $order */
        foreach ($orders as $order) {
            $order->checkStatusReservationProcess();
            $order->checkStatusCheckout();
            $count++;
        }
        return $count;
    }
###guards
    public static function guardCanView($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно создавать
     * @return bool
     */
    public static function guardCanCreate(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }

    /**
     * Можно редактировать
     *
     * @param Order     |int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
//        return \Yii::$app->user->can('admin');
        return false;
    }

    /**
     * Можно удалять
     * @param Order|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param Order|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        if (!AppHelper::isConsole()) {
            return \Yii::$app->user->can('admin');
        }
        return true;

    }
    public static function guardCanClear(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }
    public static function guardCanRemoveItem($entityOrId)
    {
        return true;
    }
    public static function guardCanRemoveSlot($entityOrId)
    {
        return true;
    }
    /**
     * Можем ли мы добавить в заказ
     * - Заказ статус Новый или В процессес наполнения
     * - Итоговое кол-во не превышает разрешенное в заезде
     * - Есть доступные машины на этот заезд
     *
     * @param Order $itemOrItemId
     * @param int $newQty
     * @param bool $return
     * @return void
     */
    public static function guardCanAddItem($entityOrId, bool $return=false):bool
    {

        if (!$entityOrId->isNew() and !$entityOrId->isReservationProcess()) {
            if ($return) {
                return false;
            }
            throw new \DomainException('Ошибка! Заказ на этапе обработки. Запрет редактирования.');
        }
        return true;
    }

    /**
     * Можем ли мы менять кол-во в позиции. Условия:
     * - Заказ статус Новый
     * - Итоговое кол-во не превышает разрешенное в заезде
     * - Есть доступные машины на этот заезд
     *
     * @param OrderItem $itemOrItemId
     * @param int $newQty
     * @param bool $return
     * @return void
     */
    public static function guardCanChangeItem(OrderItem $item, int $newQty, bool $return=false):bool
    {
        if (!$item->order->isNew() and !$item->order->isReservationProcess()) {
            if ($return) {
                return false;
            }
            throw new \DomainException('Ошибка! Заказ на этапе обработки. Запрет редактирования.');
        }

        $free=$item->slot->getFree();
        if (($newQty) > ($free + $item->qty) ) {
            if ($return) {
                return false;
            }
            throw new \DomainException('Ошибка! Достигнут максимальное кол-во машин в заезде.');
        }
        return true;
    }
    public static function guardCanCheckout(Order $order, bool $return=false):bool
    {
        return true;
    }
    private function guardCanAddAdditionalInfo(Order $order, bool $return=false):bool
    {
        if (!$order->isSentAmoCRM()) {
            if ($return) return true;
            throw new \DomainException('Ошибка! Нельзя сохранять, уже сохранено');
        }
        return true;
    }
    private function guardCanCheckLicense(bool $return=false)
    {
        return true;
    }
### private



}