<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\order\searchs\CartSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cart-search main-search">
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
                        'placeholder' => '资源编号，资源名称', 'maxlength' => true,
                    ])->label('');?>
                </div>
                <div class="col-log-2 col-md-2 form-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-default btn-flat']) ?>
                </div>
            </div>
            <div class="col-log-6 col-md-6" style="padding-right: 5px;">
                <div class="pull-right">
                    <div class="choice-info">
                        <div class="total-price">总价：<span>￥<?= $total_price;?></span></div>
                        <div class="media-num">已选择 <span><?= $sel_num; ?></span> 个媒体</div>
                    </div>
                    <?php
                    echo Html::a('立即购买', ['checking-order'], 
                        ['class' => 'btn btn-highlight btn-flat', 'title' => '立即购买']) . '&nbsp;';
                    echo Html::a('移出购物车', ['del-media'], 
                        ['class' => 'btn btn-default btn-flat', 'title' => '移出购物车']);
                    ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
