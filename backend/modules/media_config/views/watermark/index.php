<?php

use common\models\media\searchs\WatermarkSearch;
use common\models\Watermark;
use common\widgets\grid\GridViewChangeSelfColumn;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $searchModel WatermarkSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Watermark');
$this->params['breadcrumbs'][] = Yii::t('app', '{Watermark}{List}', [
    'Watermark' => Yii::t('app', 'Watermark'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="watermark-index">

    <p>
        <?= Html::a(Yii::t('app', '{Create}{Watermark}', [
            'Create' => Yii::t('app', 'Create'), 'Watermark' => Yii::t('app', 'Watermark')
        ]), ['create'], ['id' => 'btn-addWatermark', 'class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{summary}\n{pager}",
        'summaryOptions' => ['class' => 'hidden'],
        'pager' => [
            'options' => ['class' => 'hidden']
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => [
                        'width' => '30px',
                    ],
                ],
            ],

            [
                'attribute' => 'name',
                'label' => Yii::t('app', '{Watermark}{Name}', [
                    'Watermark' => Yii::t('app', 'Watermark'), 'Name' => Yii::t('app', 'Name')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '170px',
                    ],
                ],
            ],
            [
                'attribute' => 'width',
                'label' => Yii::t('app', 'Width'),
                'headerOptions' => [
                    'style' => [
                        'width' => '65px',
                    ],
                ],
            ],
            [
                'attribute' => 'height',
                'label' => Yii::t('app', 'Height'),
                'headerOptions' => [
                    'style' => [
                        'width' => '65px',
                    ],
                ],
            ],
            [
                'attribute' => 'dx',
                'label' => Yii::t('app', 'Dx'),
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'dy',
                'label' => Yii::t('app', 'Dy'),
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'refer_pos',
                'label' => Yii::t('app', 'Refer Pos'),
                'value' => function($model){
                    return Watermark::$referPosMap[$model->refer_pos];
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'is_selected',
                'label' => Yii::t('app', 'Is Selected'),
                'class' => GridViewChangeSelfColumn::class,
                'headerOptions' => [
                    'style' => [
                        'width' => '70px',
                    ],
                ],
            ],
            [
                'attribute' => 'is_del',
                'label' => Yii::t('app', 'Is Del'),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'url' => Url::to(['enable'], true),
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '95px',
                    ],
                ],
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Created At'),
                'value' => function($model){
                    return date('Y-m-d H:i', $model->created_at);
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '110px',
                    ],
                ],
                'contentOptions' => [
                    'style' => [
                        'font-size' => '12px',
                    ],
                ],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'View'), ['view', 'id' => $model->id], [
                            'id' => 'btn-viewWatermark', 'class' => 'btn btn-default'
                        ]);
                    },
                    'updata' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateWatermark', 'class' => 'btn btn-primary'
                        ]);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                            'id' => 'btn-deleteWatermark', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->name}】{Watermark}", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Watermark' => Yii::t('app', 'Watermark')
                                ]),
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '200px',
                    ],
                ],
                            
                'template' => '{view}{updata}{delete}',
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