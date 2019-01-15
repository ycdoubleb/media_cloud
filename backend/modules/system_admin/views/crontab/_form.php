<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Crontab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crontab-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'crontab_str')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_rundate')->textInput() ?>

    <?= $form->field($model, 'next_rundate')->textInput() ?>

    <?= $form->field($model, 'exec_memory')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'exec_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_del')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
