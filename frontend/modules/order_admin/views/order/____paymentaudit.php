<?php

use common\components\aliyuncs\Aliyun;
use common\utils\I18NUitl;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\grid\GridView;

/**
 * view 订单详情页的子页面
 * 支付审核信息页
 */

ModuleAssets::register($this);

?>

<div class="play-approve-index">
    <?= GridView::widget([
        'dataProvider' => $auditingData,
        'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
        'layout' => "{items}\n{pager}\n{summary}",
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => 'width: 40px;',
                ],
            ],
            [
                'attribute' => 'certificate_url',
                'label' => Yii::t('app', 'Payment Voucher'),
                'format' => 'raw',
                'value' => function($data){
                    return '<img src="'.Aliyun::absolutePath(!empty($data['certificate_url']) ? 
                                $data['certificate_url'] : 'static/imgs/notfound.png').'" style="width:100px; height: 48px"/>';
                },
                'headerOptions' => [
                    'style' => 'width: 110px',
                ],
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Payment Time'),
                'headerOptions' => [
                    'style' => 'width: 100px',
                ],
                'value' => function ($data) {
                   return date('Y-m-d H:i', $data['created_at']); 
                },
            ],
            [
                'attribute' => 'content',
                'label' => Yii::t('app', 'Payment Description'),
                'format' => 'raw',
                'value' => function ($data) {
                    return '<span class="multi-line-clamp">' . $data['content'] . '</span>';
                },
            ],
//            [
//                'attribute' => 'status',
//                'label' => Yii::t('app', '{Auditing}{Status}',[
//                    'Auditing' => Yii::t('app', 'Auditing'),
//                    'Status' => Yii::t('app', 'Status')
//                ]),
//                'headerOptions' => [
//                    'style' => 'width: 90px',
//                ],
//                'value' => function ($data) {
//                    return PlayApprove::$statusName[$data['status']];
//                }
//            ],
            [
                'attribute' => 'result',
                'label' => I18NUitl::t('app', '{Approves}{Result}'),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
                'format' => 'raw',
                'value' => function ($data) {
                    return $data['status'] == 1 ? ($data['result'] == 0 ?
                        '<i class="glyphicon glyphicon-remove-sign" style="font-size: 24px; color: #fc583d;"></i>' :
                            '<i class="glyphicon glyphicon-ok-sign" style="font-size: 24px; color: #28b28b;"></i>') : '';
                },
            ],
            [
                'attribute' => 'handled_at',
                'label' => I18NUitl::t('app', '{Approves}{Time}'),
                'headerOptions' => [
                    'style' => 'width: 100px',
                ],
                'value' => function ($data) {
                   return empty($data['handled_at']) ? '' : date('Y-m-d H:i', $data['handled_at']); 
                },
            ],
            [
                'attribute' => 'feedback',
                'format' => 'raw',
                'value' => function ($data) {
                    return '<span class="multi-line-clamp">' . $data['feedback'] . '</span>';
                },
            ],            
        ],
    ]); ?>
</div>