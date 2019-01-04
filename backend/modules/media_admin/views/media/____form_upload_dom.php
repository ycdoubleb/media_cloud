<?php

use common\widgets\webuploader\Webuploader;
use common\widgets\webuploader\WebUploaderAsset;
use yii\helpers\Html;
use yii\web\View;

?>

<!--选择资源-->
<div class="form-group field-media-file_id">
    <?= Html::label(Yii::t('app', '{Choice}{Media}：', [
        'Choice' => Yii::t('app', 'Choice'), 'Media' => Yii::t('app', 'Media')
    ]), 'video-file_id', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-9 col-md-9">
        <span class="form-must text-danger">*</span>
        <?= Webuploader::widget([
            'id' => 'uploader-container',
            'name' => 'Media[file_ids]',
            'data' => isset($mediaFiles) ? [$mediaFiles] : [],
            'pluginOptions' => [
                //设置最大选择文件数
                'fileNumLimit' => isset($mediaFiles) ? 1 : 100,		
                //设置是否自动开始上传
                'auto' => true,
                //设置分页，每页显示多少项
                'pageSize' => 10,
                //设置允许选择的文件类型
                'accept' => [
                    'title' => 'Media',
                    'extensions' => 'mp4,mp3,gif,jpg,jpeg,bmp,png,doc,docx,txt,xls,xlsx,ppt,pptx',
                    'mimeTypes' => 'video/mp4,audio/mp3,image/*,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx',
                ],

                'pluginEvents' => [
                    'uploadComplete' => 'function(evt, data){window.mediaBatchUpload.init(data);}',
                ]
            ]
        ]); ?>
    </div>
    <div class="col-lg-9 col-md-9"><div class="help-block"></div></div>
</div>

<?php
$js = <<<JS
        
    /**
     * 判断视频文件是否存在
     * @return boolean  
     */
    function isEmpty(){
        var target = $('#euploader-list > tbody > tr').find("input");
        if(target.length <= 0){
            return false;
        }else{
            return true;
        }
    }
JS;
    $this->registerJs($js,  View::POS_READY);
?>