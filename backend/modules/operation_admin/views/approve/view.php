<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use common\models\order\PlayApprove;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model PlayApprove */

YiiAsset::register($this);
OperationModuleAsset::register($this);

$this->title = Yii::t('app', "{Order}{Auditing}{Detail}：{$model->order->order_name}", [
    'Order' => Yii::t('app', 'Order'), 'Auditing' => Yii::t('app', 'Approve'),
    'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Order}{Auditing}', [
    'Order' => Yii::t('app', 'Order'), 'Auditing' => Yii::t('app', 'Auditing')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="play-approve-view">

    <p>
        
        <?php if(!$model->status && !$model->result): ?>
        
        <?= Html::a(Yii::t('app', 'Pass'), ['pass-approve', 'id' => $model->id], [
            'id' => 'btn-passApprove', 'class' => 'btn btn-primary btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to do this?'),
                'method' => 'post'
            ],
        ]); ?>
        <?= ' ' . Html::a(Yii::t('app', '{No}{Pass}', [
            'No' => Yii::t('app', 'No'), 'Pass' => Yii::t('app', 'Pass')
        ]), ['not-approve', 'id' => $model->id], [
            'id' => 'btn-notApprove', 'class' => 'btn btn-danger btn-flat'
        ]); ?>
        
        <span class="text-danger">（请务必检查凭证金额与订单应付金额是否一致！）</span>
        
        <?php endif; ?>
        
    </p>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
        <li role="presentation" class="">
            <a href="#certificate" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">支付凭证</a>
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
                        'value' => !empty($model->order_id) ? $model->order->order_name : null
                    ],
                    [
                        'label' => Yii::t('app', 'Order Sn'),
                        'value' => !empty($model->order_id) ? $model->order->order_sn : null
                    ],
                    [
                        'label' => Yii::t('app', '{Payable}{Amount}', [
                            'Payable' => Yii::t('app', 'Payable'), 'Amount' => Yii::t('app', 'Amount')
                        ]),
                        'value' => !empty($model->order_id) ? Yii::$app->formatter->asCurrency($model->order->order_amount) : null
                    ],
                    [
                        'label' => Yii::t('app', 'Purchaser'),
                        'value' => !empty($model->created_by) ? $model->createdBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', '{Payment}{Time}', [
                            'Payment' => Yii::t('app', 'Payment'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => !empty($model->order_id) && $model->order->play_at > 0 ? date('Y-m-d H:i', $model->order->play_at) : null
                    ],
                    [
                        'attribute' => 'content',
                        'label' => Yii::t('app', '{Payment}{Illustration}', [
                            'Payment' => Yii::t('app', 'Payment'), 'Illustration' => Yii::t('app', 'Illustration')
                        ]),
                    ],
                    [
                        'label' => Yii::t('app', '{Auditing}{Result}', [
                            'Auditing' => Yii::t('app', 'Auditing'), 'Result' => Yii::t('app', 'Result')
                        ]),
                        'value' => $model->status == 1 ? PlayApprove::$resultName[$model->result] : null
                    ],
                    [
                        'label' => Yii::t('app', 'Verifier'),
                        'value' => !empty($model->handled_by) ? $model->handledBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', '{Auditing}{Time}', [
                            'Auditing' => Yii::t('app', 'Auditing'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => !empty($model->handled_at) ? date('Y-m-d H:i', $model->handled_at) : null
                    ],
                    [
                        'attribute' => 'feedback',
                        'label' => Yii::t('app', '{Feedback}{Info}', [
                            'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
                        ]),
                    ]
                ],
            ]) ?>
            
        </div>
        
        <!--支付凭证-->
        <div role="tabpanel" class="tab-pane fade" id="certificate" aria-labelledby="certificate-tab">
            
            <?= Html::img($model->certificate_url, ['width' => '100%']) ?>
            
        </div>
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
        
    // 弹出媒体编辑页面面板
    $('#btn-notApprove').click(function(e){
        e.preventDefault();
        showModal($(this));
    });    
   
JS;
    $this->registerJs($js,  View::POS_READY);
?>