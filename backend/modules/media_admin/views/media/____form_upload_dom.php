<?php

use common\widgets\webuploader\Webuploader;
use common\widgets\webuploader\WebUploaderAsset;
use yii\helpers\Html;
use yii\web\View;
?>

<!--选择资源-->
<div class="form-group field-media-file_id required">
    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Choice}{Media}：', [
                'Choice' => Yii::t('app', 'Choice'), 'Media' => Yii::t('app', 'Media')
            ]), 'video-file_id', ['class' => 'col-lg-1 col-md-1 control-label form-label'])
    ?>
    <div class="col-lg-9 col-md-9">
        <div class="col-lg-12 col-md-12 clean-padding">
            <?= Webuploader::widget([
                'id' => 'uploader-container',
                'name' => 'Media[files][]',
                'pluginOptions' => [
                    //设置最大选择文件数
                    'fileNumLimit' => !$model->isNewRecord ? 1 : 100,
                    //设置是否自动开始上传
                    'auto' => true,
                    //设置分页，每页显示多少项
                    'pageSize' => 10,
                    //设置允许选择的文件类型
                    'accept' => [
                        'mimeTypes' => $mimeTypes,
                    ],
                ],
                'pluginEvents' => [
                    'uploadComplete' => 'function(evt, data){ uploadComplete(data) }',
                    'fileDequeued' => 'function(evt, file){ fileDequeued(file) }',
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-12 col-md-12 clean-padding"><div class="help-block"></div></div>
    </div>
</div>

<script type="text/javascript">

    /**
     * 验证上传文件是否有选择值
     * @param {number} total
     * @param {bool} isUploadFinished
     * @returns {undefined}
     */
    function validateWebuploaderValue(total, isUploadFinished)
    {
        if(!$('div.field-media-file_id').hasClass('required')) return;
        
        if(total <= 0){
            $('div.field-media-file_id').addClass('has-error');
            $('div.field-media-file_id').find('div.help-block').html('素材文件列表不能为空。');
            setTimeout(function(){
                $('div.field-media-file_id').removeClass('has-error');
                $('div.field-media-file_id').find('div.help-block').html('');
            }, 3000);
        }else if(!isUploadFinished){
            $('div.field-media-file_id').addClass('has-error');
            $('div.field-media-file_id').find('div.help-block').html('素材文件列表尚有文件未上传。');
            setTimeout(function(){
                $('div.field-media-file_id').removeClass('has-error');
                $('div.field-media-file_id').find('div.help-block').html('');
            }, 3000);
        }
    }

</script>
