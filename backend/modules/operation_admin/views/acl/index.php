<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use backend\modules\operation_admin\searchs\AclSearch;
use common\models\media\Acl;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel AclSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', '{Visit}{Path}', [
    'Visit' => Yii::t('app', 'Visit'), 'Path' => Yii::t('app', 'Path')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="acl-index">

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
                <?= ' ' . Html::a(Yii::t('app', 'Refresh Cache'), ['refresh-cach'], [
                    'id' => 'btn-refreshCach', 'class' => 'btn btn-primary btn-flat'
                ]) ?>
                <?= ' ' . Html::a(Yii::t('app', '{Set}{Status}', [
                    'Set' => Yii::t('app', 'Set'), 'Status' => Yii::t('app', 'Status')
                ]), ['set-status'], [
                    'id' => 'btn-setStatus', 'class' => 'btn btn-danger btn-flat'
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
                    'attribute' => 'id',
                    'label' => Yii::t('app', '{Visit}{ID}', [
                        'Visit' => Yii::t('app', 'Visit'), 'ID' => Yii::t('app', 'ID')
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
                    'attribute' => 'name',
                    'label' => Yii::t('app', '{Visit}{Name}', [
                        'Visit' => Yii::t('app', 'Visit'), 'Name' => Yii::t('app', 'Name')
                    ]),
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
                    'label' => Yii::t('app', 'Status'),
                    'format' => 'raw',
                    'value' => function($model){
                        return $model->status ? '<span class="text-danger">'. Acl::$statusMap[$model->status] .'</span>' : Acl::$statusMap[$model->status];
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '60px',
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
                    'attribute' => 'visit_count',
                    'label' => Yii::t('app', 'Visit Count'),
                    'headerOptions' => [
                        'style' => [
                            'width' => '80px',
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
                    'attribute' => 'media_id',
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
                    'label' => Yii::t('app', 'Purchaser'),
                    'value' => function($model){
                        return !empty($model->user_id) ? $model->user->nickname : null;
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
                    'label' => Yii::t('app', '{Create}{Time}', [
                        'Create' => Yii::t('app', 'Create'), 'Time' => Yii::t('app', 'Time')
                    ]),
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
                        'preview' => function($url, $model){
                            return Html::a(Yii::t('app', 'Preview'), ['preview', 'id' => $model->id], [
                                'class' => 'btn btn-default', 'onclick' => 'showModal($(this)); return false;'
                            ]);
                        },
                        'media' => function($url, $model){
                            return ' ' . Html::a('查看媒体', ['/media_admin/media/view', 'id' => $model->media_id], ['class' => 'btn btn-default']);
                        },
                        'view' => function($url, $model){
                            return ' '. Html::a(Yii::t('app', 'Detail'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
                        },
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '210px',
                            'padding' => '8px 2px',
                        ],
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 2px',
                        ],
                    ],

                    'template' => '{preview}{media}{view}',
                ],
            ],
        ]); ?>
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
        
    // 导出 & 刷新
    $('#btn-export, #btn-refreshCach').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(),
            url = $(this).attr("href");
        if(val.length > 0){
            console.log(url + "?id=" + val);
        }else{
            alert("请选择需要的id");
        }
    });  
    
    // 设置状态
    $('#btn-setStatus').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(),
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("请选择需要的id");
        }
    });
        
    /**
     * 获取复选框的值, 并组装成数组
     * @returns {Array|getCheckBoxsValue.val}
     */
    function getCheckBoxsValue(){
        var val = [],
            checkBoxs = $('input[name="selection[]"]');
        // 循环组装id
        for(var i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        return val;
    }
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>