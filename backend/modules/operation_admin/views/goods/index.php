<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use backend\modules\operation_admin\searchs\OrderGoodsSearch;
use common\models\order\Order;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $searchModel OrderGoodsSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', 'Medias Operation');
$this->params['breadcrumbs'][] = Yii::t('app', '{Goods}{List}', [
    'Goods' => Yii::t('app', 'Goods'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="order-goods-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
        'filters' => $filters,
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
            'layout' => "{items}\n{summary}\n{pager}",
            'summaryOptions' => ['class' => 'hidden'],
            'pager' => [
                'options' => ['class' => 'hidden']
            ],
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
                    'label' => Yii::t('app', '{Medias}{Number}', [
                        'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
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
                    'label' => Yii::t('app', '{Medias}{Name}', [
                        'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
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
//                [
//                    'label' => Yii::t('app', 'Uploader'),
//                    'value' => function($model){
//                        return !empty($model->goods_id) ? $model->media->createdBy->nickname : null;
//                    },
//                    'headerOptions' => [
//                        'style' => [
//                            'width' => '70px',
//                            'padding' => '8px 4px'
//                        ]
//                    ],
//                    'contentOptions' => [
//                        'style' => [
//                            'padding' => '8px 4px'
//                        ],
//                    ]
//                ],
                [
                    'attribute' => 'order_sn',
                    'label' => Yii::t('app', 'Orders Sn'),
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
//                [
//                    'label' => Yii::t('app', '{Orders}{Name}', [
//                        'Orders' => Yii::t('app', 'Orders'), 'Name' => Yii::t('app', 'Name')
//                    ]),
//                    'value' => function($model){
//                        return !empty($model->order_id) ? $model->order->order_name : null;
//                    },
//                    'headerOptions' => [
//                        'style' => [
//                            'width' => '200px',
//                            'padding' => '8px 4px'
//                        ]
//                    ],
//                    'contentOptions' => [
//                        'style' => [
//                            'padding' => '8px 4px'
//                        ],
//                    ]
//                ],
                [
                    'label' => Yii::t('app', 'Income Amount'),
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
                    'label' => Yii::t('app', 'Payment Mode'),
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
                    'label' => Yii::t('app', 'Place Order Time'),
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
                    'buttons' => [
                        'media' => function($url, $model){
                            return Html::a('查看素材', ['/media_admin/media/view', 'id' => $model->goods_id], ['class' => 'btn btn-default']);
                        },
                        'acl' => function($url, $model){
                            return ' '. Html::a('访问路径', ['acl/index', 
                                'AclSearch' => [
                                    'order_sn' => $model->order_sn, 'media_id' => $model->goods_id]
                                ], ['class' => 'btn btn-default']);
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
        
        <?php
            $page = ArrayHelper::getValue($filters, 'page', 1);
            $pageCount = ceil($totalCount / 10);
            if($pageCount >= 2){
                echo '<div class="summary">' . 
                        '第 <b>' . (($page * 10 - 10) + 1) . '</b>-<b>' . ($page != $pageCount ? $page * 10 : $totalCount) .'</b> 条，总共 <b>' . $totalCount . '</b> 条数据。' .
                    '</div>';
                
                echo LinkPager::widget([  
                    'pagination' => new Pagination([
                        'totalCount' => $totalCount,
                        'pageSize' => 10
                    ]),  
                    'maxButtonCount' => 5
                ]);
            }
        ?>
        
    </div>
    
</div>

<?php
$msg = Yii::t('app', 'Please select at least one.');    // 消息提示
$js = <<<JS
        
    // 导出
    $('#btn-export').click(function(e){
        e.preventDefault();
        var val = [],
            checkBoxs = $('input[name="selection[]"]'), 
            url = $(this).attr("href");
        // 循环组装素材id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("{$msg}");
        }
    });    
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>