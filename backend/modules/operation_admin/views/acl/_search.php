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
            'template' => "{label}\n<div class=\"col-lg-11 col-md-11\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <div class="col-lg-12 col-md-12 clear-padding">
            
<!--            <div class="col-lg-6 col-md-6 clear-padding">
                访问ID
                <?php
//                   echo $form->field($model, 'id')->textInput([
//                        'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
//                    ])->label(Yii::t('app', '{Visits}{ID}：', [
//                        'Visits' => Yii::t('app', 'Visits'), 'ID' => Yii::t('app', 'ID')
//                    ])) 
                ?>
            </div>-->
            
            <div class="clo-lg-6 col-md-6 clear-padding">
                <!--访问名称-->
                <?= $form->field($model, 'sn')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
                ])->label(Yii::t('app', 'Visits Sn') . '：') ?>
                
                <!--访问名称-->
                <?php
//                    echo $form->field($model, 'name')->textInput([
//                        'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
//                    ])->label(Yii::t('app', '{Visits}{Name}：', [
//                        'Visits' => Yii::t('app', 'Visits'), 'Name' => Yii::t('app', 'Name')
//                    ])) 
                ?>
            </div>
            
        </div>
        
        <div class="col-lg-12 col-md-12 clear-padding">
            
            <div class="clo-lg-6 col-md-6 clear-padding">
                <!--素材编号-->
                <?= $form->field($model, 'media_id')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
                ])->label(Yii::t('app', '{Medias}{Number}：', [
                    'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
                ])) ?>

                <!--订单编号-->
                <?= $form->field($model, 'order_sn')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
                ])->label(Yii::t('app', 'Orders Sn'). '：') ?>

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
                    'template' => "{label}\n<div class=\"col-lg-4 col-md-4\">{input}</div>",  
                ])->widget(Select2::class, [
                    'data' => $userMap,
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')],
                    'pluginOptions' => ['allowClear' => true],
                    'pluginEvents' => ['change' => 'function(){ submitForm()}']
                ])->label(Yii::t('app', 'Purchaser') . '：') ?>

           </div>
            
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