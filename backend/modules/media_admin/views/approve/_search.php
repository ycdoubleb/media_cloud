<?php

use common\models\media\MediaApprove;
use common\models\media\searchs\MediaApproveSearh;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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
        'action' => array_merge(['index'], ['category_id' => ArrayHelper::getValue($filters, 'category_id')]),
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
            <!--素材编号-->
            <?= $form->field($model, 'media_id')->textInput([
                'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Medias}{Number}：', [
                'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
            ])) ?>
        </div>     
            
        <div class="col-lg-6 col-md-6 clean-padding">
            <!--素材名称-->
            <?= $form->field($model, 'media_name')->textInput([
                'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Medias}{Name}：', [
                'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
            ])) ?>
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
            ])->label(Yii::t('app', '{Approves}{Type}：', [
                'Approves' => Yii::t('app', 'Approves'), 'Type' => Yii::t('app', 'Type')
            ])) ?>
        </div>

        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">
                <?= Yii::t('app', 'Other Option') ?>
            </label>
            <div class="col-lg-10 col-md-10">
                
                <!--审核状态-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'status', [
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => array_diff(MediaApprove::$statusMap, [MediaApprove::STATUS_CANCELED => '已取消']),
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Approves}{Status}', [
                            'Approves' => Yii::t('app', 'Approves'), 'Status' => Yii::t('app', 'Status')
                        ])],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>    
                
                <!--审核结果-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'result', [
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => MediaApprove::$resultMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Approves}{Result}', [
                            'Approves' => Yii::t('app', 'Approves'), 'Result' => Yii::t('app', 'Result')
                        ])],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>    
                
                <!--申请人-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                   <?= $form->field($model, 'created_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
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
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Approver')],
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