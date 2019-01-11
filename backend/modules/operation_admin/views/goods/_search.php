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
    
        <!--媒体名称-->
        <?= $form->field($model, 'meida_name')->textInput([
            'placeholder' => '请输入媒体名称', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Media}{Name}：', [
            'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
        ])) ?>
        
        <!--媒体编-->
        <?= $form->field($model, 'meida_sn')->textInput([
            'placeholder' => '请输入媒体编号', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Media}{Number}：', [
            'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
        ])) ?>
       
        <!--上传者-->
        <?= $form->field($model, 'uploaded_by', [
            'template' => "{label}\n<div class=\"col-lg-2 col-md-2\">{input}</div>",  
        ])->widget(Select2::class, [
            'data' => $uploadedByMap,
            'hideSearch' => true,
            'options' => ['placeholder' => Yii::t('app', 'All')],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => ['change' => 'function(){ submitForm()}']
        ])->label(Yii::t('app', 'Uploader') . '：') ?>
        
        <!--购买人-->
        <?= $form->field($model, 'created_by',[
            'template' => "{label}\n<div class=\"col-lg-2 col-md-2\">{input}</div>",  
        ])->widget(Select2::class, [
            'data' => $createdByMap,
            'hideSearch' => true,
            'options' => ['placeholder' => Yii::t('app', 'All')],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => ['change' => 'function(){ submitForm()}']
        ])->label(Yii::t('app', 'Purchaser') . '：') ?>
       
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#order-goods-search-form').submit();
    }   
    
</script>