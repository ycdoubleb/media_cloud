<?php

use common\models\media\Media;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

$this->title = Yii::t('app', '{Edit}{Media}{Attribute}{Tag}', [
    'Edit' => Yii::t('app', 'Edit'), 'Media' => Yii::t('app', 'Media'),
    'Attribute' => Yii::t('app', 'Attribute'), 'Tag' => Yii::t('app', 'Tag')
]);

//所有媒体id
$ids = json_encode($ids);

?>
<div class="media-edit-attribute">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
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
$js = <<<JS
   
    var ids = $ids;
    var isPageLoading = false;
        
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