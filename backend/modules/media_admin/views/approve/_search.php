<?php

use common\models\media\MediaApprove;
use common\models\media\searchs\MediaApproveSearh;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApproveSearh */
/* @var $form ActiveForm */
?>

<div class="media-approve-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'approve-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-2 col-md-2 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
        
        <div class="col-lg-6 col-md-6 clean-padding">
            <!--媒体编号-->
            <?= $form->field($model, 'media_id')->textInput([
                'placeholder' => '请输入媒体编号', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Media}{Number}', [
                'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
            ]) . '：') ?>
        </div>     
            
        <div class="col-lg-6 col-md-6 clean-padding">
            <!--媒体名称-->
            <?= $form->field($model, 'media_name')->textInput([
                'placeholder' => '请输出媒体名称', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Media}{Name}', [
                'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
            ]) . '：') ?>
        </div>

        <div class="col-lg-12 col-md-12 clean-padding">
            <!--审核类型-->
            <?= $form->field($model, 'type', [
                'labelOptions' => [
                    'class' => 'col-lg-1 col-md-1 control-label form-label',
                ],
            ])->checkboxList(MediaApprove::$typeMap, [
                'itemOptions'=>[
                    'onclick' => 'submitForm();',
                    'labelOptions'=>[
                        'style'=>[
                            'margin'=>'5px 30px 10px 0px',
                            'color' => '#666666',
                            'font-weight' => 'normal',
                        ]
                    ]
                ],
            ])->label(Yii::t('app', '{Auditing}{Type}：', [
                'Auditing' => Yii::t('app', 'Auditing'), 'Type' => Yii::t('app', 'Type')
            ])) ?>
        </div>

        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">其他选项：</label>
            <div class="col-lg-10 col-md-10">
                
                <!--审核状态-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'status', [
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => MediaApprove::$statusMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Auditing}{Status}', [
                            'Auditing' => Yii::t('app', 'Auditing'), 'Status' => Yii::t('app', 'Status')
                        ])],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>    
                
                <!--审核结果-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'result', [
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => MediaApprove::$statusMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Auditing}{Result}', [
                            'Auditing' => Yii::t('app', 'Auditing'), 'Result' => Yii::t('app', 'Result')
                        ])],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>    
                
                <!--申请人-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                   <?= $form->field($model, 'created_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Applicant')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                <!--审核人-->
                <div id="DepDropdown-handled_by" class="dep-dropdowns">
                    <?= $form->field($model, 'handled_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Verifier')],
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
        $('#approve-search-form').submit();
    }   
    
</script>