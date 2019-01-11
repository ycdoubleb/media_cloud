<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use backend\modules\operation_admin\searchs\OrderSearch;
use common\models\order\Order;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', 'Order');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
        'userMap' => $userMap
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
                    'attribute' => 'order_name',
                    'label' => Yii::t('app', '{Order}{Name}', [
                        'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                    ]),
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
                    'label' => Yii::t('app', '{Order}{Status}', [
                        'Order' => Yii::t('app', 'Order'), 'Status' => Yii::t('app', 'Status')
                    ]),
                    'value' => function($model){
                        return Order::$orderStatusName[$model->order_status];
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
                    'attribute' => 'goods_num',
                    'label' => Yii::t('app', '{Goods}{Num}', [
                        'Goods' => Yii::t('app', 'Goods'), 'Num' => Yii::t('app', 'Num')
                    ]),
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
                    'label' => Yii::t('app', '{Payable}{Amount}', [
                        'Payable' => Yii::t('app', 'Payable'), 'Amount' => Yii::t('app', 'Amount')
                    ]),
                    'value' => function($model){
                        return Yii::$app->formatter->asCurrency($model->order_amount);
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
                        return !empty($model->play_code) ? Order::$playCodeMode[$model->play_code] : null;
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
                    'label' => Yii::t('app', '{Payment}{Time}', [
                        'Payment' => Yii::t('app', 'Payment'), 'Time' => Yii::t('app', 'Time')
                    ]),
                    'value' => function($model){
                        return $model->play_at > 0 ? date('Y-m-d H:i', $model->play_at) : null;
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
                    'label' => Yii::t('app', '{Confirm}{Time}', [
                        'Confirm' => Yii::t('app', 'Confirm'), 'Time' => Yii::t('app', 'Time')
                    ]),
                    'value' => function($model){
                        return $model->confirm_at > 0 ? date('Y-m-d H:i', $model->confirm_at) : null;
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
                        'view' => function($url, $model){
                            return Html::a(Yii::t('app', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
                        },
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '66px',
                            'padding' => '8px 4px'
                        ],
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px',
                        ],
                    ],

                    'template' => '{view}',
                ],
            ],
        ]); ?>
    
    </div>

</div>

<?php
$js = <<<JS
        
    // 导出
    $('#btn-export').click(function(e){
        e.preventDefault();
        var val = [],
            checkBoxs = $('input[name="selection[]"]'), 
            url = $(this).attr("href");
        // 循环组装媒体id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("请选择需要导出的订单");
        }
    });    
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>