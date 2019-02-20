<?php

use common\models\order\Order;
use frontend\modules\order_admin\assets\ModuleAssets;
use kartik\growl\GrowlAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Order */

ModuleAssets::register($this);
GrowlAsset::register($this);

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
                // 待付款或审核失败
                if($model->order_status == 0 || $model->order_status == Order::ORDER_STATUS_AUDIT_FAILURE){
                    echo Html::a('立即付款', ['cart/payment-method', 'id' => $model->id], [
                        'class' => 'btn btn-highlight btn-flat-lg', 
                        'onclick' => 'showModal($(this).attr("href"));return false;'
                    ]).' ';
                    echo Html::a('取消订单', ['delete', 'id' => $model->id], ['class' => 'btn btn-highlight btn-flat-lg', 'target' => '_blank']);     
                }
                // 审核通过
                if($model->order_status == Order::ORDER_STATUS_TO_BE_CONFIRMED){
                    echo Html::a('确认开通', 'javascript:;', ['id' => 'submit', 'class' => 'btn btn-primary btn-flat-lg']); 
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
        <div class="success-payment progress-block <?= ($model->order_status == Order::ORDER_STATUS_TO_BE_CONFIRMED 
                    || $model->order_status == Order::ORDER_STATUS_CONFIRMED)? 'active' : '';?>">
            付款成功<br>
            <?= ($model->order_status == Order::ORDER_STATUS_TO_BE_CONFIRMED 
                    || $model->order_status == Order::ORDER_STATUS_CONFIRMED) ? date('Y-m-d H:i', $model->play_at) : '';?>
        </div>
        <!--箭头 付款成功后变绿-->
        <img src="/imgs/site/seg_<?= ($model->order_status == Order::ORDER_STATUS_TO_BE_CONFIRMED 
                    || $model->order_status == Order::ORDER_STATUS_CONFIRMED) ? 'green' : 'default';?>.png">
        <!--确认开通后变绿-->
        <div class="complete progress-block <?= $model->order_status == Order::ORDER_STATUS_CONFIRMED ? 'active' : '';?>">
            完成<br>
            <?= $model->order_status == Order::ORDER_STATUS_CONFIRMED ? date('Y-m-d H:i', $model->confirm_at) : '';?>
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
                        <?= Html::a('媒体列表', array_merge(['view'], array_merge($filter, ['tabs' => 'resources']))) ?>
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
                        echo $this->render('____mediaslist', [
                            'model' => $model,
                            'resourcesData' => $resourcesData,
                        ]);
                    } 
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$order_id = $model->id;   //订单ID

$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
       
    //确认开通
    $("#submit").click(function(){
        $.post("confirm?id=$order_id", function(rel){
            window.location.reload();  //刷新页面
        });
    });
        
    //点击复制视频地址
    var btns = document.querySelectorAll('a');
    var clipboard = new ClipboardJS(btns);
    clipboard.on('success', function(e) {
        $.notify({
            message: '复制成功',
        },{
            type: "success",
            delay: 3000,
        });
    });
    clipboard.on('error', function(e) {
        $.notify({
            message: '复制失败',
        },{
            type: "danger",
            delay: 3000,
        });
    });
JS;
$this->registerJs($js, View::POS_READY);
?>