<?php

use common\models\media\MediaApprove;
use common\models\media\MediaType;
use common\models\media\searchs\MediaRecycleSearh;
use kartik\widgets\Select2;
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
        <!--关键字-->
        <?= $form->field($model, 'keyword')->textInput(['placeholder' => '请输入媒体名称或者标签'])
            ->label(Yii::t('app', 'Keyword') . '：') ?>
        
        <!--媒体类型-->
        <?= $form->field($model, 'type_id')->checkboxList(MediaType::getMediaByType(), [
//            'value' => ArrayHelper::getValue($filters, 'CourseSearch.level', ''),
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
        ])->label(Yii::t('app', '{Media}{Type}：', [
            'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
        ])) ?>
        
        <div class="col-lg-3 col-md-3">
            <!--申请人-->
            <?= $form->field($model, 'created_by', [
                'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">{input}</div>",  
                'labelOptions' => [
                    'class' => 'col-lg-3 col-md-3 control-label form-label',
                ],
            ])->widget(Select2::class, [
                'data' => MediaApprove::$resultMap,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', 'Applicant') . '：') ?>
        </div>
        
        <div class="col-lg-3 col-md-3">
            <!--审核人-->
            <?= $form->field($model, 'handled_by', [
                'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">{input}</div>",  
                'labelOptions' => [
                    'class' => 'col-lg-3 col-md-3 control-label form-label',
                ],
            ])->widget(Select2::class, [
                'data' => MediaApprove::$resultMap,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', 'Verifier') . '：') ?>
        </div>
        
        <div class="col-lg-3 col-md-3">
            <!--审核状态-->
            <?= $form->field($model, 'status', [
                'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">{input}</div>",  
                'labelOptions' => [
                    'class' => 'col-lg-3 col-md-3 control-label form-label',
                ],
            ])->widget(Select2::class, [
                'data' => MediaApprove::$statusMap,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', '{Auditing}{Status}：', [
                'Auditing' => Yii::t('app', 'Auditing'), 'Status' => Yii::t('app', 'Status')
            ])) ?>
        </div>

        <div class="col-lg-3 col-md-3">
            <!--审核结果-->
            <?= $form->field($model, 'result', [
                'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">{input}</div>",  
                'labelOptions' => [
                    'class' => 'col-lg-3 col-md-3 control-label form-label',
                ],
            ])->widget(Select2::class, [
                'data' => MediaApprove::$resultMap,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', '{Auditing}{Result}：', [
                'Auditing' => Yii::t('app', 'Auditing'), 'Result' => Yii::t('app', 'Result')
            ])) ?>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
