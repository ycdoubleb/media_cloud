<?php

use common\components\aliyuncs\Aliyun;
use common\models\order\searchs\OrderSearch;
use common\utils\DateUtil;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

ModuleAssets::register($this);

$this->title = Yii::t('app', 'Resources');

?>
<div class="resources-index main">
    <div class="mc-title">
        <span>我的资源</span>
    </div>
    <?= $this->render('_search', [
        'searchModel' => $searchModel,
        'filters' => $filters
    ]); ?>
    <div class="mc-panel clear-margin">
        <div class="order-table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-bordered mc-table'],
                'layout' => "{items}\n{pager}\n{summary}",
                'columns' => [
                    [
                        'attribute' => 'cover_url',
                        'label' => Yii::t('app', 'Preview'),
                        'format' => 'raw',
                        'value' => function($data){
                            return '<img src="'.Aliyun::absolutePath(!empty($data['cover_url']) ? 
                                        $data['cover_url'] : 'static/imgs/notfound.png').'" style="width: 70px"/>';
                        },
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                    ],
                    [
                        'attribute' => 'media_id',
                        'label' => Yii::t('app', 'Resources Sn'),
//                        'headerOptions' => [
//                            'style' => 'width: 100px',
//                        ],
                    ],
                    [
                        'attribute' => 'media_name',
                        'label' => Yii::t('app', '{Resources}{Name}',[
                            'Resources' => Yii::t('app', 'Resources'),
                            'Name' => Yii::t('app', 'Name')
                        ]),
                    ],
                    [
                        'attribute' => 'order_sn',
                        'label' => Yii::t('app', '{Order Sn}/{Name}',[
                            'Order Sn' => Yii::t('app', 'Order Sn'),
                            'Name' => Yii::t('app', 'Name')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '170px',
                            ],
                        ],
                        'format' => 'raw',
                        'value' => function($data){
                            return $data['order_sn'].'<br/>'.$data['order_name'];
                        }
                    ],
                    [
                        'attribute' => 'type_name',
                        'label' => Yii::t('app', 'Type'),
                        'headerOptions' => [
                            'style' => 'width: 50px',
                        ],
                    ],     
                    [
                        'attribute' => 'price',
                        'label' => Yii::t('app', '{Resources}{Price}',[
                            'Resources' => Yii::t('app', 'Resources'),
                            'Price' => Yii::t('app', 'Price')
                        ]),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function($data) {
                            return '￥'. $data['price'];
                        }
                    ],                    
//                    [
//                        'attribute' => 'order_amount',
//                        'label' => Yii::t('app', '{Order}{Amount}', [
//                            'Order' => Yii::t('app', 'Order'), 'Amount' => Yii::t('app', 'Amount')
//                        ]),
//                        'headerOptions' => [
//                            'style' => [
//                                'width' => '90px',
//                            ],
//                        ],
//                    ],
                    [
                        'attribute' => 'duration',
                        'label' => Yii::t('app', 'Duration'),
                        'headerOptions' => [
                            'style' => 'width: 70px',
                        ],
                        'value' => function($data) {
                            return $data['duration'] > 0 ? DateUtil::intToTime($data['duration'], ':', true) : null;
                        },
                    ],
                    [
                        'attribute' => 'size',
                        'label' => Yii::t('app', 'Size'),
                        'headerOptions' => [
                            'style' => 'width: 100px',
                        ],
                        'value' => function($data) {
                            return Yii::$app->formatter->asShortSize($data['size']);
                        },
                    ],            
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('app', '{Buy}{Time}',[
                            'Buy' => Yii::t('app', 'Buy'),
                            'Time' => Yii::t('app', 'Time')
                        ]),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function ($data) {
                           return date('Y-m-d H:i', $data['created_at']); 
                        },
                        'contentOptions' => ['style' => 'font-size: 13px'],
                    ],
                    [
                        'label' => Yii::t('app', '{Copy}{Route}', [
                            'Copy' => Yii::t('app', 'Copy'), 'Route' => Yii::t('app', 'Route')
                        ]),
                        'headerOptions' => [
                            'style' => [
                                'width' => '90px',
                            ],
                        ],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => Yii::t('app', 'Operation'),
                        'template' => '{view}',
                        'headerOptions' => ['style' => 'width: 65px'],
                        'buttons' => [
                            'view' => function ($url, $data, $key) {
                                $options = [
                                   'class' => '',
                                   'style' => 'color: #39f',
                                   'title' => Yii::t('app', 'View'),
                                   'aria-label' => Yii::t('app', 'View'),
                                   'data-pjax' => '0',
                                   'target' => '_blank'
                               ];
                               $buttonHtml = [
                                   'name' => '详情',
                                   'url' => ['/media_library/default/view', 'id' => $data['media_id']],
                                   'options' => $options,
                                   'symbol' => '&nbsp;',
                                   'conditions' => true,
                                   'adminOptions' => true,
                               ];
                               return Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ';
                           },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>