<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\MediaApprove;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApprove */
/* @var $form ActiveForm */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Apply}{Reason}', [
    'Apply' => Yii::t('app', 'Apply'), 'Reason' => Yii::t('app', 'Reason')
]);

// 当前action
$action = Yii::$app->controller->action->id

?>
<div class="media-approve-create">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-approve-form',
            'class' => 'form form-horizontal',
        ],
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
                
                <div class="form-group field-mediaapprove-content has-success">
                    <label class="control-label" for="mediaapprove-content"></label>
                    <?= Html::textarea('MediaApprove[content]', null, [
                        'id' => 'mediaapprove-content', 
                        'class' => 'form-control',
                        'maxlength' => true,
                        'rows' => 20,
                    ]) ?>
                </div>
                    
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                         
                <!--加载-->
                <div class="loading-box" style="text-align: right">
                    <span class="loading" style="display: none"></span>
                    <span class="no_more" style="display: none">提交中...</span>
                </div>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
   
    var mediaIds = $mediaIds;
    var action = "$action";
    var isPageLoading = false;
        
    // 提交表单    
    $("#submitsave").click(function(){
        var _self = $(this);
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.each(mediaIds, function(index, mediaId){
                $.post('/media_admin/approve/' + action + '?media_id=' + mediaId, $('#media-approve-form').serialize(), function(response){
                    if(response.code == "0" && index >= mediaIds.length - 1){
                        isPageLoading = false;  //取消设置提交当中...
                        window.location.replace(window.location.href);
                    }
                });
            });
            _self.hide();
            $('.loading-box .loading, .loading-box .no_more').show();
        }
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>