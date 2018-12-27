<?php

use common\models\media\MediaAttributeValue;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttributeValue */

$this->title = Yii::t('app', "{Update}{Media}{Attribute}{Value}", [
    'Update' => Yii::t('app', 'Update'), 'Media' => Yii::t('app', 'Media'), 
    'Attribute' => Yii::t('app', 'Attribute'), 'Value' => Yii::t('app', 'Value')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Attribute}{Value}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute'), 'Value' => Yii::t('app', 'Value')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="media-attribute-value-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
