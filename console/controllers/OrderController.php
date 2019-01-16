<?php

namespace console\controllers;

use common\models\order\Order;
use common\models\order\OrderAction;
use common\models\order\PlayApprove;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;
use yii\db\Query;

/**
 * 订单定时任务
 *
 * @author Administrator
 */
class OrderController extends Controller{
    /**
     * 检查订单是否在限期内完成支付
     * 
     * 如果订单没有在限期内完成支付即设置该订单【作废】
     */
    public function actionCheckPlay(){
        //
        //do something
        //
        $orders = (new Query())
                ->select(['id', 'order_status', 'created_at'])
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['or',
                    ['order_status' => Order::ORDER_STATUS_READING_PAYING], //待付款
                    ['order_status' => Order::ORDER_STATUS_AUDIT_FAILURE],  //审核失败
                ])->all();
        $invalidIds = [];
        // 循环拿出所有需要作废的订单ID
        foreach ($orders as $order) {
            if(strtotime("+7 day", $order['created_at']) <= time()){
                $invalidIds[] += $order['id'];
            }
        }
        try {
            Yii::$app->db->createCommand()->update(Order::tableName(), [
                'order_status' => Order::ORDER_STATUS_INVALID, 'updated_at' => time()], ['in', 'id', $invalidIds])->execute();
            $models = Order::findAll(['id' => $invalidIds]);
            // 保存订单操作记录
            foreach ($models as $model){
                OrderAction::savaOrderAction($model->id, '作废订单', '系统定时操作', $model->order_status, $model->play_status);
            }
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        
        return ExitCode::OK;
    }
    
    
    /**
     * 检查订单是否确认
     * 
     * 如果订单在【支付审核通过】7天后还没有【确认开通】即自动设置该订单状态为【确认开通】
     */
    public function actionCheckConfirm(){
        //
        //do something
        //
        $orders = (new Query())
                ->select(['Order.id', 'Order.order_status', 'PlayApprove.handled_at AS handled_at'])
                ->from(['Order' => Order::tableName()])
                ->where(['order_status' => Order::ORDER_STATUS_TO_BE_CONFIRMED])    //待确认
                ->leftJoin(['PlayApprove' => PlayApprove::tableName()], '(PlayApprove.order_id = Order.id AND PlayApprove.result = 1)')
                ->all();
        $confirmIds = [];
        // 循环拿出所有需要确认的订单ID
        foreach ($orders as $order) {
            if(strtotime("+7 day", $order['handled_at']) <= time()){
                $confirmIds[] += $order['id'];
            }       
        }
        try {
            Yii::$app->db->createCommand()->update(Order::tableName(), ['order_status' => Order::ORDER_STATUS_CONFIRMED,
                'confirm_at' => time(), 'updated_at' => time()], ['in', 'id', $confirmIds])->execute();
            $models = Order::findAll(['id' => $confirmIds]);
            // 保存订单操作记录
            foreach ($models as $model){
                OrderAction::savaOrderAction($model->id, '确认订单', '系统定时操作', $model->order_status, $model->play_status);
            }
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        
        return ExitCode::OK;
    }
}
