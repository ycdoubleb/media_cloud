<?php

use yii\grid\GridView;
use yii\helpers\Html;
?>

<?= GridView::widget([
    'dataProvider' => $actionDataProvider,
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
            'label' => Yii::t('app', '{Operate}{Type}', [
                'Operate' => Yii::t('app', 'Operate'), 'Type' => Yii::t('app', 'Type')
            ]),
            'value' => function($model) {
                return $model->title;
            },
            'headerOptions' => [
                'style' => [
                    'width' => '120px',
                ]
            ],
        ],
        [
            'label' => Yii::t('app', 'Created By'),
            'value' => function($model) {
                return !empty($model->created_by) ? $model->createdBy->nickname : null;
            },
            'headerOptions' => [
                'style' => [
                    'width' => '120px',
                ]
            ],
        ],
        [
            'label' => Yii::t('app', 'Content'),
            'format' => 'raw',
            'value' => function($model) {
                return $model->content;
            },
            'headerOptions' => [
                'style' => [
                    'width' => '750px',
                ]
            ],
        ],
        [
            'label' => Yii::t('app', '{Operate}{Time}', [
                'Operate' => Yii::t('app', 'Operate'), 'Time' => Yii::t('app', 'Time')
            ]),
            'value' => function($model) {
                return date('Y-m-d H:i', $model->created_at);
            },
            'headerOptions' => [
                'style' => [
                    'width' => '120px',
                ]
            ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'buttons' => [
                'view' => function($url, $model) {
                    return Html::a(Yii::t('app', 'View'), ['operatelog-view', 'id' => $model->id], [
                                'id' => 'btn-viewAction', 'class' => 'btn btn-default', 'onclick' => 'showModal($(this)); return false;'
                    ]);
                },
            ],
            'headerOptions' => [
                'style' => [
                    'width' => '80px',
                ],
            ],
            'template' => '{view}',
        ],
    ],
]);
?>