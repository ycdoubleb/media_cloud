<?php

use common\models\Crontab;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Crontab */

$this->title =  Yii::t('app', "{Crontabs}{Detail}",[
    'Crontabs' => Yii::t('app', 'Crontabs'), 'Detail' => Yii::t('app', 'Detail'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Crontabs}{List}', [
    'Crontabs' => Yii::t('app', 'Crontabs'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);
?>
<div class="crontab-view">

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
            'name',
            'route',
            'crontab_str',
            'last_rundate',
            'next_rundate',
            'exec_memory',
            'exec_time',
            'status',
            'is_del',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
