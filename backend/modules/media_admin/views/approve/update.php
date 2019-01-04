<?php

use backend\modules\media_admin\assets\ModuleAsset;
use common\models\media\MediaApprove;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaApprove */
/* @var $form ActiveForm */

ModuleAsset::register($this);

$this->title = Yii::t('app', '{Feedback}{Info}', [
    'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
]);

?>
<div class="media-approve-update">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-approve-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
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
    
                <?= $form->field($model, 'feedback')->textarea(['rows' => 20, 'maxlength' => true])->label('') ?>
                
            </div>
            
            <div class="modal-footer">
                
                <span id="submit-result"></span>
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                
                <?= Html::button(Yii::t('app', 'Close'), [
                    'id' => 'close', 'class' => 'btn btn-default btn-flat hidden',
                    'data-dismiss' => 'modal', 'aria-label' => 'Close'
                ]) ?>
                
            </div>
                
       </div>
    </div>
  
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
        
    var result = $result,
        medias = $ids;
    // 初始化
    var mediaBatchApprove = new mediaapprove.MediaBatchApprove({media_url: '/media_admin/approve/update?result=' + result});
    
    $.each(medias, function(index, data){
        mediaBatchApprove.init({id: data});
    });
        
    /** 上传完成 */
    $(mediaBatchApprove).on('submitFinished',function(){
        var max_num = this.medias.length;
        var completed_num = 0;
        $.each(this.medias,function(){
            if(this.submit_result){
                completed_num++;
            }
        });
        // 如果都成功，则显示关闭
        if(max_num == completed_num){
            $('#close').removeClass('hidden')
        };
        
        $('#submit-result').html("共有 "+max_num+" 个需要审核，其中 "+completed_num+" 个成功， "+(max_num - completed_num)+" 个失败！");        
    });
   
    // 提交表单    
    $("#submitsave").click(function(){
        var formdata = $('#media-approve-form').serialize();
        mediaBatchApprove.submit(formdata);
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>