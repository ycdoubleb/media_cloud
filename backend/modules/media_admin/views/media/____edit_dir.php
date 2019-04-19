<?php

use common\widgets\zTree\zTreeDropDown;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

GrowlAsset::register($this);

$this->title = Yii::t('app', 'Reset Dir');

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
                <div class="form-group field-media-dir_id required">
                    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Storage Dir'), 'media-dir_id', ['class' => 'col-lg-2 col-md-2 control-label form-label']) ?>
                    
                    <div class="col-lg-7 col-md-7">
                        <div class="col-lg-12 col-md-12 clean-padding">
                            
                            <?= zTreeDropDown::widget([
                                'id' => 'media-dir_id',
                                'name' => 'Media[dir_id]',
                                'data' => $dirDataProvider,
                                'url' => [
                                    'index' => Url::to(['/media_config/dir/search-children', 'category_id' => $category_id]),
                                    'create' => Url::to(['/media_config/dir/add-dynamic', 'category_id' => $category_id]),
                                    'update' => Url::to(['/media_config/dir/edit-dynamic', 'category_id' => $category_id]),
                                    'delete' => Url::to(['/media_config/dir/delete']),
                                ],
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