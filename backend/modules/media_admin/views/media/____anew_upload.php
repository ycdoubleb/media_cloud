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
//                    'mediaFiles' => $mediaFiles,
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

<script type="text/javascript">
    
    //url
    var url = "anew-upload?id=<?= $model->id ?>";
    //批量上传控制器
    var mediaBatchUpload;
    //是否已上传完成所有文件
    window.isUploadFinished = false;
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        initBatchUpload();        //初始批量上传
        initSubmit();             //初始提交
    }
    
    /************************************************************************************
    *
    * 初始化批量上传
    *
    ************************************************************************************/
    function initBatchUpload(){
        
        mediaBatchUpload = new mediaupload.MediaBatchUpload({
            media_url : url,
        });
    }
    
    /**
     * 上传完成后返回的文件数据
     * @param {object} data
     * @returns {Array|uploaderMedias}
     */
    function uploadComplete(data){
        mediaBatchUpload.addMediaData(data);
    }
    
    /**
     * 删除上传列表中的文件
     * @param {object} data
     * @returns {undefined}
     */
    function fileDequeued(data){
        mediaBatchUpload.delMediaData(data.dbFile);
    }
    
    /************************************************************************************
     *
     * 初始化提交
     *
     ************************************************************************************/ 
    function initSubmit(){
        // 提交上传
        $("#submitsave").click(function(){
            validateWebuploaderValue(mediaBatchUpload.medias.length);
            if($('div.has-error').length > 0 || !window.isUploadFinished) return;
            mediaBatchUpload.submit();
        });
    }
    
</script>

<?php
$js = <<<JS
        
    window.onload();
        
    // 禁用回车提交表单
    $("#media-form").keypress(function(e) {
        if (e.which == 13) {
          return false;
        }
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>