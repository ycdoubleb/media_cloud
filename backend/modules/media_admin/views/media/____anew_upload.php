<?php

use common\models\media\Media;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

$this->title = Yii::t('app', '{Anew}{Upload}{Media}{File}', [
    'Anew' => Yii::t('app', 'Anew'), 'Media' => Yii::t('app', 'Media'),
    'Upload' => Yii::t('app', 'Upload'), 'File' => Yii::t('app', 'File')
]);

?>
<div class="media-anew-upload">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'action' => ['anew-upload', 'id' => $model->id],
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
    
                <?= $this->render('____form_upload_dom', [
                    'model' => $model,
                    'mediaFiles' => $mediaFiles,
                    'mimeTypes' => $mimeTypes
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>
    
</div>

<?php
$js = <<<JS
        
    // 初始化
    window.mediaBatchUpload = new mediaupload.MediaBatchUpload({media_url: "anew-upload?id={$model->id}"});
        
    // 提交表单    
    $("#submitsave").click(function(){
        var formdata = $('#media-form').serialize();
        window.mediaBatchUpload.submit(formdata);
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>