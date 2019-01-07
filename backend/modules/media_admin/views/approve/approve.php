<?php

use backend\modules\media_admin\assets\ModuleAsset;
use common\models\media\MediaApprove;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApprove */
/* @var $form ActiveForm */

ModuleAsset::register($this);

$this->title = Yii::t('app', '{Feedback}{Info}', [
    'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
]);

?>
<div class="media-approve-update">

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
    
                <div class="form-group field-mediaapprove-feedback has-success">
                    <label class="control-label" for="mediaapprove-feedback"></label>
                    <?= Html::textarea('MediaApprove[feedback]', null, [
                        'id' => 'mediaapprove-feedback', 
                        'class' => 'form-control',
                        'maxlength' => true,
                        'rows' => 20,
                    ]) ?>
                </div>               
                
            </div>
            
            <div class="modal-footer">
                
                <span id="submit-result"></span>
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                
                <?= Html::button(Yii::t('app', 'Close'), [
                    'id' => 'close', 'class' => 'btn btn-default btn-flat hidden',
                    'data-dismiss' => 'modal', 'aria-label' => 'Close'
                ]) ?>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
        
    // 提交表单    
    $("#submitsave").click(function(){
        $('#media-approve-form').submit();
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>