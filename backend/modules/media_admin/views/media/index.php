<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\models\media\searchs\MediaSearch;
use common\utils\DateUtil;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaSearch */
/* @var $dataProvider ActiveDataProvider */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Media}{List}', [
    'Media' => Yii::t('app', 'Media'), 'List' => Yii::t('app', 'List')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">
  
    <?= $this->render('_search', [
        'model' => $searchModel,
        'filters' => $filters,
        'userMap' => $userMap,
        'attrMap' => $attrMap
    ]) ?>
    
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                <?= Html::a(Yii::t('app', '{Reset}{Tag}', [
                    'Reset' => Yii::t('app', 'Reset'), 'Tag' => Yii::t('app', 'Tag')
                ]), ['batch-edit-attribute'], ['id' => 'btn-editAttribute', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', '{Approve}{Into}{DB}', [
                    'Approve' => Yii::t('app', 'Approve'), 'Into' => Yii::t('app', 'Into'), 'DB' => Yii::t('app', 'DB')
                ]), ['approve/add-apply'], ['id' => 'btn-addApply', 'class' => 'btn btn-danger btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', '{Approve}{Delete}', [
                    'Approve' => Yii::t('app', 'Approve'), 'Delete' => Yii::t('app', 'Delete')
                ]), ['approve/del-apply'], ['id' => 'btn-delApply', 'class' => 'btn btn-danger btn-flat']); ?>
            </div>
            
        </div>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
    //        'filterModel' => $searchModel,
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
                    'label' => Yii::t('app', 'Thumb Image'),
                    'format' => 'raw',
                    'value' => function($model) use($iconMap){
                        if($model->cover_url != null){
                            $cover_url = $model->cover_url;
                        }else if(isset($iconMap[$model->ext])){
                            $cover_url = $iconMap[$model->ext];
                        }else{
                            $cover_url = '';
                        }
                        return Html::img($cover_url, ['width' => 87, 'height' => 74]);
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '96px',
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
                    'label' => Yii::t('app', 'Name'),
                    'headerOptions' => [
                        'style' => [
                            'width' => '146px',
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
                    'label' => Yii::t('app', '{Storage}{Dir}', [
                        'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
                    ]),
                    'value' => function($model){
                        return !empty($model->dir_id) ? $model->dir->getFullPath() : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '146px',
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
                    'label' => Yii::t('app', 'Type'),
                    'value' => function($model){
                        return !empty($model->type_id) ? $model->mediaType->name : null;
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
                    'attribute' => 'duration',
                    'label' => Yii::t('app', 'Duration'),
                    'value' => function($model){
                        return $model->duration > 0 ? DateUtil::intToTime($model->duration, ':', true) : null;
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '76px',
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
                    'attribute' => 'size',
                    'label' => Yii::t('app', 'Size'),
                    'value' => function($model){
                        return Yii::$app->formatter->asShortSize($model->size);
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
                    'attribute' => 'mts_status',
                    'label' => Yii::t('app', 'Mts Status'),
                    'value' => function($model){
                        return Media::$mtsStatusName[$model->mts_status];
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '76px',
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
                    'attribute' => 'status',
                    'label' => Yii::t('app', 'Status'),
                    'value' => function($model){
                        return Media::$statusName[$model->status];
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '76px',
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
                        return !empty($model->owner_id) ? $model->owner->nickname : null;
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
                    'label' => Yii::t('app', '{Upload}{Time}', [
                        'Upload' => Yii::t('app', 'Upload'), 'Time' => Yii::t('app', 'Time')
                    ]),
                    'value' => function($model){
                        return Date('Y-m-d H:i', $model->created_at);
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
                    'label' => Yii::t('app', 'Tag'),
                    'value' => function($model){
                        return implode(',', ArrayHelper::getColumn($model->mediaTagRefs, 'tags.name'));
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '196px',
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
                    'header' => '操作',
                    'buttons' => [
                        'view' => function($url, $model){
                            return Html::a(Yii::t('app', 'View'), ['view', 'id' => $model->id], [
                                'class' => 'btn btn-default', 'target' => '_blank'
                            ]);
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
                            'padding' => '8px 4px'
                        ],
                    ],

                    'template' => '{view}',
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
    $('#btn-addApply, #btn-delApply').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?media_id=" + val);
        }else{
            alert("请选择需要申请的媒体");
        }
    });
       
    // 出媒体编辑标签面板
    $('#btn-editAttribute').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("请选择需要申请的媒体");
        }
    }); 
        
    /**
     * 获取 getCheckBoxsValue
     * @returns {Array|getcheckBoxsValue.val}
     */
    function getCheckBoxsValue(){
        var val = [],
            checkBoxs = $('input[name="selection[]"]');
        // 循环组装媒体id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        
        return val
    }
    
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>