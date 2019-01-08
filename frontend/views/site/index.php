<?php

use common\widgets\webuploader\Webuploader;
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
    </div>
</div>
<script>

</script>