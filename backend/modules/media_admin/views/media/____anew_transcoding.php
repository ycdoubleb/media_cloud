<?php

use common\models\media\Media;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

$this->title = Yii::t('app', '{Anew}{Transcoding}{Video}{File}', [
    'Anew' => Yii::t('app', 'Anew'), 'Video' => Yii::t('app', 'Video'),
    'Transcoding' => Yii::t('app', 'Transcoding'), 'File' => Yii::t('app', 'File')
]);

?>
<div class="media-anew-transcoding">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'action' => ['anew-transcoding', 'id' => $model->id],
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
    
                <?= $this->render('____form_watermark_dom', [
                    'model' => $model,
                    'isNewRecord' => $model->isNewRecord ? 1 : 0,
                    'wateFiles' => $wateFiles,
                    'wateSelected' => $wateSelected
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>
    
</div>

<?php
$js = <<<JS
        
    window.onload();

JS;
    $this->registerJs($js,  View::POS_READY);
?>