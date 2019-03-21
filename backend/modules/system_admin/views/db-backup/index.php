<?php

use common\models\searchs\DbbackupSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel DbbackupSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Database Backups');
$this->params['breadcrumbs'][] = Yii::t('app', '{Backups}{List}', [
    'Backups' => Yii::t('app', 'Backups'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="db-backup-index">

    <p>
        <?= Html::a(Yii::t(null, '{Create}{Backups}', [
            'Create' => Yii::t('app', 'Create'), 'Backups' => Yii::t('app', 'Backups')
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            'name',
            'path',
            'size:shortSize',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{download} {delete}',
                'buttons' => [
                    'download' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-save"></span>', Url::to(['/' . $model->path]), [
                                    'title' => Yii::t('app', 'Restore this backup'),
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['delete', 'id' => $model->id]), [
                                    'title' => Yii::t('app', 'Delete this backup'), 'data-method' => 'post'
                        ]);
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
