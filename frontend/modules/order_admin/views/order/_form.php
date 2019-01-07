<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\order\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_status')->textInput() ?>

    <?= $form->field($model, 'play_status')->textInput() ?>

    <?= $form->field($model, 'play_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'play_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'play_at')->textInput() ?>

    <?= $form->field($model, 'confirm_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
