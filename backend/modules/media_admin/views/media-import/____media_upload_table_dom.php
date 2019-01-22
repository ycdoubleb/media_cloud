<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

?>
<div class="media-upload_table">
  
    <div class="title">媒体列表：</div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{summary}\n{pager}",  
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => [
                        'width' => '50px',
                    ],
                ],
            ],

            [
                'label' => Yii::t('app', '{Media}{Name}', [
                    'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
                ]),
                'value' => function($model){
                    return $model['name'];
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
                'label' => Yii::t('app', 'Thumb Image'),
                'format' => 'raw',
                'value' => function($model){
                    $ext = substr($model['url'], strrpos($model['url'], '.')+1);
                    $cover_url = str_replace($ext, 'jpg', $model['url']);
                    
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
                'label' => Yii::t('app', 'Path'),
                'value' => function($model){
                    return $model['url'];
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '176px',
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
                'label' => Yii::t('app', 'Duration'),
                'value' => function($model){
                    return $model['duration'];
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
                'label' => Yii::t('app', 'Size'),
                'value' => function($model){
                    return $model['size'];
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
                'label' => Yii::t('app', 'Tag'),
                'value' => function($model){
                    return $model['tags'];
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '176px',
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