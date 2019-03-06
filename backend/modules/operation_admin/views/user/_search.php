<?php

use common\models\searchs\UserSearch;
use common\models\User;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model UserSearch */
/* @var $form ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'user-searc-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <!--用户名称-->
        <?= $form->field($model, 'nickname')->textInput([
            'placeholder' => '请输入真实名称', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{True}{Name}：',[
            'True' => Yii::t('app', 'True'),'Name' => Yii::t('app', 'Name')
        ])) ?>

        <!--用户账号-->
        <?= $form->field($model, 'username')->textInput([
            'placeholder' => '请输入账号', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Account Number') . '：') ?>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">公司/部门：</label>
        
            <div class="col-lg-6 col-md-6">
                
                <!--公司-->
                <div class="col-lg-6 col-md-6 clean-padding">
                    <?= Html::activeTextInput($model, 'company', [
                        'class' => 'form-control',
                        'placeholder' => '请输入公司',
                        'onchange' => 'submitForm()'
                    ]) ?>
                </div>
                
                <!--部门-->
                <div class="col-lg-6 col-md-6 clean-padding">
                    <?= Html::activeTextInput($model, 'department', [
                        'class' => 'form-control',
                        'placeholder' => '请输入部门',
                        'onchange' => 'submitForm()'
                    ]) ?>
                </div>
                
            </div>
            
        </div>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">其他选项：</label>
        
            <div class="col-lg-6 col-md-6">
                
                <!--状态-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                   <?= $form->field($model, 'status',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => User::$statusIs,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Status')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                <!--认证-->
                <div id="DepDropdown-handled_by" class="dep-dropdowns">
                    <?= $form->field($model, 'is_certificate',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => ['否', '是'],
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Certificate')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
            </div>
            
        </div>
            
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#user-searc-form').submit();
    }   
    
</script>