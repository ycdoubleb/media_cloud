<?php

use common\models\media\MediaCategory;
use yii\web\View;

/* @var $this View */
/* @var $model MediaCategory */

$this->title = Yii::t('app', "{Update}{Media}{Category}: {$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Media' => Yii::t('app', 'Media'), 'Category' => Yii::t('app', 'Category')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Category}', [
    'Media' => Yii::t('app', 'Media'), 'Category' => Yii::t('app', 'Category')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="media-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
