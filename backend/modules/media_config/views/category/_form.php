<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\media\MediaCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-category-form">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-category-form',
            'class' => 'form form-horizontal',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">"
                . "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>\n"
                . "<div class=\"col-lg-12 col-md-12 clean-padding\">{error}</div>"
            . "</div>", 
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
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
                                
                <!--类目名称-->
                <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => true]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
