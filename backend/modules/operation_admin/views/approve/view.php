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

$this->title = Yii::t('app', "{Approves}{Detail}", [
   'Approves' => Yii::t('app', 'Approves'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Approves}{List}', [
    'Approves' => Yii::t('app', 'Approves'), 'List' => Yii::t('app', 'List')
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
        <?= ' ' . Html::a(Yii::t('app', 'No Pass'), ['not-approve', 'id' => $model->id], [
            'id' => 'btn-notApprove', 'class' => 'btn btn-danger btn-flat'
        ]); ?>
        
        <span class="text-danger">（<?= Yii::t('app', 'Note: Please ensure that the voucher amount is consistent with the amount due to the order') ?>）</span>
        
        <?php endif; ?>
        
    </p>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">
                <?= Yii::t('app', 'Basic Info') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a href="#certificate" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', 'Payment Voucher') ?>
            </a>
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
                        'label' => Yii::t('app', '{Orders}{Name}', [
                            'Orders' => Yii::t('app', 'Orders'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'value' => !empty($model->order_id) ? $model->order->order_name : null
                    ],
                    [
                        'label' => Yii::t('app', 'Orders Sn'),
                        'value' => !empty($model->order_id) ? $model->order->order_sn : null
                    ],
                    [
                        'label' => Yii::t('app', 'Payable Amount'),
                        'value' => !empty($model->order_id) ? Yii::$app->formatter->asCurrency($model->order->order_amount) : null
                    ],
                    [
                        'label' => Yii::t('app', 'Purchaser'),
                        'value' => !empty($model->created_by) ? $model->createdBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', 'Payment Time'),
                        'value' => !empty($model->order_id) && $model->order->play_at > 0 ? date('Y-m-d H:i', $model->order->play_at) : null
                    ],
                    [
                        'attribute' => 'content',
                        'label' => Yii::t('app', 'Payment Description'),
                    ],
                    [
                        'label' => Yii::t('app', '{Approves}{Result}', [
                            'Approves' => Yii::t('app', 'Approves'), 'Result' => Yii::t('app', 'Result')
                        ]),
                        'value' => $model->status == 1 ? PlayApprove::$resultName[$model->result] : null
                    ],
                    [
                        'label' => Yii::t('app', 'Approver'),
                        'value' => !empty($model->handled_by) ? $model->handledBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', 'Approves Time'),
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
        
    // 弹出素材编辑页面面板
    $('#btn-notApprove').click(function(e){
        e.preventDefault();
        showModal($(this));
    });    
   
JS;
    $this->registerJs($js,  View::POS_READY);
?>