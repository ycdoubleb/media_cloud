<?php

use common\models\media\Acl;
use common\models\media\MediaType;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Acl */

$this->title = Yii::t('app', 'Preview');

?>
<div class="acl-preview">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                
                <?php if(!empty($model->media_id)){
                    switch ($model->media->mediaType->sign){
                        case MediaType::SIGN_VIDEO :
                            echo "<video id=\"myVideo\" src=\"{$model->url}\"  poster=\"{$model->media->cover_url}\" width=\"100%\" controls=\"controls\"></video>";
                            break;
                        case MediaType::SIGN_AUDIO :
                            echo "<audio src=\"{$model->url}\" style=\"width: 100%\" controls=\"controls\"></audio>";
                            break;
                        case MediaType::SIGN_IMAGE :
                            echo "<img src=\"{$model->url}\" width=\"100%\" />";
                            break;
                        case MediaType::SIGN_DOCMENT :
                            echo "<iframe src=\"http://eezxyl.gzedu.com/?furl={$model->url}\" width=\"100%\" height=\"700\" style=\"border: none\"></iframe>";
                            break;
                    }
                }?>
                    
            </div>
            
            <div class="modal-footer">
                
                <span id="submit-result"></span>
                
                <?= Html::button(Yii::t('app', 'Close'), [
                    'id' => 'close', 'class' => 'btn btn-default btn-flat',
                    'data-dismiss' => 'modal', 'aria-label' => 'Close'
                ]) ?>
                
            </div>
                
       </div>
    </div>
  

</div>