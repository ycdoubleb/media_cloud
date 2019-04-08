<?php

use common\models\media\searchs\MediaAttributeValueSearch;
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
/* @var $searchModel MediaAttributeValueSearch */
/* @var $dataProvider ActiveDataProvider */

?>
<div class="media-attribute-value-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
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
                'label' => Yii::t('app', 'Is Del'),
                'class' => GridViewChangeSelfColumn::class,
                'plugOptions' => [
                    'url' => Url::to(['attribute-value/change-value'], true),
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '120px',
                    ],
                ],
            ],
//
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'buttons' => [
//                    'delete' => function ($url, $model){
//                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['attribute-value/delete', 'id' => $model->id, 'attribute_id' => $model->attribute_id], [
//                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
//                            'data' => [
//                                'pjax' => 0, 
//                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->value}】{Value}", [
//                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete'), 'Value' => Yii::t('app', 'Value')
//                                ]),
//                                'method' => 'post',
//                            ],
//                        ]);
//                    },
//                ],
//                'headerOptions' => [
//                    'style' => [
//                        'width' => '200px',
//                    ],
//                ],
//
//                'template' => '{delete}',
//            ],
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
