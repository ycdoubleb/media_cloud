<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\media\MediaRecycle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-recycle-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'media_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'result')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'handled_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handled_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
