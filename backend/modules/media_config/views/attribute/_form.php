<?php

use common\models\media\MediaAttribute;
use common\models\media\MediaCategory;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaAttribute */
/* @var $form ActiveForm */
?>

<div class="media-attribute-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-attribute-form',
            'class' => 'form form-horizontal',
//            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-7 col-md-7\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                
                <!--名称-->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                
                <!--所属类目-->
                <?= $form->field($model, 'category_id')->widget(Select2::class, [
                    'data' => MediaCategory::getMediaCategory(),
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->label(Yii::t('app', '{The}{Category}', [
                    'The' => Yii::t('app', 'The'), 'Category' => Yii::t('app', 'Category')
                ])) ?>

                <!--是否启用-->
                <?= $form->field($model, 'is_del')->checkbox([
                    'value' => 0, 'label' => '', 'style' => 'margin-top: 14px'
                ])->label(Yii::t('app', 'Is Use')) ?>
                
                <!--输入类型-->
                <?= $form->field($model, 'input_type')->widget(Select2::class, [
                    'data' => MediaAttribute::$inputTypeMap,
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->label(Yii::t('app', '{Input}{Type}', [
                    'Input' => Yii::t('app', 'Input'), 'Type' => Yii::t('app', 'Type')
                ])) ?>
                
                <!--值长度-->
                <?= $form->field($model, 'value_length')->textInput(['type' => 'number'])->label(Yii::t('app', '{Value}{Length}', [
                    'Value' => Yii::t('app', 'Value'), 'Length' => Yii::t('app', 'Length')
                ])) ?>
                
                <!--是否必选-->
                <?= $form->field($model, 'is_required')->checkbox([
                    'value' => 1, 'label' => '', 'style' => 'margin-top: 14px'
                ])->label(Yii::t('app', 'Is Required')) ?>
                
                <!--是否搜索-->
                <?= $form->field($model, 'index_type')->checkbox([
                    'value' => 1, 'label' => '', 'style' => 'margin-top: 14px'
                ])->label(Yii::t('app', 'Is Search')) ?>
                
            </div>

            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
