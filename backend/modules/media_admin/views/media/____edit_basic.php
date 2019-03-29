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
            'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">"
                . "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>\n"
                . "<div class=\"col-lg-12 col-md-12 clean-padding\">{error}</div>"
            . "</div>",  
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
                    'dirDataProvider' => $dirDataProvider
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), [
                    'id' => 'submitsave', 'class' => 'btn btn-primary btn-flat',
                    'onclick' => 'submitSave($(this))'
                ]) ?>
                
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

<script type="text/javascript">
    var ids = <?= $ids ?>;
    var isPageLoading = false;
    
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        disabledEnterSubmit();       
        submitSave();          
    }
    
    /**
     * 禁用回车提交表单
     * @returns {undefined}
     */
    function disabledEnterSubmit(){
        $("#media-form").keypress(function(event){
            if (event.which == 13) {
                return false;
            }
        });
    }
    
    /**
     * 提交保存
     * @returns {undefined}
     */
    function submitSave(_self){
        console.log(111);
        validateDirDepDropdownValue($("#media-dir_id"));
        // 如果必选项有错误提示，则返回
        if($('div.has-error').length > 0) return;
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.each(ids, function(index, mediaId){
                $.post('/media_admin/media/edit-basic?id=' + mediaId, $('#media-form').serialize(), function(response){
                    if(response.code == "0" && index >= ids.length - 1){
                        isPageLoading = false;  //取消设置提交当中...
                        window.location.reload();
                    }
                });
            });
            _self.hide();
            $('.loading-box .loading, .loading-box .no_more').show();
        }
   }
    
    /**
     * 验证存储目录下拉框是否有选择值
     * @param {zTreeDropdown} _this
     * @returns {undefined}
     */
    function validateDirDepDropdownValue(_this){
        if(!_this.parents('div.form-group').hasClass('required')) return;

        if(_this.val() == ''){
            var label = _this.parents('div.form-group').find('label.form-label').text();
            var relabel = label.replace('*', "");
            _this.parents('div.form-group').addClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
            setTimeout(function(){
                _this.parents('div.form-group').removeClass('has-error');
                _this.parents('div.form-group').find('div.help-block').html('');
            }, 3000);
        }
    }

</script>