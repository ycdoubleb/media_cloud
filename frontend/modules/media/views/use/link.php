<?php

use frontend\modules\media\assets\ModuleAssets;
use yii\helpers\Html;

ModuleAssets::register($this);

$this->title = Yii::t('app', 'Preview');
?>

<div class="preview-link">
    <?php
        $mediaType = $model->media->mediaType->sign;
        $mediaUrl = $model->url;
        switch ($mediaType){
            case 'video' : 
                echo '<video src="'.$mediaUrl.'" controls="controls" width="100%"></video>';
                break;
            case 'audio' : 
                echo '<audio src="'.$mediaUrl.'" controls="controls" style="width:100%"></audio>';
                break;
            case 'image' : 
                echo Html::img($mediaUrl, ['style' => 'width:100%']);
                break;
            case 'document' : 
                echo '<iframe src="http://eezxyl.gzedu.com/?furl='.$mediaUrl.'" width="100%" height="700" style="border: none"></iframe>';
                break;
        }
    ?>
</div>