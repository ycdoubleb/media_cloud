<?php

use backend\modules\operation_admin\searchs\AclSearch;
use common\models\media\Acl;
use kartik\widgets\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model AclSearch */
/* @var $form ActiveForm */
?>

<div class="acl-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'acl-searc-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-10 col-md-10\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-2 col-md-2 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <div class="clo-lg-6 col-md-6 clear-padding">
            <!--访问ID-->
            <?= $form->field($model, 'id')->textInput([
                'placeholder' => '请输入访问ID', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Visit}{ID}：', [
                'Visit' => Yii::t('app', 'Visit'), 'ID' => Yii::t('app', 'ID')
            ])) ?>
        </div>
        
        <div class="clo-lg-6 col-md-6 clear-padding">
            <!--访问名称-->
            <?= $form->field($model, 'name')->textInput([
                'placeholder' => '请输入访问名称', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Visit}{Name}：', [
                'Visit' => Yii::t('app', 'Visit'), 'Name' => Yii::t('app', 'Name')
            ])) ?>
        </div>
        
        <div class="clo-lg-6 col-md-6 clear-padding">
            <!--素材编号-->
            <?= $form->field($model, 'media_id')->textInput([
                'placeholder' => '请输入素材编号', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', '{Media}{Number}：', [
                'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
            ])) ?>

            <!--订单编号-->
            <?= $form->field($model, 'order_sn')->textInput([
                'placeholder' => '请输入订单编号', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', 'Order Sn'). '：') ?>

            <!--状态-->
            <?= $form->field($model, 'status')->checkboxList(Acl::$statusMap, [
                'style' => 'margin-right: -60px;',
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
            ])->label(Yii::t('app', 'Status'). '：') ?>

            <!--购买人-->
            <?= $form->field($model, 'user_id',[
                'template' => "{label}\n<div class=\"col-lg-3 col-md-3\">{input}</div>",  
            ])->widget(Select2::class, [
                'data' => $userMap,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => ['allowClear' => true],
                'pluginEvents' => ['change' => 'function(){ submitForm()}']
            ])->label(Yii::t('app', 'Purchaser') . '：') ?>
        
       </div>
        
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#acl-searc-form').submit();
    }   
    
</script>