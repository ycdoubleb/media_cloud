<?php

use common\models\media\MediaAttribute;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttribute */

$this->title = Yii::t('app', '{Create}{Media}{Attribute}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Attribute}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-attribute-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
