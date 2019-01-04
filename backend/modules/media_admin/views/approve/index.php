<?php

use backend\modules\media_admin\assets\ModuleAsset;
use common\models\media\MediaApprove;
use common\models\media\searchs\MediaApproveSearh;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

ModuleAsset::register($this);

/* @var $this View */
/* @var $searchModel MediaApproveSearh */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{Approve}', [
    'Media' => Yii::t('app', 'Media'), 'Approve' => Yii::t('app', 'Approve')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-approve-index">  
    
    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>

    <div class="panel pull-left">
    
        <div class="title">
            <div class="btngroup pull-right">
                <?= Html::a(Yii::t('app', 'Pass'), ['update', 'result' => MediaApprove::RESULT_PASS_YES], [
                    'id' => 'btn-yesPass', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', '{No}{Pass}', [
                    'No' => Yii::t('app', 'No'), 'Pass' => Yii::t('app', 'Pass')
                ]), ['update', 'result' => MediaApprove::RESULT_PASS_NO], ['id' => 'btn-noPass', 'class' => 'btn btn-danger btn-flat']); ?>
            </div>
            
        </div>
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
        //        'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",  
            'columns' => [
                [
                    'header' => Html::checkbox('selectall'),
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::checkbox('stuCheckBox', null, ['value' => $model->id]);
                    },
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
                    'label' => Yii::t('app', '{Media}{Type}', [
                        'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
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
                    'label' => Yii::t('app', '{Auditing}{Type}', [
                        'Auditing' => Yii::t('app', 'Auditing'), 'Type' => Yii::t('app', 'Type')
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
                    'label' => Yii::t('app', '{Approve}{Time}', [
                        'Approve' => Yii::t('app', 'Approve'), 'Time' => Yii::t('app', 'Time')
                    ]),
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
                    'label' => Yii::t('app', '{Approve}{Illustration}', [
                        'Approve' => Yii::t('app', 'Approve'), 'Illustration' => Yii::t('app', 'Illustration')
                    ]),
                    'headerOptions' => [
                        'style' => [
                            'width' => '210px',
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
                    'label' => Yii::t('app', '{Auditing}{Status}', [
                        'Auditing' => Yii::t('app', 'Auditing'), 'Status' => Yii::t('app', 'Status')
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
                    'label' => Yii::t('app', '{Auditing}{Result}', [
                        'Auditing' => Yii::t('app', 'Auditing'), 'Result' => Yii::t('app', 'Result')
                    ]),
                    'value' => function($model){
                        return $model->result === 0 || $model->result === 1 ? 
                            MediaApprove::$resultMap[$model->result] : null;
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
                    'label' => Yii::t('app', 'Verifier'),
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
                    'label' => Yii::t('app', '{Auditing}{Time}', [
                        'Auditing' => Yii::t('app', 'Auditing'), 'Time' => Yii::t('app', 'Time')
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
                            'width' => '210px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
            ],
        ]); ?>
    
    </div>    
        
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
        
    // 弹出媒体编辑页面面板
    $('#btn-yesPass, #btn-noPass').click(function(e){
        e.preventDefault();
        var val = [],
            checkBoxs = $('input[name="stuCheckBox"]'), 
            url = $(this).attr("href");
        // 循环组装媒体id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "&id=" + val);
        }else{
            alert("请选择需要的审核");
        }
    });    
        
    // 单击全选或取消全选
    $('input[name="selectall"]').click(function(){
        if($(this).is(':checked')){
            $('input[name="stuCheckBox"]').each(function(){
                $(this).prop("checked",true);
            });
        }else{
            $('input[name="stuCheckBox"]').each(function(){
                $(this).prop("checked",false);
            });
        }
    });
JS;
    $this->registerJs($js,  View::POS_READY);
?>