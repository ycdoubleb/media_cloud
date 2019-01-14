<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\MediaApprove;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApprove */
/* @var $form ActiveForm */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Approve}{Reason}', [
    'Approve' => Yii::t('app', 'Approve'), 'Reason' => Yii::t('app', 'Reason')
]);

?>
<div class="media-approve-create">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-approve-form',
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
                
                <div class="form-group field-mediaapprove-content has-success">
                    <label class="control-label" for="mediaapprove-content"></label>
                    <?= Html::textarea('MediaApprove[content]', null, [
                        'id' => 'mediaapprove-content', 
                        'class' => 'form-control',
                        'maxlength' => true,
                        'rows' => 20,
                    ]) ?>
                </div>
                    
            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-flat']) ?>
                                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>