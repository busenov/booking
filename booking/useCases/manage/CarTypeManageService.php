<?php

namespace booking\useCases\manage;

use booking\entities\Car\CarType;
use booking\entities\Car\Price;
use booking\forms\manage\Car\CarTypeForm;
use booking\forms\manage\Car\PricesForm;
use booking\repositories\CarTypeRepository;
use booking\repositories\UserRepository;
use booking\services\TransactionManager;

class CarTypeManageService
{
    private CarTypeRepository $repository;

    public function __construct(
        CarTypeRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function create(CarTypeForm $form): CarType
    {
        $this->guardCanCreate();
        $entity = CarType::create(
            $form->name,
            $form->description,
            (int)$form->status,
            (int)$form->qty,
            (double)$form->pwr,
            $form->note,
            $form->type
        );

        $this->repository->save($entity);

        return $entity;
    }

    public function edit($entityOrId, CarTypeForm $form):void
    {
        $entity=$this->repository->get($entityOrId);
        $this->guardCanEdit($entity);
        $entity->edit(
            $form->name,
            $form->description,
            (int)$form->status,
            (int)$form->qty,
            (double)$form->pwr,
            $form->note,
            $form->type
        );
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
###prices

    /**
     * Добавляем пустую цену
     * @param CarType|int $entityOrId
     * @return void
     */
    public function addEmptyPrice(CarType $entityOrId)
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanEditPrice($entity);
        $prices=$entity->prices;
        $prices[]=Price::create(0);
        $entity->prices=$prices;
        $this->repository->save($entity);
    }
    /**
     * Редактируем цены
     * @param CarType|int $entityOrId
     * @param PricesForm $form
     * @return void
     */
    public function editPrices($entityOrId, PricesForm $form)
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanEditPrice($entity);

        $prices=[];
        foreach ($form->prices as $price) {
            if (empty($price->_price)) {
                $prices[]=Price::create(doubleval($price->cost),intval($price->weekday),intval($price->date_from),intval($price->status),$price->note);
            } else {
                $price->_price->edit(doubleval($price->cost),intval($price->weekday),intval($price->date_from),intval($price->status),$price->note);
                $prices[]=$price->_price;
            }

        }
        $entity->prices=$prices;
        $this->repository->save($entity);
    }
    public function deletePrice($entityOrId,$price_id)
    {
        $entity = $this->repository->get($entityOrId);
        $this->guardCanEditPrice($entity);
        $prices=[];
        foreach ($entity->prices as $key=>$price) {
            if ($price->isIdEqualTo($price_id)) {
                unset($prices[$key]);
            }
        }
        $entity->prices=$prices;
        $this->repository->save($entity);
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
        return true;
    }

    /**
     * Можно редактировать
     *
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
       return true;
    }

    /**
     * Можно удалять
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemove($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно жестко удалять
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanRemoveHard($entityOrId, bool $return=false):bool
    {
        return true;
    }
    /**
     * Можно ли редактировать цены?
     * @param CarType|int $entityOrId
     * @param bool $return
     * @return bool
     */
    public static function guardCanEditPrice($entityOrId, bool $return=false):bool
    {
        return true;
    }

### private



}