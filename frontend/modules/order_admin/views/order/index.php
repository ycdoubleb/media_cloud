<?php

use common\models\order\Order;
use common\models\order\searchs\OrderSearch;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

ModuleAssets::register($this);

$this->title = Yii::t('app', 'Order');
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
                'tableOptions' => ['class' => 'table table-bordered mc-table'],
                'layout' => "{items}\n{pager}\n{summary}",
                'columns' => [
                    [
                        'attribute' => 'order_sn',
                        'label' => Yii::t('app', 'Order Sn'),
                        'headerOptions' => [
                            'style' => [
                                'width' => '160px',
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'order_name',
                        'label' => Yii::t('app', '{Order}{Name}', [
                            'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '164px',
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'order_status',
                        'label' => Yii::t('app', '{Order}{Status}', [
                            'Order' => Yii::t('app', 'Order'), 'Status' => Yii::t('app', 'Status')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '70px',
                            ],
                        ],
                        'value' => function ($data) {
                            return Order::$orderStatusName[$data['order_status']];
                        }
                    ],
                    [
                        'attribute' => 'order_amount',
                        'label' => Yii::t('app', '{Order}{Amount}', [
                            'Order' => Yii::t('app', 'Order'), 'Amount' => Yii::t('app', 'Amount')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '80px',
                            ],
                        ],
                        'value' => function($data) {
                            return '￥'. $data['order_amount'];
                        }
                    ],
                    [
                        'attribute' => 'goods_num',
                        'label' => Yii::t('app', '{Resources}{Total}', [
                            'Resources' => Yii::t('app', 'Resources'), 'Total' => Yii::t('app', 'Total')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '70px',
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('app', 'Order Time'),
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
                        'header' => Yii::t('app', 'Operation'),
                        'template' => '{view}{place-order}{confirm}{delete}',
                        'headerOptions' => ['style' => 'width: 335px'],
                        'contentOptions' => ['style' => 'text-align: left'],
                        'buttons' => [
                            'view' => function ($url, $data, $key) {
                                $options = [
                                   'class' => 'btn btn-default btn-flat',
                                   'style' => '',
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
                                   'target' => '_blank'
                               ];
                               $buttonHtml = [
                                   'name' => '立即付款',
                                   'url' => ['cart/place-order', 'id' => $data['id']],
                                   'options' => $options,
                                   'symbol' => '&nbsp;',
                                   'conditions' => $data['order_status'] == 0,
                                   'adminOptions' => true,
                               ];
                               return $buttonHtml['conditions'] ? Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ' : '';
                            },
                            'confirm' => function ($url, $data, $key) {
                                $options = [
                                   'class' => 'btn btn-primary btn-flat',
                                   'style' => '',
                                   'title' => Yii::t('app', 'Confirm'),
                                   'aria-label' => Yii::t('app', 'Confirm'),
                                   'data-pjax' => '0',
                                   'target' => '_blank'
                               ];
                               $buttonHtml = [
                                   'name' => '确认开通',
                                   'url' => ['confirm', 'id' => $data['id']],
                                   'options' => $options,
                                   'symbol' => '&nbsp;',
                                   'conditions' => $data['play_status'] == 10,
                                   'adminOptions' => true,
                               ];
                               return $buttonHtml['conditions'] ? Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ' : '';
                            },
                            'delete' => function ($url, $data, $key) {
                                $options = [
                                   'class' => 'btn btn-highlight btn-flat',
                                   'style' => '',
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
                                   'conditions' => $data['order_status'] == 0 || $data['order_status'] == 6,
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
        
JS;
$this->registerJs($js, View::POS_READY);
?>