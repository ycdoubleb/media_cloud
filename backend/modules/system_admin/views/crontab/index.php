<?php

use common\models\searchs\CrontabSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel CrontabSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Crontabs}{Task}', [
    'Crontabs' => Yii::t('app', 'Crontabs'), 'Task' => Yii::t('app', 'Task')
]);
$this->params['breadcrumbs'][] = Yii::t('app', '{Crontabs}{List}', [
    'Crontabs' => Yii::t('app', 'Crontabs'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="crontab-index">

    <p>

        <?= Html::a(Yii::t('app', '{Create}{Crontabs}', [
            'Create' => Yii::t('app', 'Create'), 'Crontabs' => Yii::t('app', 'Crontabs')
        ]), ['create'], ['class' => 'btn btn-success']) ?>
        
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ]
                ],
            ],
            'name',
            'route',
            'crontab_str',
            [
                'attribute' => 'last_rundate',
                'headerOptions' => [
                    'style' => [
                        'width' => '140px',
                    ]
                ],
            ],
            [
                'attribute' => 'next_rundate',
                'headerOptions' => [
                    'style' => [
                        'width' => '140px',
                    ]
                ],
            ],
            //'exec_memory',
            [
                'attribute' => 'exec_time',
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ]
                ],
            ],
            [
                'attribute' => 'status',
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ]
                ],
            ],
            [
                'attribute' => 'is_del',
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ]
                ],
            ],
            //'created_at',
            //'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => [
                    'style' => [
                        'width' => '100px',
                    ]
                ],
            ],
        ],
    ]);
    ?>
</div>
