<?php

use common\models\media\Media;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

$this->title = Yii::t('app', '{Edit}{Basic Info}', [
    'Edit' => Yii::t('app', 'Edit'), 'Basic Info' => Yii::t('app', 'Basic Info'),
]);

?>
<div class="media-edit-basic">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['edit-basic', 'id' => $model->id],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-7 col-md-7\"><div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>\n<div class=\"col-lg-12 col-md-12 clean-padding\">{error}</div></div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
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
    
                <?= $this->render('____form_basic_dom', [
                    'model' => $model,
                    'form' => $form,
                ]) ?>
                
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
   
    var ids = $ids;
    var isPageLoading = false;
        
    // 禁用回车提交表单
    $("#media-form").keypress(function(e) {
        if (e.which == 13) {
          return false;
        }
    });    
        
    // 提交表单    
    $("#submitsave").click(function(){
        var _self = $(this);
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.each(ids, function(index, mediaId){
                $.post('/media_admin/media/edit-basic?id=' + mediaId, $('#media-form').serialize(), function(response){
                    if(response.code == "0" && index >= ids.length - 1){
                        isPageLoading = false;  //取消设置提交当中...
//                        _self.show();
//                        $('.loading-box .loading, .loading-box .no_more').hide();
                        window.location.reload();
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