<?php
namespace console\controllers;

use booking\access\Rbac;
use booking\entities\User\User;
use booking\forms\manage\User\UserCreateForm;
use booking\helpers\AppHelper;
use booking\useCases\manage\OrderManageService;
use booking\useCases\manage\UserManageService;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

/**
 * Управление пользователями (User manage)
 */
class OrderController extends Controller
{
    private OrderManageService $service;

    public function __construct($id, $module, OrderManageService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }


    /**
     * Удаляем заказы со статусом NEW, которые редактировались больше чем $second. По умолчанию 3 дня
     * @return void
     */
    public function actionClearOrders(?int $second=null):void
    {
        $count=$this->service->clearOrders($second);
        $this->stdout('Delete orders count:'. $count . PHP_EOL);
    }
    /**
     * Проверяем заказы, на время бронирования и оплаты, если время закончилось тогда убираем бронь и статус ставим NEW
     * @return void
     */
    public function actionCheckOrders():void
    {
        $count=$this->service->checkOrders();
        $this->stdout('Change status orders count:'. $count . PHP_EOL);
    }
###

}