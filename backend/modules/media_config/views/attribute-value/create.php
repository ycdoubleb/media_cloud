<?php

use common\models\media\MediaAttributeValue;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaAttributeValue */

$this->title = Yii::t('app', '{Create}{Value}', [
    'Create' => Yii::t('app', 'Create'), 'Value' => Yii::t('app', 'Candidate Value')
]);

?>
<div class="media-attribute-value-create">

    <div class="media-attribute-value-form">

        <?php $form = ActiveForm::begin([
            'options'=>[
                'id' => 'media-attribute-value-form',
                'class' => 'form form-horizontal',
            ],
            'fieldConfig' => [  
                'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">"
                    . "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>\n"
                    . "<div class=\"col-lg-12 col-md-12 clean-padding\">{error}</div>"
                . "</div>",   
                'labelOptions' => [
                    'class' => 'col-lg-1 col-md-1 control-label form-label',
                ],  
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

                    <!--属性值-->
                    <?= $form->field($model, 'value')->textarea([
                        'rows' => 6, 'maxlength' => $model->mediaAttribute->value_length
                    ])->label(Yii::t('app', 'Candidate Value')) ?>

                    <!--是否启用-->
                    <?php
    //                   echo $form->field($model, 'is_del')->checkbox([
    //                        'value' => 0, 'label' => '', 'style' => 'margin-top: 14px'
    //                    ])->label(Yii::t('app', 'Is Use')) 
                    ?>

                </div>

                <div class="modal-footer">

                    <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>

                </div>

           </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
