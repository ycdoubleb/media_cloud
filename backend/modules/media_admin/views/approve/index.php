<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\media\searchs\MediaApproveSearh */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Media Approves');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-approve-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Media Approve'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'media_id',
            'type',
            'content',
            'status',
            //'result',
            //'feedback',
            //'handled_by',
            //'handled_at',
            //'created_by',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
