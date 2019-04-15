<?php

use common\models\media\Media;
use common\widgets\grid\GridViewChangeSelfColumn;
use common\widgets\tagsinput\TagsInputAsset;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;


/* @var $this View */
TagsInputAsset::register($this);

?>

<?=GridView::widget([
    'id' => 'grid-view',
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
            'attribute' => 'id',
            'label' => Yii::t('app', 'Number'),
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
            'label' => Yii::t('app', 'Thumb Img'),
            'format' => 'raw',
            'value' => function($model) use($iconMap){
                if($model->cover_url != null){
                    $cover_url = $model->cover_url;
                }else if(isset($iconMap[$model->ext])){
                    $cover_url = $iconMap[$model->ext];
                }else{
                    $cover_url = '/';
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
            'class' => GridViewChangeSelfColumn::class,
            'plugOptions' => [
                'type' => 'input',
                'url' => Url::to(['change-value'], true),
            ],
            'headerOptions' => [
                'style' => [
                    'width' => '175px',
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
            'label' => Yii::t('app', 'Storage Dir'),
            'value' => function($model){
                return !empty($model->dir_id) ? $model->dir->getFullPath() : null;
            },
            'headerOptions' => [
                'style' => [
                    'width' => '125px',
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
                    'width' => '50px',
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
                    'width' => '65px',
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
                    'width' => '100px',
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
            'attribute' => 'price',
            'label' => Yii::t('app', 'Price'),
            'value' => function($model){
                return Yii::$app->formatter->asCurrency($model->price);
            },
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
            'attribute' => 'mts_status',
            'label' => Yii::t('app', 'Mts Status'),
            'value' => function($model){
                return Media::$mtsStatusName[$model->mts_status];
            },
            'headerOptions' => [
                'style' => [
                    'width' => '65px',
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
            'label' => Yii::t('app', 'Operator'),
            'value' => function($model){
                return !empty($model->owner_id) ? $model->owner->nickname : null;
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
            'attribute' => 'created_at',
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
            'attribute' => 'tags',
            'label' => Yii::t('app', 'Tags'),
            'class' => GridViewChangeSelfColumn::class,
            'plugOptions' => [
                'type' => 'input',
                'url' => Url::to(['change-tags'], true),
            ],
            'inputOptions' => [
                'data-role' => 'tagsinput',
            ],
            'headerOptions' => [
                'style' => [
                    'width' => '175px',
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
                    return Html::a(Yii::t('app', 'View'), ['view', 'id' => $model->id, 'category_id' => $model->category_id], [
                        'class' => 'btn btn-default', 'target' => '_blank'
                    ]);
                },
            ],
            'headerOptions' => [
                'style' => [
                    'width' => '65px',
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
$js = <<<JS
    
    $("input[data-role=tagsinput]").tagsinput();
        
    $('#grid-view table tbody tr').each(function(){
        $(this).click(function(e){
            e = window.event || e;
            var obj = e.srcElement || e.target;
            if($(obj).is('td')){
                if($(this).children(":first").children('input[name="selection[]"]').is(':checked')){
                    $(this).children(":first").children('input[name="selection[]"]').prop("checked",false);
                }else{
                    $(this).children(":first").children('input[name="selection[]"]').prop("checked",true);
                }
            }
        });
    });
        
JS;
    $this->registerJs($js,  View::POS_READY);
?>
