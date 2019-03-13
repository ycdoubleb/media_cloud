<?php

use common\models\media\MediaType;
use common\models\media\searchs\MediaTypeDetailSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\LinkPager;

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
        'summaryOptions' => ['class' => 'hidden'],
        'pager' => [
            'options' => ['class' => 'hidden']
        ],
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
                'attribute' => 'name',
                'label' => Yii::t('app', 'Suffix'),
                'filter' => '',
                'headerOptions' => [
                    'style' => [
                        'width' => '150px',
                    ],
                ],
            ],
            [
                'attribute' => 'mime_type',
                'label' => Yii::t('app', '{Mime}{Type}', [
                    'Mime' => Yii::t('app', 'Mime'), 'Type' => Yii::t('app', 'Type')
                ]),
                'filter' => '',
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'type' => 'input',
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '250px',
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
                        'width' => '200px',
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
                        'width' => '200px',
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

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出素材类型面板
    $('#btn-addSuffix, #btn-updateSuffix').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>