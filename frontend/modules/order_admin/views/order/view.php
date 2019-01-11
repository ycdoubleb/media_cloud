<?php

use common\models\order\Order;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Order */

ModuleAssets::register($this);

$this->title = Yii::t('app', '{Order}{Detail}', [
    'Order' => Yii::t('app', 'Order'),
    'Detail' => Yii::t('app', 'Detail'),
]);
$tabs = ArrayHelper::getValue(Yii::$app->request->queryParams, 'tabs', 'base');

?>
<div class="order-view main mediacloud">
    <!--头部信息 / 按钮-->
    <div class="mc-title">
        <span>
            <?= $this->title . ' > ' . '订单：' . $model->order_sn; ?>
        </span>
        <div class="btngroup pull-right">
            <?php 
                if($model->order_status == 0){
                    echo Html::a('立即付款', ['create'], ['class' => 'btn btn-highlight btn-flat', 'target' => '_blank']).' ';
                        
                    echo Html::a('取消订单', ['create'], ['class' => 'btn btn-highlight btn-flat', 'target' => '_blank']);     
                }
            ?>
        </div>
    </div>
    
    <!--订单进度-->
    <div class="order-progress">
        <!--提交订单-->
        <div class="place-order progress-block active">
            提交订单<br>
            <?= date('Y-m-d H:i', $model->created_at);?>
        </div>
        <!--箭头-->
        <img src="/imgs/site/seg_green.png">
        <!--付款成功后变绿-->
        <div class="success-payment progress-block <?= $model->play_status == 11 ? 'active' : '';?>">
            付款成功<br>
            <?= $model->play_status == 11 ? date('Y-m-d H:i', $model->play_at) : '';?>
        </div>
        <!--箭头 付款成功后变绿-->
        <img src="/imgs/site/seg_<?= $model->play_status == 11 ? 'green' : 'default';?>.png">
        <!--确认开通后变绿-->
        <div class="confirm-open progress-block <?= $model->order_status == 11 ? 'active' : '';?>">
            确认开通<br>
            <?= $model->order_status == 11 ? date('Y-m-d H:i', $model->confirm_at) : '';?>
        </div>
        <!--箭头 确认开通后变绿-->
        <img src="/imgs/site/seg_<?= $model->order_status == 11 ? 'green' : 'default';?>.png">
        <!--确认开通后变绿-->
        <div class="complete progress-block <?= $model->order_status == 11 ? 'active' : '';?>">
            完成<br>
            <?= $model->order_status == 11 ? date('Y-m-d H:i', $model->confirm_at) : '';?>
        </div>
    </div>
    
    <!--订单基本信息 / 审核状态 / 资源列表-->
    <div class="choice-panel">
        <div class="mc-panel clear-margin ">
            <div class="mc-tabs">
                <ul class="list-unstyled">
                    <li id="base">
                        <?= Html::a('基本信息', array_merge(['view'], array_merge($filter, ['tabs' => 'base']))) ?>
                    </li>
                    <li id="payment">
                        <?= Html::a('支付审核', array_merge(['view'], array_merge($filter, ['tabs' => 'payment']))) ?>
                    </li>
                    <li id="resources">
                        <?= Html::a('资源列表', array_merge(['view'], array_merge($filter, ['tabs' => 'resources']))) ?>
                    </li>
                </ul>
            </div>
            <div class="panel-content">
                <?php
                    if($tabs == 'base'){
                        echo $this->render('____baseinfo', [
                            'model' => $model,
                        ]);
                    } elseif ($tabs == 'payment') {
                        echo $this->render('____paymentaudit', [
                            'auditingData' => $auditingData,
                        ]);
                    } else {
                        echo $this->render('____resourceslist', [
                            'resourcesData' => $resourcesData,
                        ]);
                    } 
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
        
JS;
$this->registerJs($js, View::POS_READY);
?>