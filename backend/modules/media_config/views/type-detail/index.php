<?php

use common\models\media\MediaType;
use common\models\media\searchs\MediaTypeDetailSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaTypeDetailSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{File}{Suffix}', [
    'Media' => Yii::t('app', 'Media'), 'File' => Yii::t('app', 'File'), 'Suffix' => Yii::t('app', 'Suffix')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-type-detail-index">

    <p>
        <?= Html::a(Yii::t('app', '{Create}{Suffix}', [
            'Create' => Yii::t('app', 'Create'), 'Suffix' => Yii::t('app', 'Suffix')
        ]), ['create'], ['id' => 'btn-addSuffix', 'class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                'label' => Yii::t('app', 'Suffix'),
                'filter' => '',
                'headerOptions' => [
                    'style' => [
                        'width' => '300px',
                    ],
                ],
            ],
            [
                'label' => Yii::t('app', 'Icon'),
                'format' => 'raw',
                'value' => function($model){
                    return Html::img($model->icon_url, ['width' => 68, 'height' => 47]);
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '300px',
                    ],
                ],
            ],
            [
                'label' => Yii::t('app', '{The}{Media}{Type}', [
                    'The' => Yii::t('app', 'The'), 'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
                ]),
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'type_id',
                    'data' => MediaType::getMediaByType(),
                    'hideSearch' => false,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'value' => function($model){
                    return $model->mediaType->name;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '300px',
                    ],
                ],
            ],
            [
                'attribute' => 'is_del',
                'label' => Yii::t('app', 'Is Use'),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'labels' => ['禁用', '启用'],
                    'values' => [1, 0],
                ],
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'is_del',
                    'data' => ['启用', '禁用'],
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ],
                ],
            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'updata' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateSuffix', 'class' => 'btn btn-default'
                        ]);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->name}】{Suffix}", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Suffix' => Yii::t('app', 'Suffix')
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
<?= $this->render('/layouts/____model'); ?>

<?php
$js = <<<JS
    
    // 弹出媒体类型面板
    $('#btn-addSuffix, #btn-updateSuffix').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>