<?php

use backend\modules\operation_admin\searchs\OrderSearch;
use common\models\order\Order;
use kartik\select2\Select2 as Select22;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model OrderSearch */
/* @var $form ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'order-search-form',
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
    
        <!--订单名称-->
        <?= $form->field($model, 'order_name')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Orders}{Name}', [
            'Orders' => Yii::t('app', 'Orders'), 'Name' => Yii::t('app', 'Name')
        ]) . '：') ?>
        
        <!--订单编号-->
        <?= $form->field($model, 'order_sn')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Orders Sn') . '：') ?>
                
        <!--订单状态-->
        <?= $form->field($model, 'order_status')->checkboxList(array_diff(Order::$orderStatusName, [Order::ORDER_STATUS_INVALID => '已作废']), [
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
        ])->label(Yii::t('app', '{Orders}{Status}：', [
            'Orders' => Yii::t('app', 'Orders'), 'Status' => Yii::t('app', 'Status')
        ])) ?>
        
        <!--购买人-->
        <?= $form->field($model, 'created_by',[
            'template' => "{label}\n<div class=\"col-lg-2 col-md-2\">{input}</div>",  
        ])->widget(Select22::class, [
            'data' => $userMap,
            'hideSearch' => true,
            'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => ['change' => 'function(){ submitForm()}']
        ])->label(Yii::t('app', 'Purchaser') . '：') ?>
      
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#order-search-form').submit();
    }   
    
</script>