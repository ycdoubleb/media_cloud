<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\MediaApprove;
use common\modules\rbac\components\Helper;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;


MediaModuleAsset::register($this);

/* @var $this View */
/* @var $searchModel MediaApproveSearh */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Medias}{Approves}', [
    'Medias' => Yii::t('app', 'Medias'), 'Approves' => Yii::t('app', 'Approves')
]);
$this->params['breadcrumbs'][] = Yii::t('app', '{Approves}{List}', [
    'Approves' => Yii::t('app', 'Approves'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="media-approve-index">  
    
    <?= $this->render('_search', [
        'model' => $searchModel,
        'filters' => $filters,
        'userMap' => $userMap
    ]) ?>

    <div class="panel pull-left">
    
        <div class="title">
            <div class="btngroup pull-right">
                <?php 
                    if( Helper::checkRoute(Url::to(['pass-approve']))){
                        echo Html::a(Yii::t('app', 'Pass'), ['pass-approve'], [
                            'id' => 'btn-passApprove', 'class' => 'btn btn-primary btn-flat']);
                    }
                    if( Helper::checkRoute(Url::to(['not-approve']))){
                        echo ' ' . Html::a(Yii::t('app', 'No Pass'), ['not-approve'], ['id' => 'btn-notApprove', 'class' => 'btn btn-danger btn-flat']);
                    }
                ?>
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
                    'attribute' => 'media_id',
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
                        return !empty($model->media_id) ? $model->media->name : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '190px',
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
                    'label' => Yii::t('app', '{Medias}{Type}', [
                        'Medias' => Yii::t('app', 'Medias'), 'Type' => Yii::t('app', 'Type')
                    ]),
                    'value' => function($model){
                        return !empty($model->media_id) ? $model->media->mediaType->name : null;
                    },
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
                    'label' => Yii::t('app', '{Approves}{Type}', [
                        'Approves' => Yii::t('app', 'Approves'), 'Type' => Yii::t('app', 'Type')
                    ]),
                    'value' => function($model){
                        return MediaApprove::$typeMap[$model->type];
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '86px',
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
                    'label' => Yii::t('app', 'Applicant'),
                    'value' => function($model){
                        return !empty($model->created_by) ? $model->createdBy->nickname : null;
                    },
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
                    'label' => Yii::t('app', 'Apply Time'),
                    'value' => function($model){
                        return date('Y-m-d H:i', $model->created_at);
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '76px',
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
                    'attribute' => 'content',
                    'label' => Yii::t('app', 'Reasons For Apply'),
                    'headerOptions' => [
                        'style' => [
                            'width' => '160px',
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
                    'label' => Yii::t('app', '{Approves}{Status}', [
                        'Approves' => Yii::t('app', 'Approves'), 'Status' => Yii::t('app', 'Status')
                    ]),
                    'value' => function($model){
                        return MediaApprove::$statusMap[$model->status];
                    },
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
                    'label' => Yii::t('app', '{Approves}{Result}', [
                        'Approves' => Yii::t('app', 'Approves'), 'Result' => Yii::t('app', 'Result')
                    ]),
                    'value' => function($model){
                        return $model->status == 1 ? MediaApprove::$resultMap[$model->result] : null;
                    },
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
                    'label' => Yii::t('app', 'Approver'),
                    'value' => function($model){
                        return !empty($model->handled_by) ? $model->handledBy->nickname : null;
                    },
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
                    'label' => Yii::t('app', '{Approves}{Time}', [
                        'Approves' => Yii::t('app', 'Approves'), 'Time' => Yii::t('app', 'Time')
                    ]),
                    'value' => function($model){
                        return !empty($model->handled_at) ? date('Y-m-d H:i', $model->handled_at) : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '76px',
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
                    'attribute' => 'feedback',
                    'label' => Yii::t('app', '{Feedback}{Info}', [
                        'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
                    ]),
                    'headerOptions' => [
                        'style' => [
                            'width' => '160px',
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
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Operate'),
                    'buttons' => [
                        'view' => function($url, $model){
                            return Html::a(Yii::t('app', '{View}{Medias}', [
                                'View' => Yii::t('app', 'View'), 'Medias' => Yii::t('app', 'Medias')
                            ]), ['media/view', 'id' => $model->media_id, 'category_id' => !empty($model->media_id) ? $model->media->category_id : null], [
                                'class' => 'btn btn-default', 'target' => '_blank'
                            ]);
                        },
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                            'padding' => '8px 4px'
                        ],
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ],

                    'template' => '{view}',
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

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$msg = Yii::t('app', 'Please select at least one.');    // 消息提示
$js = <<<JS
        
    // 弹出素材编辑页面面板
    $('#btn-passApprove, #btn-notApprove').click(function(e){
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