<?php

use common\models\media\Acl;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Acl */
/* @var $form ActiveForm */


$this->title = Yii::t('app', '{Set}{Status}', [
    'Set' => Yii::t('app', 'Set'), 'Status' => Yii::t('app', 'Status')
]);

?>
<div class="acl-set-status">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'acl-set-status-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
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
                
                <!--状态-->
                <div class="form-group field-acl-status">
                    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Status') . '：', 'field-acl-status', [
                        'class' => 'col-lg-1 col-md-1 control-label form-label'
                    ]) ?>
                    <div class="col-lg-10 col-md-10">
                        <?= Html::radioList('Acl[status]', null, Acl::$statusMap,[
                            'itemOptions'=>[
                                'labelOptions'=>[
                                    'style'=>[
                                        'margin'=>'10px 15px 10px 0',
                                        'color' => '#999999',
                                        'font-weight' => 'normal',
                                    ]
                                ]
                            ],
                        ]) ?>
                    </div>
                    <div class="col-lg-10 col-md-10"><div class="help-block"></div></div>
                </div>
                
                <!--原因-->
                <div class="form-group field-alc-content">
                    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Reason') . '：', 'field-alc-content', [
                        'class' => 'col-lg-1 col-md-1 control-label form-label'
                    ]) ?>
                    <div class="col-lg-10 col-md-10">
                        <?= Html::textarea('Acl[content]', null, [
                            'id' => 'MediaApprove-content', 
                            'class' => 'form-control',
                            'placeholder' => Yii::t('app', 'Input Placeholder'),
                            'maxlength' => true,
                            'rows' => 15,
                        ]) ?>
                    </div>
                    <div class="col-lg-10 col-md-10"><div class="help-block"></div></div>
                </div>
                
            </div>
            
            <div class="modal-footer">
                                
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>