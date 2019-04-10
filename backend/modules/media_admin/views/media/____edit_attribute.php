<?php

use common\models\media\Media;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

GrowlAsset::register($this);

$this->title = Yii::t('app', '{Edit}{Attribute}{Tags}', [
    'Edit' => Yii::t('app', 'Edit'), 'Attribute' => Yii::t('app', 'Attribute'), 'Tags' => Yii::t('app', 'Tags')
]);

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
                
                <?= $this->render('____form_tags_dom', [
                    'isTagRequired' => $isTagRequired,
                    'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Confirm'), ['id' => 'submitsave', 'class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>
    
</div>

<?php
$js = <<<JS
       
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
        $.post("/media_admin/media/edit-attribute?id={$model->id}", $('#media-form').serialize(), function(response){
            if(response.code == "0"){
                window.location.reload();
                $.notify({
                    message: response['msg'],    //提示消息
                },{
                    type: "success", //成功类型
                });
            }
        });
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
            validateTags($(this));
        });
    }

JS;
    $this->registerJs($js,  View::POS_READY);
?>