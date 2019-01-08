<?php

use common\models\order\Order;
use yii\widgets\DetailView;

?>
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
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model->createdBy->nickname;
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