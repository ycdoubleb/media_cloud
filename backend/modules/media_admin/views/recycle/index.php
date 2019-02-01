<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\MediaRecycle;
use common\models\media\searchs\MediaRecycleSearh;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $searchModel MediaRecycleSearh */
/* @var $dataProvider ActiveDataProvider */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', 'Recycle Bin');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-recycle-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
        'userMap' => $userMap
    ]) ?>

    <div class="panel pull-left">
    
        <div class="title">
            <div class="btngroup pull-right">
                <?= Html::a(Yii::t('app', 'Recovery'), ['recovery'], [
                    'id' => 'btn-recovery', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', 'Delete'), ['delete'], [
                    'id' => 'btn-delete', 'class' => 'btn btn-danger btn-flat']); ?>
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
                            'width' => '195px',
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
                    'label' => Yii::t('app', 'Size'),
                    'value' => function($model){
                        return !empty($model->media_id) ? Yii::$app->formatter->asShortSize($model->media->size) : null;
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
                    'label' => Yii::t('app', 'Operator'),
                    'value' => function($model){
                        return !empty($model->media_id) ? $model->media->owner->nickname : null;
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
                    'label' => Yii::t('app', '{Delete}{Time}', [
                        'Delete' => Yii::t('app', 'Delete'), 'Time' => Yii::t('app', 'Time')
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
                    'label' => Yii::t('app', '{Handle}{Status}', [
                        'Handle' => Yii::t('app', 'Handle'), 'Status' => Yii::t('app', 'Status')
                    ]),
                    'value' => function($model){
                        return MediaRecycle::$statusMap[$model->status];
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
                    'label' => Yii::t('app', 'Handler'),
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
                    'label' => Yii::t('app', '{Handle}{Time}', [
                        'Handle' => Yii::t('app', 'Handle'), 'Time' => Yii::t('app', 'Time')
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
                    'label' => Yii::t('app', '{Handle}{Result}', [
                        'Handle' => Yii::t('app', 'Handle'), 'Result' => Yii::t('app', 'Result')
                    ]),
                    'value' => function($model){
                        return $model->status == 1 ? MediaRecycle::$resultMap[$model->result] : null;
                    },
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
$js = <<<JS
        
    // 还原 & 删除
    $('#btn-recovery, #btn-delete').click(function(e){
        e.preventDefault();
        var val = [],
            checkBoxs = $('input[name="selection[]"]'), 
            url = $(this).attr("href");
        // 循环组装id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        if(val.length > 0){
            if(confirm("确定执行该操作") == true){
                $(".myModal").html("");
                $('.myModal').modal("show").load(url + "?id=" + val);
            }
        }else{
            alert("请选择需要的id");
        }
    });    
                
JS;
    $this->registerJs($js,  View::POS_READY);
?>