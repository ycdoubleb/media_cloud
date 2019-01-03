<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\media\searchs\MediaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{Media}{List}', [
    'Media' => Yii::t('app', 'Media'), 'List' => Yii::t('app', 'List')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">
  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'category_id',
            'type_id',
            'owner_id',
            'dir_id',
            //'file_id',
            //'name',
            //'cover_url:url',
            //'url:url',
            //'price',
            //'duration',
            //'size',
            //'status',
            //'mts_status',
            //'del_status',
            //'is_link',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
