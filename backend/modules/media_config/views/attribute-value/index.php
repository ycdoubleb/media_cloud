<?php

use common\models\media\searchs\MediaAttributeValueSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaAttributeValueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{Attribute}{Value}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute'), 'Value' => Yii::t('app', 'Value')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-attribute-value-index">

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
                'attribute' => 'value',
                'label' => Yii::t('app', '{Value}{Name}', [
                    'Value' => Yii::t('app', 'Value'), 'Name' => Yii::t('app', 'Name')
                ]),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'type' => 'input',
                    'url' => Url::to(['attribute-value/change-value'], true),
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '880px',
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
                    'url' => Url::to(['attribute-value/change-value'], true),
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '120px',
                    ],
                ],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['attribute-value/delete', 'id' => $model->id, 'attribute_id' => $model->attribute_id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->value}】{Value}", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Value' => Yii::t('app', 'Value')
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

                'template' => '{delete}',
            ],
        ],
    ]); ?>
    
</div>
