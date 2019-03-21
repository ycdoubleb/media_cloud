<?php

use common\models\Config;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Config */

$this->title =  Yii::t('app', "{Configs}{Detail}",[
    'Configs' => Yii::t('app', 'Configs'), 'Detail' => Yii::t('app', 'Detail'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Configs}{List}', [
    'Configs' => Yii::t('app', 'Configs'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'config_name',
            'config_value:ntext',
            'des:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
