<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use backend\modules\operation_admin\searchs\OrderGoodsSearch;
use common\models\order\Order;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderGoodsSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', 'Order Goods');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-goods-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
        'uploadedByMap' => $uploadedByMap,
        'createdByMap' => $createdByMap,
    ]) ?>

    <div class="panel pull-left">
        
        <div class="title">
            
            <div class="pull-right">
                <?= Html::a(Yii::t('app', 'Export'), ['export'], [
                    'id' => 'btn-export', 'class' => 'btn btn-primary btn-flat'
                ]) ?>
            </div>

        </div>
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'headerOptions' => [
                        'style' => [
                            'width' => '20px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],

                [
                    'attribute' => 'goods_id',
                    'label' => Yii::t('app', '{Media}{Number}', [
                        'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
                    ]),
                    'headerOptions' => [
                        'style' => [
                            'width' => '66px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                
                [
                    'label' => Yii::t('app', '{Media}{Name}', [
                        'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
                    ]),
                    'value' => function($model){
                        return !empty($model->goods_id) ? $model->media->name : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '180px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'Uploader'),
                    'value' => function($model){
                        return !empty($model->goods_id) ? $model->media->createdBy->nickname : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '70px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'attribute' => 'order_sn',
                    'label' => Yii::t('app', 'Order Sn'),
                    'headerOptions' => [
                        'style' => [
                            'width' => '150px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', '{Order}{Name}', [
                        'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                    ]),
                    'value' => function($model){
                        return !empty($model->order_id) ? $model->order->order_name : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '200px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', '{Income}{Amount}', [
                        'Income' => Yii::t('app', 'Income'), 'Amount' => Yii::t('app', 'Amount')
                    ]),
                    'value' => function($model){
                        return Yii::$app->formatter->asCurrency($model->amount);
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '90px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'Purchaser'),
                    'value' => function($model){
                        return !empty($model->created_by) ? $model->createdBy->nickname : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '70px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', '{Payment}{Mode}', [
                        'Payment' => Yii::t('app', 'Payment'), 'Mode' => Yii::t('app', 'Mode')
                    ]),
                    'value' => function($model){
                        return !empty($model->order_id) && !empty($model->order->play_code) ? Order::$playCodeMode[$model->order->play_code] : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '70px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'Order Time'),
                    'value' => function($model){
                        return $model->created_at > 0 ? date('Y-m-d H:i', $model->created_at) : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '70px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px',
                            'font-size' => '13px'
                        ],
                    ]
                ],            

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'buttons' => [
                        'media' => function($url, $model){
                            return Html::a('查看媒体', ['/media_admin/media/view', 'id' => $model->goods_id], ['class' => 'btn btn-default']);
                        },
                        'acl' => function($url, $model){
                            return ' '. Html::a('访问路径', ['acl/view', 'id' => $model->goods_id], ['class' => 'btn btn-default']);
                        },
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '160px',
                            'padding' => '8px 2px',
                        ],
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 2px',
                        ],
                    ],

                    'template' => '{media}{acl}',
                ],
            ],
        ]); ?>
        
    </div>
    
</div>
