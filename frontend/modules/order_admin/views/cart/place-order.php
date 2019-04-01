<?php

use common\models\order\Order;
use common\utils\I18NUitl;
use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* 下单成功页 */

$this->title = Yii::t('app', 'Checkout Success');

MainAssets::register($this);
ModuleAssets::register($this);

?>

<div class="order_admin mediacloud">
    <!--头部信息-->
    <div class="header checking-header place-order">
        <div class="container">
            <div class="media-top">
                <div class="pull-left">
                    <div class="cloud-name">
                        <span class="cloud-title">素材在线</span>
                        <span class="cloud-website">www.resonline.com</span>
                    </div>
                    <div class="cloud-cart">购物车</div>
                </div>
                <div class="pull-right bar-step">
                    <ul id="progressbar">
                        <li class="old-active">1、我的购物车</li>
                        <li class="old-active">2、填写核对订单信息</li>
                        <li class="active">3、成功提交订单</li>
                        <li></li>
                    </ul>
                </div>
            </div>
            <div class="information">
                <div class="use-purpose">
                    <div class="pull-left">
                        <span class="glyphicon glyphicon-ok-circle"></span>
                        <span class="info-text">提交订单完成！现在马上去付款吧！<span>（请在七天内完成付款噢）</span></span>
                    </div>
                    <div class="pull-right">
                        <?php
                        echo Html::a('立即付款', ['payment-method', 'id' => $model->id], [
                            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '立即付款',
                            'onclick' => 'showModal($(this).attr("href"));return false;'
                        ]) . '&nbsp;&nbsp;';
                        echo Html::a('查看订单', ['order/view', 'id' => $model->id], [
                            'target' => '_black',
                            'class' => 'btn btn-default btn-flat-lg', 'title' => '查看订单'
                        ]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--订单信息-->
    <div class="container content">
        <div class="checking-order order-success common">
            <div class="mc-tabs">
                <ul class="list-unstyled">
                    <li class="active">
                        <?= Html::a('订单信息', 'javascript:;', ['title' => '订单信息']);?>
                    </li>
                </ul>
            </div>
            <!--订单列表-->
            <div class="mc-panel set-bottom">
                <div class="panel-left">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table detail-view mc-table'],
                        'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                        'attributes' => [
                            [
                                'attribute' => 'order_sn',
                                'label' => Yii::t('app', 'Orders Sn')
                            ],
                            [
                                'attribute' => 'goods_num',
                                'label' => I18NUitl::t('app', '{Medias}{Num}')
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => Yii::t('app', 'Place Order Time'),
                                'value' => function($model){
                                    return !empty($model->created_at) ? date('Y-m-d H:i', $model->created_at) : null;
                                }
                            ],
                            [
                                'attribute' => 'user_note',
                                'format' => 'raw',
                                'label' => Yii::t('app', 'User Note'),
                                'contentOptions' => [
                                    'style' => 'word-break: break-all',
                                ]
                            ],
                        ]
                    ])?>
                </div>
                <div class="panel-right">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table detail-view mc-table'],
                        'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                        'attributes' => [
                            [
                                'attribute' => 'order_status',
                                'label' => I18NUitl::t('app', '{Orders}{Status}'),
                                'value' => function ($data) {
                                    return Order::$orderStatusName[$data['order_status']];
                                }
                            ],
                            [
                                'attribute' => 'order_amount',
                                'label' => I18NUitl::t('app', '{Orders}{Amount}'),
                                'value' => function($data) {
                                    return '￥'. $data['order_amount'];
                                }
                            ],
                            [
                                'attribute' => 'play_code',
                                'label' => Yii::t('app', 'Payment Mode'),
                                'value' => function($data) {
                                    return $data['play_code'] == 'eeplay' ? '线下支付' : '';
                                }
                            ],
                            [
                                'attribute' => 'confirm_at',
                                'value' => function($model){
                                    return !empty($model->confirm_at) ? date('Y-m-d H:i', $model->confirm_at) : '';
                                }
                            ],                        
                        ]
                    ])?>
                </div>
            </div>
        </div>
    </div>
</div>