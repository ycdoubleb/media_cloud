<?php

use common\models\media\Media;
use common\utils\I18NUitl;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

GrowlAsset::register($this);

$this->title = I18NUitl::t('app', '{Edit}{Attribute}');

?>
<div class="media-edit-attribute">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
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
    
                <?= $this->render('____form_attribute_dom', [
                    'attrMap' => $attrMap,
                    'attrSelected' => isset($attrSelected) ? $attrSelected : null,
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
        submitValidate();
        if($('div.has-error').length > 0) return;
        
        var _self = $(this);
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.each(ids, function(index, id){
                $.post("/media_admin/media/edit-attribute?id=" + id, $('#media-form').serialize(), function(response){
                    if(response.code == "0" && index >= ids.length - 1){
                        isPageLoading = false;  //取消设置提交当中...
                        $('.myModal').modal('hide');
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
        
    /**
     * 提交时验证
     * @returns {undefined}
     */
    function submitValidate()
    {
        $('div.form-group').find('.media-attribute_value, .media-tag_id').each(function(){
            validateDepDropdownValue($(this));
            validateCheckboxList($(this).find('input'));
        });
    }

JS;
    $this->registerJs($js,  View::POS_READY);
?>