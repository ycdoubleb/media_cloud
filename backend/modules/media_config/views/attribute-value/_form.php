<?php

use common\models\media\MediaAttributeValue;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaAttributeValue */
/* @var $form ActiveForm */
?>

<div class="media-attribute-value-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-attribute-value-form',
            'class' => 'form form-horizontal',
//            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">{input}</div>\n<div class=\"col-lg-9 col-md-9\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-2 col-md-2 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                
                <!--属性值-->
                <?= $form->field($model, 'value')->textarea(['rows' => 6, 'maxlength' => $model->mediaAttribute->value_length])
                ->label(Yii::t('app', '{Value}{Name}', [
                    'Value' => Yii::t('app', 'Value'), 'Name' => Yii::t('app', 'Name')
                ])) ?>

                <!--是否启用-->
                <?= $form->field($model, 'is_del')->checkbox([
                    'value' => 0, 'label' => '', 'style' => 'margin-top: 14px'
                ])->label(Yii::t('app', 'Is Use')) ?>
                
            </div>
            
            <div class="modal-footer">

                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>

            </div>
                
       </div>
    </div>
      
    <?php ActiveForm::end(); ?>

</div>
