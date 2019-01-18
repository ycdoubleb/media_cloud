<?php

use common\models\User;
use common\widgets\webuploader\ImagePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $userModel User */
/* @var $form ActiveForm */

?>

<div class="user-form mc-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4 col-md-4\">{input}</div>\n<div class=\"col-lg-6 col-md-6\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 col-md-2 control-label',
                'style' => ['color' => '#999999', 'padding-top' => '10px']],
        ],
    ]); ?>
    
    <?= $form->field($userModel, 'username')->textInput(['maxlength' => true, 'placeholder' => '手机号', 'disabled' => true]) ?>
    
    <?= $form->field($userModel, 'nickname')->textInput(['maxlength' => true, 'placeholder' => '真实名称']) ?>
                
    <?= $form->field($userModel, 'avatar')->widget(ImagePicker::class); ?>
    
    <?= $form->field($userModel, 'password_hash')->passwordInput(['value' => '', 'minlength' => 6, 'maxlength' => 20]) ?>
    
    <?= $form->field($userModel, 'password2')->passwordInput(['minlength' => 6, 'maxlength' => 20]) ?>

    
    <div class="form-group btn-addupd" style="padding-left: 95px;">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-flat-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
