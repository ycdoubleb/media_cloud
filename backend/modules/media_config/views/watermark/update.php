<?php

use common\models\Watermark;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Watermark */

$this->title = Yii::t('app', "{Update}{Watermark}：{$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Watermark' => Yii::t('app', 'Watermark')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Watermark'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="watermark-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
