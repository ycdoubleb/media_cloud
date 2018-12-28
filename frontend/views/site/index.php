<?php

use common\widgets\webuploader\WebUploaderAsset;
use yii\web\View;

/* @var $this View */

$this->title = 'My Yii Application';
WebUploaderAsset::register($this);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">
        <div id="uploader-container"></div>
    </div>
</div>
<script>
    /**
     * 加载文件上传
     */
    window.uploader;
    window.onload = function () {
        require(['euploader'], function (euploader) {
            //公共配置
            window.config = {
                swf: "$swfpath" + "/Uploader.swf",
                // 文件接收服务端。
                server: '/webuploader/default/upload',
                //检查文件是否存在
                checkFile: '/webuploader/default/check-file',
                //分片合并
                mergeChunks: '/webuploader/default/merge-chunks',
                //自动上传
                auto: true,
                //开起分片上传
                chunked: true,
                name: 'Video[file_id]',
                // 上传容器
                container: '#uploader-container',
                //验证文件总数量, 超出则不允许加入队列
                fileNumLimit: 1,
                //指定接受哪些类型的文件
                accept: {
                    title: 'Material',
                    extensions: 'mp4,mp3,gif,jpg,jpeg,bmp,png,doc,docx,txt,xls,xlsx,ppt,pptx',
                    mimeTypes: 'video/mp4,audio/mp3,image/*,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx',
                },
                formData: {
                    _csrf: "$csrfToken",
                }

            };
            //视频
            window.uploader = new euploader.Uploader(window.config, euploader.FilelistView);
            $(window.uploader).on('uploadComplete',function(f,d){
                console.log(d);
            });
        });
    }

</script>