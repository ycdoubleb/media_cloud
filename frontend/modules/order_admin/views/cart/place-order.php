<?php

use common\models\order\Order;
use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */

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
                        <span class="cloud-title">资源在线</span>
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
                        <span class="info-text">提交订单完成！现在马上去付款吧！</span>
                    </div>
                    <div class="pull-right">
                        <?php
                        echo Html::a('立即付款', ['payment-method', 'id' => $model->id], [
                            'class' => 'btn btn-highlight btn-flat', 'title' => '立即付款',
                            'onclick' => 'showModal($(this).attr("href"));return false;'
                        ]) . '&nbsp;&nbsp;';
                        echo Html::a('查看订单', ['view', 'id' => $model->id], [
                            'target' => '_black',
                            'class' => 'btn btn-default btn-flat', 'title' => '查看订单'
                        ]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--订单信息-->
    <div class="container content">
        <div class="checking-order common">
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
                            ],
                            [
                                'attribute' => 'goods_num',
                                'label' => Yii::t('app', '{Resources}{Total}',[
                                    'Resources' => Yii::t('app', 'Resources'),
                                    'Total' => Yii::t('app', 'Total'),
                                ])
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => Yii::t('app', 'Order Time'),
                                'value' => function($model){
                                    return !empty($model->created_at) ? date('Y-m-d H:i', $model->created_at) : null;
                                }
                            ],
                            [
                                'attribute' => 'user_note',
                                'format' => 'raw',
                                'label' => Yii::t('app', '{User}{Message}',[
                                    'User' => Yii::t('app', 'User'),
                                    'Message' => Yii::t('app', 'Message'),
                                ]),
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
                                'label' => Yii::t('app', '{Order}{Status}',[
                                    'Order' => Yii::t('app', 'Order'),
                                    'Status' => Yii::t('app', 'Status'),
                                ]),
                                'value' => function ($data) {
                                    return Order::$orderStatusName[$data['order_status']];
                                }
                            ],
                            [
                                'attribute' => 'order_amount',
                                'label' => Yii::t('app', '{Order}{Amount}',[
                                    'Order' => Yii::t('app', 'Order'),
                                    'Amount' => Yii::t('app', 'Amount'),
                                ]),
                                'value' => function($data) {
                                    return '￥'. $data['order_amount'];
                                }
                            ],
                            [
                                'attribute' => 'play_code',
                                'label' => Yii::t('app', '{Payment}{Mode}',[
                                    'Payment' => Yii::t('app', 'Payment'),
                                    'Mode' => Yii::t('app', 'Mode'),
                                ]),
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