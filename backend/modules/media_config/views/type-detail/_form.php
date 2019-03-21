<?php

use common\models\media\MediaType;
use common\models\media\MediaTypeDetail;
use common\widgets\webuploader\ImagePicker;
use kartik\select2\Select2;
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
                
                <!--所属素材类型-->
                <?= $form->field($model, 'type_id')->widget(Select2::class, [
                    'data' => MediaType::getMediaByType(),
                    'hideSearch' => true,
                    'options' => ['placeholder' => Yii::t('app', 'All')],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->label(Yii::t('app', 'Type For Belong')) ?>
            
                <!--后缀名-->
                <?= $form->field($model, 'name')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => true
                ])->label(Yii::t('app', '{Suffix}{Name}', [
                    'Suffix' => Yii::t('app', 'Suffix'), 'Name' => Yii::t('app', 'Name')
                ])) ?>
                
                <!--MIME-->
                <?= $form->field($model, 'mime_type', [
                    'template' => "{label}\n<div class=\"col-lg-7 col-md-7\">{input}</div>\n"
                    . "<div class=\"col-lg-7 col-md-7\">{error}</div>"
                    . "<a href=\"http://www.w3school.com.cn/media/media_mimeref.asp\" target=\"_blank\" style=\"display:block;margin-top:8px\">MIME类型—参考手册</a>",  
                ])->textInput([
                        'placeholder' => Yii::t('app', 'Import format: video/mp4'), 'maxlength' => true
                    ])->label(Yii::t('app', 'MIME{Type}', [
                        'Type' => Yii::t('app', 'Type')
                    ]));
                ?>

                <!--图标-->
                <?= $form->field($model, 'icon_url', [
                    'template' => "{label}\n<div class=\"col-lg-5 col-md-5\">{input}</div>\n<div class=\"col-lg-5 col-md-5\">{error}</div>",  
                ])->widget(ImagePicker::class, [
                    'id' => 'mediatypedetail-icon_url',
                    'pluginOptions' =>[
                        'fileSingleSizeLimit' => 1*1024*1024,
                        //设置允许选择的文件类型
                        'accept' => [
                            'mimeTypes' => 'image/png',
                        ],
                    ],
                ])->label(Yii::t('app', 'Icon'));?>
                
                <!--是否启用-->
                <?php 
//                    echo $form->field($model, 'is_del')->checkbox(['value' => 0, 'style' => 'margin-top: 14px'], false)->label(Yii::t('app', 'Is Use')) 
                ?>

            </div>
            
            <div class="modal-footer">
                
                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>
                
            </div>
                
       </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
