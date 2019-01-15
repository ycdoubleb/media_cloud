<?php

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;

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
        return ExitCode::OK;
    }
}
