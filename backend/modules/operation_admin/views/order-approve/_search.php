<?php

use backend\modules\operation_admin\searchs\PlayApproveSearch;
use common\models\order\PlayApprove;
use kartik\widgets\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model PlayApproveSearch */
/* @var $form ActiveForm */
?>

<div class="play-approve-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'play-approve-search-form',
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
            'placeholder' => '请输入订单名称', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', '{Order}{Name}', [
            'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
        ]) . '：') ?>
        
        <!--订单编号-->
        <?= $form->field($model, 'order_sn')->textInput([
            'placeholder' => '请输入订单编号', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Order Sn') . '：') ?>
                
        <!--审核状态-->
        <?= $form->field($model, 'status')->checkboxList(PlayApprove::$statusName, [
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
        ])->label(Yii::t('app', '{Order}{Status}：', [
            'Order' => Yii::t('app', 'Order'), 'Status' => Yii::t('app', 'Status')
        ])) ?>
        
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
        
        <!--审核人-->
        <?= $form->field($model, 'handled_by',[
            'template' => "{label}\n<div class=\"col-lg-2 col-md-2\">{input}</div>",  
        ])->widget(Select2::class, [
            'data' => $handledByMap,
            'hideSearch' => true,
            'options' => ['placeholder' => Yii::t('app', 'All')],
            'pluginOptions' => ['allowClear' => true],
            'pluginEvents' => ['change' => 'function(){ submitForm()}']
        ])->label(Yii::t('app', 'Handled By') . '：') ?>
      
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#play-approve-search-form').submit();
    }   
    
</script>