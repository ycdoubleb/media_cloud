<?php

use common\models\media\Media;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

GrowlAsset::register($this);

$this->title = Yii::t('app', '{Edit}{Media}{Attribute}{Tag}', [
    'Edit' => Yii::t('app', 'Edit'), 'Media' => Yii::t('app', 'Media'),
    'Attribute' => Yii::t('app', 'Attribute'), 'Tag' => Yii::t('app', 'Tag')
]);

?>
<div class="media-edit-attribute">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ],
//        'action' => ['edit-attribute', 'id' => $model->id],
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
    
                <?= $this->render('____form_attribute_dom', [
                    'attrMap' => $attrMap,
                    'isTagRequired' => $isTagRequired,
                    'attrSelected' => isset($attrSelected) ? $attrSelected : null,
                    'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
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
$isTagRequired = isset($isTagRequired) && $isTagRequired ? 1 : 0;
$js = <<<JS
   
    var ids = $ids;
    var isPageLoading = false;
    var isTagRequired = $isTagRequired;
    
    // 初始化标签组件
    $("input[data-role=tagsinput]").tagsinput();
    
    // 禁用回车提交表单
    $("#media-form").keypress(function(e) {
        if (e.which == 13) {
          return false;
        }
    });
        
    // 提交表单    
    $("#submitsave").click(function(){
        submitValidate();
        if($('div.has-error').length > 0) return;
        
        var _self = $(this);
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.each(ids, function(index, mediaId){
                $.post('/media_admin/media/edit-attribute?id=' + mediaId, $('#media-form').serialize(), function(response){
                    if(response.code == "0" && index >= ids.length - 1){
                        isPageLoading = false;  //取消设置提交当中...
                        $('.myModal').modal('hide');
                        if(!isTagRequired){
                            // 获取素材数据 
                            $.get("/media_admin/media/list?page=" + window.page,  window.params, function(response){
                                $('#media_list').html(response);
                            });
                        }else{
                            window.location.reload();
                        }
                        $.notify({
                            message: response['msg'],    //提示消息
                        },{
                            type: "success", //成功类型
                        });
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