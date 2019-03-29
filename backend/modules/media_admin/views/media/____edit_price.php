<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

GrowlAsset::register($this);

$this->title = Yii::t('app', 'Reset Price');

?>
<div class="media-edit-basic">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ],
    ]); ?>
    
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
    
                <!--素材价格-->
                <div class="form-group field-media-price required">
                    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Medias}{Price}：', [
                        'Medias' => Yii::t('app', 'Medias'), 'Price' => Yii::t('app', 'Price')
                    ]), 'media-price', ['class' => 'col-lg-2 col-md-2 control-label form-label']) ?>
                    
                    <div class="col-lg-7 col-md-7">
                        <div class="col-lg-12 col-md-12 clean-padding">
                            
                            <?= Html::textInput('Media[price]', '0.00', [
                                'id' => 'media-price', 'class' => 'form-control',
                                'type' => 'number', 'placeholder' => '请输入素材价格',
                                'aria-required' => true, 'aria-invalid' => false
                            ]) ?>
                            
                        </div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 clean-padding">
                        
                        <div class="help-block"></div>
                            
                    </div>
                        
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
                        // 获取素材数据 
                        $.get("/media_admin/media/list?page=" + window.page,  window.params, function(response){
                            $('#media_list').html(response);
                            $('.myModal').modal('hide');
                        });
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