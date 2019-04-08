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
        'action' => array_merge(['index'], $filters),
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
        ])->label(Yii::t('app', '{Orders}{Status}：', [
            'Orders' => Yii::t('app', 'Orders'), 'Status' => Yii::t('app', 'Status')
        ])) ?>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">
                <?= Yii::t('app', 'Other Option') . '：' ?>
            </label>
            <div class="col-lg-6 col-md-6">
                
                <!--购买人-->
                <div id="DepDropdown_purchaser" class="dep-dropdowns">
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
                
                <!--审核人-->
                <div id="DepDropdown-handled_by" class="dep-dropdowns">
                    <?= $form->field($model, 'handled_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $handledByMap,
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
        $('#play-approve-search-form').submit();
    }   
    
</script>