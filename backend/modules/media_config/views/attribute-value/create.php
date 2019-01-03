<?php

use common\models\media\MediaAttributeValue;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttributeValue */

$this->title = Yii::t('app', '{Create}{Media}{Attribute}{Value}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media'), 
    'Attribute' => Yii::t('app', 'Attribute'), 'Value' => Yii::t('app', 'Value')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Media}{Attribute}{Value}', [
    'Media' => Yii::t('app', 'Media'), 'Attribute' => Yii::t('app', 'Attribute'), 'Value' => Yii::t('app', 'Value')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-attribute-value-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
