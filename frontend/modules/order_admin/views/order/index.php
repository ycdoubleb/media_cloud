<?php

use common\models\order\Order;
use common\models\order\searchs\OrderSearch;
use common\utils\I18NUitl;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

ModuleAssets::register($this);

$this->title = Yii::t('app', 'Orders');
?>
<div class="order-index main">
    <div class="mc-tabs">
        <ul class="list-unstyled">
            <li id="all">
                <?= Html::a('全部订单', array_merge(['index'], array_merge($filters, ['order_status' => '']))) ?>
            </li>
            <li id="payment">
                <?= Html::a('待付款', array_merge(['index'], array_merge($filters, ['order_status' => '0']))) ?>
            </li>
            <li id="audited">
                <?= Html::a('待审核', array_merge(['index'], array_merge($filters, ['order_status' => '5']))) ?>
            </li>
            <li id="confirmed">
                <?= Html::a('待确认', array_merge(['index'], array_merge($filters, ['order_status' => '10']))) ?>
            </li>
            <li id="completed">
                <?= Html::a('已完成', array_merge(['index'], array_merge($filters, ['order_status' => '11']))) ?>
            </li>
            <li id="cancelled">
                <?= Html::a('已取消', array_merge(['index'], array_merge($filters, ['order_status' => '15']))) ?>
            </li>
        </ul>
    </div>
    <?= $this->render('_search', [
        'searchModel' => $searchModel,
    ]); ?>
    <div class="mc-panel clear-margin">
        <div class="order-table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
                'layout' => "{items}\n{pager}\n{summary}",
                'columns' => [
                    [
                        'attribute' => 'order_sn',
                        'label' => Yii::t('app', 'Orders Sn'),
                        'headerOptions' => [
                            'style' => 'width: 130px',
                        ],
                    ],
                    [
                        'attribute' => 'order_name',
                        'label' => I18NUitl::t('app', '{Orders}{Name}'),
                        'headerOptions' => [
                            'style' => 'width: 190px',
                        ],
                        'format' => 'raw',
                        'value' => function ($data) {
                            return '<span class="multi-line-clamp" style="-webkit-line-clamp:3">' . $data['order_name'] . '</span>';
                        }
                    ],
                    [
                        'attribute' => 'order_status',
                        'label' => I18NUitl::t('app', '{Orders}{Status}'),
                        'headerOptions' => [
                            'style' => 'width: 70px',
                        ],
                        'value' => function ($data) {
                            return Order::$orderStatusName[$data['order_status']];
                        }
                    ],
                    [
                        'attribute' => 'order_amount',
                        'label' => I18NUitl::t('app', '{Orders}{Amount}'),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function($data) {
                            return '￥'. $data['order_amount'];
                        }
                    ],
                    [
                        'attribute' => 'goods_num',
                        'label' => I18NUitl::t('app', '{Medias}{Num}'),
                        'headerOptions' => [
                            'style' => 'width: 70px',
                        ],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('app', 'Place Order Time'),
                        'headerOptions' => [
                            'style' => 'width: 80px;',
                        ],
                        'value' => function ($data) {
                            return date('Y-m-d H:i', $data['created_at']); 
                        },
                        'contentOptions' => ['style' => 'font-size: 13px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}{place-order}{confirm}{delete}',
                        'headerOptions' => ['style' => 'width: 275px'],
                        'contentOptions' => ['style' => 'text-align: left'],
                        'buttons' => [
                            'view' => function ($url, $data, $key) {
                                $options = [
                                   'class' => 'btn btn-default btn-flat',
                                   'style' => 'margin-left:5px;',
                                   'title' => Yii::t('app', 'View'),
                                   'aria-label' => Yii::t('app', 'View'),
                                   'data-pjax' => '0',
                                   'target' => '_blank'
                               ];
                               $buttonHtml = [
                                   'name' => '查看详情',
                                   'url' => ['view', 'id' => $data['id']],
                                   'options' => $options,
                                   'symbol' => '&nbsp;',
                                   'conditions' => true,
                                   'adminOptions' => true,
                               ];
                               return Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ';
                            },
                            'place-order' => function ($url, $data, $key) {
                                $options = [
                                    'class' => 'btn btn-highlight btn-flat',
                                    'style' => '',
                                    'title' => Yii::t('app', 'Payment'),
                                    'aria-label' => Yii::t('app', 'Payment'),
                                    'data-pjax' => '0',
                                    'target' => '',
                                    'onclick' => 'showModal($(this).attr("href"));return false;'
                               ];
                               $buttonHtml = [
                                   'name' => '立即付款',
                                   'url' => ['cart/payment-method', 'id' => $data['id']],
                                   'options' => $options,
                                   'symbol' => '&nbsp;',
                                   'conditions' => $data['order_status'] == 0 || $data['order_status'] == Order::ORDER_STATUS_AUDIT_FAILURE,
                                   'adminOptions' => true,
                               ];
                               return $buttonHtml['conditions'] ? Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ' : '';
                            },
                            'confirm' => function ($url, $data, $key) {
                                $options = [
                                    'id' => 'submit',
                                    'data-url' => Url::to(['confirm?id='.$data['id']], true),
                                    'class' => 'btn btn-primary btn-flat',
                                    'style' => '',
                                    'title' => Yii::t('app', 'Confirm'),
                                    'aria-label' => Yii::t('app', 'Confirm'),
                                    'data-pjax' => '0',
                                    'target' => ''
                                ];
                                $buttonHtml = [
                                    'name' => '确认开通',
                                    'url' => 'javascript:;',
                                    'options' => $options,
                                    'symbol' => '&nbsp;',
                                    'conditions' => $data['order_status'] == Order::ORDER_STATUS_TO_BE_CONFIRMED,
                                    'adminOptions' => true,
                                ];
                                return $buttonHtml['conditions'] ? Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ' : '';
                            },
                            'delete' => function ($url, $data, $key) {
                                $options = [
                                    'class' => 'btn btn-highlight btn-flat',
                                    'style' => 'margin-right:0px;',
                                    'title' => Yii::t('app', 'Cancel'),
                                    'aria-label' => Yii::t('app', 'Cancel'),
                                    'data-pjax' => '0',
                                    'target' => ''
                                ];
                                $buttonHtml = [
                                    'name' => '取消订单',
                                    'url' => ['delete', 'id' => $data['id']],
                                    'options' => $options,
                                    'symbol' => '&nbsp;',
                                    'conditions' => $data['order_status'] == 0 || $data['order_status'] == Order::ORDER_STATUS_AUDIT_FAILURE,
                                    'adminOptions' => true,
                                ];
                                return $buttonHtml['conditions'] ? Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ' : '';
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<?php
// 过滤条件tabs
$order_status = ArrayHelper::getValue($filters, 'order_status', '');
switch ($order_status) {
    case '0' : $is_active = 'payment';
        break;
    case 5 : $is_active = 'audited';
        break;
    case 10 : $is_active = 'confirmed';
        break;
    case 11 : $is_active = 'completed';
        break;
    case 15 : $is_active = 'cancelled';
        break;
    default : $is_active = 'all';
        break;
}

$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$is_active]").addClass('active');
       
    //确认开通
    $("#submit").click(function(){
        var url = $(this).attr('data-url');
        $.post(url, function(rel){
            window.location.reload();  //刷新页面
        });
    });
JS;
$this->registerJs($js, View::POS_READY);
?>