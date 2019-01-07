<?php

use common\models\media\searchs\WatermarkSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel WatermarkSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Watermark');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="watermark-index">

    <p>
        <?= Html::a(Yii::t('app', '{Create}{Watermark}', [
            'Create' => Yii::t('app', 'Create'), 'Watermark' => Yii::t('app', 'Watermark')
        ]), ['create'], ['id' => 'btn-addWatermark', 'class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
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
                'label' => Yii::t('app', '{Level}{Shifting}', [
                    'Level' => Yii::t('app', 'Level'), 'Shifting' => Yii::t('app', 'Shifting')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'dy',
                'label' => Yii::t('app', '{Vertical}{Shifting}', [
                    'Vertical' => Yii::t('app', 'Vertical'), 'Shifting' => Yii::t('app', 'Shifting')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'refer_pos',
                'label' => Yii::t('app', '{Watermark}{Position}', [
                    'Watermark' => Yii::t('app', 'Watermark'), 'Position' => Yii::t('app', 'Position')
                ]),
                'value' => function($model){
                    return CustomerWatermark::$referPosMap[$model->refer_pos];
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '85px',
                    ],
                ],
            ],
            [
                'attribute' => 'is_selected',
                'label' => Yii::t('app', '{Default}{Selected}', [
                    'Default' => Yii::t('app', 'Default'), 'Selected' => Yii::t('app', 'Selected')
                ]),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'labels' => ['否', '是'],
                    'values' => [0, 1],
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '70px',
                    ],
                ],
            ],
            [
                'attribute' => 'is_del',
                'label' => Yii::t('app', 'Status'),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'labels' => ['停用', '启用'],
                    'values' => [1, 0],
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
                    'updata' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateWatermark', 'class' => 'btn btn-default'
                        ]);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->name}】{Type}", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Type' => Yii::t('app', 'Type')
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
                            
                'template' => '{updata}{delete}',
            ],
        ],
    ]); ?>
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出媒体类型面板
    $('#btn-addWatermark, #btn-updateWatermark').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    //$this->registerJs($js, View::POS_READY);
?>