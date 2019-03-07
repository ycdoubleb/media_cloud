<?php

use kartik\widgets\Select2;
use common\modules\rbac\models\AuthItem;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model AuthItem */
/* @var $form ActiveForm */
?>

<div class="role-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-category-form',
            'class' => 'form form-horizontal',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">{input}</div>\n<div class=\"col-lg-10 col-md-10\">{error}</div>",  
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

                <?= $form->field($model, 'group_id')->widget(Select2::classname(), [
                    'data' => $authGroups, 'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')]
                ]) ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), [
                    'class' => 'btn btn-flat ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')
                ]) ?>
                
            </div>

    <?php ActiveForm::end(); ?>

</div>
