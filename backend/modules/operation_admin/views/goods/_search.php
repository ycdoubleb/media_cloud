<?php

use backend\modules\operation_admin\searchs\OrderGoodsSearch;
use kartik\widgets\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model OrderGoodsSearch */
/* @var $form ActiveForm */
?>

<div class="order-goods-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'order-goods-search-form',
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
    
        <!--素材名称-->
        <?= $form->field($model, 'meida_name')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Medias}{Name}：', [
            'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
        ])) ?>
        
        <!--素材编-->
        <?= $form->field($model, 'meida_sn')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Medias}{Number}：', [
            'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
        ])) ?>
       
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">
                <?= Yii::t('app', 'Other Option') ?>
            </label>
            <div class="col-lg-6 col-md-6">
                
                <!--上传者-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
                   <?= $form->field($model, 'uploaded_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $uploadedByMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Uploader')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                <!--审核人-->
                <div id="DepDropdown-handled_by" class="dep-dropdowns">
                    <?= $form->field($model, 'created_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $createdByMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Purchaser')],
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
        $('#order-goods-search-form').submit();
    }   
    
</script>