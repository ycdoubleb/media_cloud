<?php

use common\models\media\MediaCategory;
use yii\web\View;

/* @var $this View */
/* @var $model MediaCategory */

$this->title = Yii::t('app', '{Create}{Media}{Category}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media'), 'Category' => Yii::t('app', 'Category')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Category}', [
    'Media' => Yii::t('app', 'Media'), 'Category' => Yii::t('app', 'Category')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
