<?php

use common\models\UserProfile;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $peofileModel UserProfile */
/* @var $form ActiveForm */

?>

<div class="user-form mc-form">

    <!--警告框-->
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p class="alert-link">修改警告：</p>
        <p>该信息修改后将需要等待后台管理员认证后方可购买素材！</p>
    </div>
    
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
    
    <?= $form->field($peofileModel, 'company')->textInput(['maxlength' => true, 'placeholder' => '公司名称']) ?>
    
    <?= $form->field($peofileModel, 'department')->textInput(['maxlength' => true, 'placeholder' => '部门']) ?>
                
    
    <div class="form-group btn-addupd" style="padding-left: 95px;">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-flat-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
