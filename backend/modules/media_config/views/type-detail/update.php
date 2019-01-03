<?php

use common\models\media\MediaTypeDetail;
use yii\web\View;

/* @var $this View */
/* @var $model MediaTypeDetail */

$this->title = Yii::t('app', "{Update}{Media}{Type}{Suffix}: {$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Media' => Yii::t('app', 'Media'), 
    'Type' => Yii::t('app', 'Type'), 'Suffix' => Yii::t('app', 'Suffix')
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{File}{Suffix}', [
    'Media' => Yii::t('app', 'Media'), 'File' => Yii::t('app', 'File'), 'Suffix' => Yii::t('app', 'Suffix')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="media-type-detail-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
