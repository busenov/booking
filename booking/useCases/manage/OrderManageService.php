<?php

namespace booking\useCases\manage;

use booking\entities\Order\Order;
use booking\entities\Order\OrderItem;
use booking\entities\Schedule\Schedule;
use booking\entities\Slot\Slot;
use booking\entities\User\User;
use booking\forms\manage\Order\OrderCreateForm;
use booking\forms\manage\Order\OrderEditForm;
use booking\forms\manage\Order\OrderItemForm;
use booking\forms\manage\Slot\SlotForm;
use booking\helpers\DateHelper;
use booking\repositories\CarTypeRepository;
use booking\repositories\OrderRepository;
use booking\repositories\ScheduleRepository;
use booking\repositories\SlotRepository;
use booking\repositories\UserRepository;
use http\Exception\RuntimeException;

class OrderManageService
{
    private OrderRepository $repository;
    private UserRepository $userRepository;

    public function __construct(
        OrderRepository $repository,
        UserRepository $userRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function create(OrderCreateForm $form): Order
    {
        $this->guardCanCreate();
        $entity = Order::create(
            (int)$form->slot_id,
            (int)$form->status,
            (int)$form->isChild,
            $form->note,
        );

        #customer
        if (!($customer=$this->userRepository->findByTelephone($form->customer->telephone))) {
            $customer=User::createCustomer(
                $form->customer->name,
                $form->customer->telephone,
            );
        }
        $entity->setCustomer($customer);
        #items
        if ($form->items) {
            foreach ($form->items as $item) {
                if ($item->qty) {
                    $entity->addItem($item->cartTypeId,intval($item->qty));
                }
            }
        }


        $this->repository->save($entity);

        return $entity;
    }

    public function edit($entityOrId, OrderEditForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            (int)$form->status,
            (int)$form->isChild,
            (int)$form->note,
        );
        #customer
        if (!($customer=$this->userRepository->findByTelephone($form->customer->telephone))) {
            $customer=User::createCustomer(
                $form->customer->name,
                $form->customer->telephone,
            );
        }
        $entity->setCustomer($customer);
        #items
        foreach ($form->items as $item) {
            if ($item->_orderItem) {
                $entity->editItem($item->_orderItem->id,$item->qty);
            } else {
                $entity->addItem($item->cartTypeId,$item->qty);
            }
        }
        $this->repository->save($entity);
    }
    public function addItem($entityOrId, OrderItemForm $orderItemForm):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanAddItem($entity);
        $entity->addItem($orderItemForm->cartTypeId,$orderItemForm->qty);
        $this->repository->save($entity);
    }
    public function removeItem($entityOrId,$itemId)
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanRemoveItem($entity);
        $entity->removeItem($itemId);
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
        return \Yii::$app->user->can('admin');
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
        return \Yii::$app->user->can('admin');
    }
    public static function guardCanClear(bool $return=false):bool
    {
        return \Yii::$app->user->can('admin');
    }

    private function guardCanAddItem($entityOrId)
    {
        return true;
    }
    private function guardCanRemoveItem($entityOrId)
    {
        return true;
    }
### private



}