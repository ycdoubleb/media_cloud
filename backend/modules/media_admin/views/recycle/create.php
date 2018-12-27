<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\media\MediaRecycle */

$this->title = Yii::t('app', 'Create Media Recycle');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media Recycles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-recycle-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
