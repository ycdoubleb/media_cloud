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

?>
<div class="media-edit-attribute">
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'action' => ['edit-attribute', 'id' => $model->id],
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
                    'model' => $model,
                    'attrMap' => $attrMap,
                    'attrSelected' => $attrSelected,
                    'tagsSelected' => $tagsSelected,
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
                
    // 提交表单    
    $("#submitsave").click(function(){
        $('#media-form').submit();
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>