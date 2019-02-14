<?php

use common\models\media\searchs\MediaTypeSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaTypeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{Type}', [
    'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-type-index">
   
    <p>
        <?= Html::a(Yii::t('app', '{Create}{Type}', [
            'Create' => Yii::t('app', 'Create'), 'Type' => Yii::t('app', 'Type')
        ]), ['create'], ['id' => 'btn-addType', 'class' => 'btn btn-success']) ?>
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
                'label' => Yii::t('app', '{Media}{Type}{Name}', [
                    'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type'), 'Name' => Yii::t('app', 'Name')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '300px',
                    ],
                ],
            ],
            [
                'label' => Yii::t('app', '{Include}{Suffix}{Name}', [
                    'Include' => Yii::t('app', 'Include'), 'Suffix' => Yii::t('app', 'Suffix'), 'Name' => Yii::t('app', 'Name')
                ]),
                'value' => function($model){
                    return implode(',', ArrayHelper::getColumn($model->typeDetails, 'name'));
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '700px',
                    ],
                ],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'updata' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateType', 'class' => 'btn btn-default'
                        ]);
                    },
                    'view' => function($url, $model){
                        return ' '. Html::a(Yii::t('app', '{Config}{Suffix}', [
                            'Config' => Yii::t('app', 'Config'), 'Suffix' => Yii::t('app', 'Suffix')
                        ]), ['type-detail/index', 'MediaTypeDetailSearch' => ['type_id' => $model->id]], ['class' => 'btn btn-primary']);
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
                        'width' => '225px',
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
    
    // 弹出媒体类型面板
    $('#btn-addType, #btn-updateType').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>