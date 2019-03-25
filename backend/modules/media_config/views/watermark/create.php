<?php

use common\models\Watermark;
use yii\web\View;

/* @var $this View */
/* @var $model Watermark */

$this->title = Yii::t('app', '{Create}{Watermark}', [
    'Create' => Yii::t('app', 'Create'), 'Watermark' => Yii::t('app', 'Watermark')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Watermark}{List}', [
    'Watermark' => Yii::t('app', 'Watermark'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="watermark-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
