<?php

use common\models\media\MediaAttribute;
use common\models\media\MediaCategory;
use common\models\media\searchs\MediaAttributeSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaAttributeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{Attribute}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-attribute-index">

    <p>
        <?= Html::a(Yii::t('app', '{Create}{Attribute}', [
            'Create' => Yii::t('app', 'Create'), 'Attribute' => Yii::t('app', 'Attribute')
        ]), ['create'], ['id' => 'btn-addAttr', 'class' => 'btn btn-success']) ?>
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
                'label' => Yii::t('app', '{Attribute}{Name}', [
                    'Attribute' => Yii::t('app', 'Attribute'), 'Name' => Yii::t('app', 'Name')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '200px',
                    ],
                ],
            ],
            [
                'label' => Yii::t('app', '{The}{Category}', [
                    'The' => Yii::t('app', 'The'), 'Category' => Yii::t('app', 'Category')
                ]),
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'category_id',
                    'data' => MediaCategory::getMediaCategory(),
                    'hideSearch' => false,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'value' => function($model){
                    return !empty($model->category_id) ? $model->category->name : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '168px',
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
                        'width' => '120px',
                    ],
                ],
            ],
            [
                'label' => Yii::t('app', '{Input}{Type}', [
                    'Input' => Yii::t('app', 'Input'), 'Type' => Yii::t('app', 'Type')
                ]),
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'input_type',
                    'data' => MediaAttribute::$inputTypeMap,
                    'hideSearch' => false,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'value' => function($model){
                    return MediaAttribute::$inputTypeMap[$model->input_type];
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '120px',
                    ],
                ],
            ],    
            [
                'attribute' => 'value_length',
                'filter' => '',
                'label' => Yii::t('app', '{Value}{Length}', [
                    'Value' => Yii::t('app', 'Value'), 'Length' => Yii::t('app', 'Length')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '96px',
                    ],
                ],
            ],     
            [
                'attribute' => 'is_required',
                'class' => GridViewChangeSelfColumn::class,
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'is_required',
                    'data' => ['是', '否'],
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '86px',
                    ],
                ],
            ],
            [
                'attribute' => 'index_type',
                'label' => Yii::t('app', 'Is Search'),
                'class' => GridViewChangeSelfColumn::class,
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'index_type',
                    'data' => ['是', '否'],
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '86px',
                    ],
                ],
            ],
            [
                'attribute' => 'sort_order',
                'filter' => '',
                'label' => Yii::t('app', 'Sort Order'),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'type' => 'input',
                ],
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
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'updata' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateAttr', 'class' => 'btn btn-default'
                        ]);
                    },
                    'view' => function($url, $model){
                        return ' '. Html::a(Yii::t('app', '{Config}{Value}', [
                            'Config' => Yii::t('app', 'Config'), 'Value' => Yii::t('app', 'Value')
                        ]), ['view', 'id' => $model->id], ['class' => 'btn btn-primary']);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->name}】{Attribute}", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Attribute' => Yii::t('app', 'Attribute')
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
                            
                'template' => '{updata}{view}{delete}',
            ],
        ],
    ]); ?>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出素材属性面板
    $('#btn-addAttr, #btn-updateAttr').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>