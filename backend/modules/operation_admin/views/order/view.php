<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use common\models\order\Order;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Order */

YiiAsset::register($this);
OperationModuleAsset::register($this);

$this->title = Yii::t('app', "{Order}{Detail}：{$model->order_name}", [
    'Order' => Yii::t('app', 'Order'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="order-view">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
        <li role="presentation" class="">
            <a href="#tags" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">线下支付</a>
        </li>
        <li role="presentation" class="">
            <a href="#video" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">资源列表</a>
        </li>
        <li role="presentation" class="">
            <a href="#action" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">操作记录</a>
        </li>
    </ul>
    
    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
    
            <?= DetailView::widget([
                'model' => $model,
                'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                'attributes' => [
                    [
                        'label' => Yii::t('app', '{Order}{Name}', [
                            'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'value' => $model->order_name
                    ],
                    'order_sn',
                    [
                        'label' => Yii::t('app', '{Order}{Status}', [
                            'Order' => Yii::t('app', 'Order'), 'Status' => Yii::t('app', 'Status')
                        ]),
                        'value' => Order::$orderStatusName[$model->order_status]
                    ],
                    [
                        'label' => Yii::t('app', '{Goods}{Num}', [
                            'Goods' => Yii::t('app', 'Goods'), 'Num' => Yii::t('app', 'Num')
                        ]),
                        'value' => $model->goods_num
                    ],
                    [
                        'label' => Yii::t('app', '{Goods}{Total Price}', [
                            'Goods' => Yii::t('app', 'Goods'), 'Total Price' => Yii::t('app', 'Total Price')
                        ]),
                        'value' => Yii::$app->formatter->asCurrency($model->goods_amount)
                    ],
                    [
                        'label' => Yii::t('app', '{Payable}{Amount}', [
                            'Payable' => Yii::t('app', 'Payable'), 'Amount' => Yii::t('app', 'Amount')
                        ]),
                        'value' => Yii::$app->formatter->asCurrency($model->order_amount)
                    ],
                    [
                        'label' => Yii::t('app', 'Purchaser'),
                        'value' => !empty($model->created_by) ? $model->createdBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', '{Payment}{Status}', [
                            'Payment' => Yii::t('app', 'Payment'), 'Status' => Yii::t('app', 'Status')
                        ]),
                        'value' => Order::$playStatusName[$model->play_status]
                    ],
                    [
                        'label' => Yii::t('app', '{Payment}{Mode}', [
                            'Payment' => Yii::t('app', 'Payment'), 'Mode' => Yii::t('app', 'Mode')
                        ]),
                        'value' => !empty($model->play_code) ? Order::$playCodeMode[$model->play_code] : null
                    ],
                    [
                        'label' => Yii::t('app', 'Order Time'),
                        'value' => $model->created_at > 0 ? date('Y-m-d H:i', $model->created_at) : null
                    ],
                    [
                        'label' => Yii::t('app', '{Payment}{Time}', [
                            'Payment' => Yii::t('app', 'Payment'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => $model->play_at > 0 ? date('Y-m-d H:i', $model->play_at) : null
                    ],
                    [
                        'label' => Yii::t('app', '{Confirm}{Time}', [
                            'Confirm' => Yii::t('app', 'Confirm'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => $model->confirm_at > 0 ? date('Y-m-d H:i', $model->confirm_at) : null
                    ],                   
                    'user_note',
                ],
            ]) ?>
        
        </div>
        
    </div>

</div>
