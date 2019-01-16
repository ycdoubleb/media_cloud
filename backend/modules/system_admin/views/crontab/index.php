<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\CrontabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Crontabs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>

        <?=
        Html::a(Yii::t('app', '{Create} {Crontab}', ['Create' => Yii::t('app', 'Create'), 'Crontab' => Yii::t('app', 'Crontab'),])
                , ['create'], ['class' => 'btn btn-success'])
        ?>
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
