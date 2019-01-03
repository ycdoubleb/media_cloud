<?php

use common\models\media\MediaType;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model MediaType */

$this->title = Yii::t('app', "{Update}{Media}{Type}: {$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Type}', [
    'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model-> id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="media-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
