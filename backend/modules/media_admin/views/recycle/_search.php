<?php

use common\models\media\MediaRecycle;
use common\models\media\MediaType;
use common\models\media\searchs\MediaRecycleSearh;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaRecycleSearh */
/* @var $form ActiveForm */
?>

<div class="media-recycle-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'approve-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => array_merge(['index'], ['category_id' => ArrayHelper::getValue($filters, 'category_id')]),
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
        
        <!--关键字-->
        <?= $form->field($model, 'keyword')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Keyword') . '：') ?>
        
        <!--素材类型-->
        <?= $form->field($model, 'media_type')->checkboxList(MediaType::getMediaByType(), [
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
        ])->label(Yii::t('app', '{Medias}{Type}：', [
            'Medias' => Yii::t('app', 'Medias'), 'Type' => Yii::t('app', 'Type')
        ])) ?>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">
                <?= Yii::t('app', 'Other Option') ?>
            </label>
            <div class="col-lg-10 col-md-10">
                
                <!--处理状态-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'status', [
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => MediaRecycle::$statusMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Handle}{Status}', [
                            'Handle' => Yii::t('app', 'Handle'), 'Status' => Yii::t('app', 'Status')
                        ])],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>    
                
                <!--处理结果-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                    <?= $form->field($model, 'result', [
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => MediaRecycle::$resultMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', '{Handle}{Result}', [
                            'Handle' => Yii::t('app', 'Handle'), 'Result' => Yii::t('app', 'Result')
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
                
                <!--处理人-->
                <div id="DepDropdown-handled_by" class="dep-dropdowns">
                    <?= $form->field($model, 'handled_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Handler')],
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