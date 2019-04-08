<?php

use common\models\searchs\ConfigSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ConfigSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Config Admin');
$this->params['breadcrumbs'][] = Yii::t('app', '{Configs}{List}', [
    'Configs' => Yii::t('app', 'Configs'), 'List' => Yii::t('app', 'List')
]);

?>
<div class="config-index">

    <h1><?php //Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t(null, '{Create}{Configs}',[
            'Create' => Yii::t('app', 'Create'),
            'Configs' => Yii::t('app', 'Configs'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'config_name',
            'config_value:ntext',
            'des:ntext',
            
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
