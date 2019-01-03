<?php

use common\models\media\MediaAttribute;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttribute */

$this->title = Yii::t('app', "{Update}{Media}{Attribute}: {$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute')
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Attribute}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="media-attribute-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
