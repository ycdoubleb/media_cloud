<?php

use common\models\media\MediaTypeDetail;
use yii\web\View;

/* @var $this View */
/* @var $model MediaTypeDetail */

$this->title = Yii::t('app', '{Create}{Media}{Type}{Suffix}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media'), 
    'Type' => Yii::t('app', 'Type'), 'Suffix' => Yii::t('app', 'Suffix')
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{File}{Suffix}', [
    'Media' => Yii::t('app', 'Media'), 'File' => Yii::t('app', 'File'), 'Suffix' => Yii::t('app', 'Suffix')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-type-detail-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
