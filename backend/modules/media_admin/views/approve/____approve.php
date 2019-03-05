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

$this->title = Yii::t('app', '{Feedback}{Info}', [
    'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
]);

// 当前action
$action = Yii::$app->controller->action->id

?>
<div class="media-approve-update">

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
    
                <div class="form-group field-mediaapprove-feedback has-success">
                    <label class="control-label" for="mediaapprove-feedback"></label>
                    <?= Html::textarea('MediaApprove[feedback]', null, [
                        'id' => 'mediaapprove-feedback', 
                        'class' => 'form-control',
                        'maxlength' => true,
                        'rows' => 20,
                    ]) ?>
                </div>               
                
            </div>
            
            <div id="myModalFooter" class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                         
                <div class="progress" style="display: none">
                    <div class="progress-bar result-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%; line-height: 18px">0%</div>
                </div>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
   
    var ids = $ids;
    var action = "$action";
    var increment = 0,
        complete_num = 0,
        max_num = ids.length,
        progress = $('#myModalFooter').find('div.result-progress');
        
    // 禁用回车提交表单
    $("#media-approve-form").keypress(function(e) {
        if (e.which == 13) {
          return false;
        }
    });        
    
    // 提交表单    
    $("#submitsave").click(function(){
        $(this).hide();
        $('#myModalFooter').find('div.progress').show();
        $.each(ids, function(index, id){
            $.post('/media_admin/approve/' + action + '?id=' + id, $('#media-approve-form').serialize(), function(response){
                if(response.code == "0"){
                    complete_num = ++increment;
                    if(index >= max_num - 1){
                        window.location.replace(window.location.href);
                    }
                }
                progress.css({width: parseInt(complete_num / max_num * 100) + '%'}).html(parseInt(complete_num / max_num * 100) + '%');
            });
        });
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>