<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\media\Watermark */

$this->title = Yii::t('app', 'Create Watermark');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Watermarks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="watermark-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
