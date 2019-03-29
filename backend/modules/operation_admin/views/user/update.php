<?php

use common\models\AdminUser;
use common\models\User;
use common\widgets\webuploader\ImagePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model User */

$this->title = Yii::t('app', "{Update}{User}", [
    'Update' => Yii::t('app', 'Update'), 'User' => Yii::t('app', 'User')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{User}{List}', [
    'User' => Yii::t('app', 'User'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="user-update">

    <div class="user-form">
    
        <?php $form = ActiveForm::begin([
            'options' => [
                'class' => 'form form-horizontal',
                'enctype' => 'multipart/form-data',
            ],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">{input}</div>\n<div class=\"col-lg-9 col-md-9\">{error}</div>",
                'labelOptions' => [
                    'class' => 'col-lg-1 col-md-1 control-label form-label'
                ],
            ],
        ]); ?>


            <div class="col-lg-7 col-md-7">

                <?= ($model->isNewRecord ? "" : $form->field($model, 'id')->textInput(['maxlength' => 32, 'readonly' => 'true']));?>

                <?= $form->field($model, 'username')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => 32
                ])->label(Yii::t('app', 'User Account Number')); ?>

                <?= $form->field($model, 'nickname')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => 32
                ])->label(Yii::t('app', 'Real Name')); ?>

                <?= $form->field($model, 'password_hash')->passwordInput(['minlength' => 6, 'maxlength' => 20]); ?>

                <?= $form->field($model, 'password2')->passwordInput(['minlength' => 6, 'maxlength' => 20]); ?>

                <?= $form->field($model, 'sex')->radioList(AdminUser::$sexName, [
                    'itemOptions'=>[
                        'labelOptions'=>[
                            'style'=>[
                                'margin'=>'5px 29px 10px 0px',
                                'color' => '#666666',
                                'font-weight' => 'normal',
                            ]
                        ],
                    ],
                ]) ?>


                <?= $form->field($model, 'phone')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'minlength' => 11, 'maxlength' => 11
                ])->label(Yii::t('app', 'Contact Way')); ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => 200]) ?>

            </div>

            <div class="col-lg-5 col-md-5">

                <?= $form->field($model, 'avatar')->widget(ImagePicker::class, [
                    'id' => 'avatar',
                    'pluginOptions' =>[
                        'fileSingleSizeLimit' => 1*1024*1024,
                        //设置允许选择的文件类型
                        'accept' => [
                            'mimeTypes' => 'image/jpeg',
                        ],
                    ],
                ]) ?>

            </div>


        <div class="form-group col-lg-7 col-md-7">
            <?= Html::label(null, null, ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
            <div class="col-lg-11 col-md-11">
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['id' => 'submitsave', 
                    'class' => 'btn btn-success btn-flat', 'onclick' => 'submitForm()']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
