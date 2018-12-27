<?php

use common\models\media\MediaType;
use yii\web\View;

/* @var $this View */
/* @var $model MediaType */

$this->title = Yii::t('app', '{Create}{Media}{Type}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Type}', [
    'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
