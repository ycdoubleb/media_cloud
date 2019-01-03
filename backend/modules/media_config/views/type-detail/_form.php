<?php

use common\components\aliyuncs\Aliyun;
use common\models\media\MediaType;
use common\models\media\MediaTypeDetail;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaTypeDetail */
/* @var $form ActiveForm */
?>

<div class="media-type-detail-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-type-detail-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-7 col-md-7\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-2 col-md-2 control-label form-label',
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
                
                <!--所属媒体类型-->
                <?= $form->field($model, 'type_id')->widget(Select2::class, [
                    'data' => MediaType::getMediaByType(),
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->label(Yii::t('app', '{The}{Media}{Type}', [
                    'The' => Yii::t('app', 'The'), 'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
                ])) ?>
            
                <!--后缀名？-->
                <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(Yii::t('app', '{Suffix}{Name}', [
                    'Suffix' => Yii::t('app', 'Suffix'), 'Name' => Yii::t('app', 'Name')
                ])) ?>

                <!--图标-->
                <?= $form->field($model, 'icon_url', [
                    'template' => "{label}\n<div class=\"col-lg-5 col-md-5\">{input}</div>\n<div class=\"col-lg-5 col-md-5\">{error}</div>",  
                ])->widget(FileInput::class, [
                    'options' => [
                        'accept' => 'image/*',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'resizeImages' => true,
                        'showCaption' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'browseClass' => 'btn btn-primary btn-block',
                        'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                        'browseLabel' => '选择上传图像...',
                        'initialPreview' => [
                            $model->isNewRecord ?
                                    Html::img(Aliyun::absolutePath('static/imgs/notfound.png'), ['class' => 'file-preview-image', 'width' => '129', 'height' => '98']) :
                                    Html::img($model->icon_url, ['class' => 'file-preview-image', 'width' => '129', 'height' => '98']),
                        ],
                        'overwriteInitial' => true,
                    ],
                ])->label(Yii::t('app', 'Icon'));?>
                
                <!--是否启用-->
                <?= $form->field($model, 'is_del')->checkbox(['value' => 0, 'style' => 'margin-top: 14px'], false)->label(Yii::t('app', 'Is Use')) ?>

            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
