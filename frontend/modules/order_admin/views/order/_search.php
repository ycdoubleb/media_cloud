<?php

use common\models\order\searchs\OrderSearch;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model OrderSearch */
/* @var $form ActiveForm */
?>

<div class="order-search main-search">
    <div class="mc-form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'id' => 'order-admin-form',
                'class' => 'form-horizontal',
            ],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-12 col-md-12\">{input}</div>\n",
                'labelOptions' => [
                    'class' => 'control-label form-label',
                ], 
            ],
        ]);?>

        <div class="col-log-12 col-md-12">
            <div class="col-log-6 col-md-6" style="padding-left: 5px;">
                <div class="col-log-10 col-md-10 search-name">
                    <?= $form->field($searchModel, 'keyword')->textInput([
                        'placeholder' => '订单名称，订单编号', 'maxlength' => true,
                    ])->label('');?>
                </div>
                <div class="col-log-2 col-md-2 form-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-default btn-flat']) ?>
                </div>
            </div>
            <div class="col-log-6 col-md-6"></div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
