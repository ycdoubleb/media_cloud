<?php

use common\models\order\Order;
use common\utils\I18NUitl;
use yii\widgets\DetailView;

/**
 * view 订单详情页的子页面
 * 订单基本信息页
 */

?>

<div class="panel-left">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table detail-view mc-table'],
        'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
        'attributes' => [
            [
                'attribute' => 'order_sn',
                'label' => I18NUitl::t('app', '{Orders Sn}')
            ],
            [
                'attribute' => 'goods_num',
                'label' => I18NUitl::t('app', '{Medias}{Total}')
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
        ],
    ]) ?>
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
        ],
    ]) ?>
</div>