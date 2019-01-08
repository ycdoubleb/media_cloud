<?php

use common\widgets\ueditor\UeditorAsset;
use common\widgets\webuploader\Webuploader;
use yii\web\View;

/* @var $this View */

$this->title = 'My Yii Application';
UeditorAsset::register($this);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">
        <div id="uploader-container">
            <?=
            Webuploader::widget([
                'name' => 'files',
                'pluginEvents' => [
                    'ready' => 'function(uploader){console.log(uploader);}',
                    'uploadComplete' => 'function(evt, data){console.log(data);}',
                    'uploadFinished' => 'function(evt){console.log("上传完成");}',
            ]]);
            ?>
        </div>
        <div><textarea id="ueditor-container"></textarea></div>
    </div>
</div>
<script>
    window.onload = function () {
        var ue = UE.getEditor('ueditor-container', {
            initialFrameHeight: 200,
            maximumWords: 100000,
            toolbars: [
                [
                    'fullscreen', 'source', '|',
                    'paragraph', 'fontfamily', 'fontsize', '|',
                    'forecolor', 'backcolor', '|',
                    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', '|',
                    'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', '|',
                    'insertorderedlist', 'insertunorderedlist', 'simpleupload', 'horizontal', '|',
                    'selectall', 'cleardoc',
                    'undo', 'redo',
                ]
            ]
        });
    }

</script>