<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\MediaApprove;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApprove */
/* @var $form ActiveForm */

MediaModuleAsset::register($this);
GrowlAsset::register($this);

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
            
            <div id="myModalBody" class="modal-body">
                
                <!--申请原因-->
                <div class="form-group field-mediaapprove-content has-success">
                    <label class="control-label" for="mediaapprove-content"></label>
                    <?= Html::textarea('MediaApprove[content]', null, [
                        'id' => 'mediaapprove-content', 
                        'class' => 'form-control',
                        'maxlength' => true,
                        'rows' => 20,
                    ]) ?>
                </div>
                
                <div class="result-table hidden">
                    <!--结果提示-->
                    <p class="text-default result-hint" style="font-size: 13px; margin-top: 10px">
                        共有 <span class="max_num">0</span> 个需要申请，其中 <span class="completed_num">0</span> 个成功！
                    </p>
                    
                    <!--文本-->
                    <p class="text-default" style="font-size: 13px;">以下为失败列表：</p>

                    <!--失败列表-->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr><th style="width: 50px;">素材ID</th><th style="width: 210px;">素材名</th><th style="width: 300px;">失败原因</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                         
                <?= Html::button(Yii::t('app', 'Close'), ['id' => 'btn-close', 'class' => 'btn btn-default btn-flat', 'data-dismiss' => 'modal']) ?>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
    var media_ids =  "$media_ids",
        action = "$action";
        
    // 禁用回车提交表单
    $("#media-approve-form").keypress(function(e) {
        if (e.which == 13) {
          return false;
        }
    });    
        
    // 提交表单    
    $("#submitsave").click(function(){
        var mediaIdArray = media_ids.split(",");
        $(this).addClass('hidden');
        $('#myModalLabel').html('申请结果');
        $('#myModalBody').find('div.field-mediaapprove-content').addClass('hidden');
        $('#myModalBody').find('div.result-table').removeClass('hidden');
        $.post('/media_admin/approve/' + action + '?media_ids=' + media_ids, $('#media-approve-form').serialize(), function(response){
            $('#myModalBody').find('div.result-table p.result-hint span.max_num').html(mediaIdArray.length);
            $('#myModalBody').find('div.result-table p.result-hint span.completed_num').html(mediaIdArray.length - response.data.length);
            if(response.code == "0" && response.data.length > 0){
                $.each(response.data, function(){
                    var tr_dom = $('<tr data-vid="'+this.id+'"><td>'+this.id+'</td><td>'+this.name+'</td><td>'+this.reason+'</td></tr>');
                    tr_dom.appendTo($('#myModalBody').find('div.result-table table tbody'));
                });
            }
        })
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>