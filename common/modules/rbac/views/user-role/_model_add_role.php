<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

$this->title = Yii::t('app', 'Add') . Yii::t('app', 'Role');
$animateIcon = ' <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>';
?>
<div class="user-add-role rbac">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            <?php
            $form = ActiveForm::begin([
                        'id' => 'assignment-user-form',
            ]);
            ?>
            <div class="modal-body" id="rolelist">
                <?= Select2::widget([
                    'id' => 'users',
                    'name' => 'roles[]',
                    'value' => null, // initial value
                    'data' => $available,
                    'maintainOrder' => false,
                    'options' => ['placeholder' =>  Yii::t('app', 'Select Placeholder'), 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
               
            </div>
            <div class="modal-footer">
               
                <button class="btn btn-danger" data-dismiss="modal" aria-label="Close"><?= Yii::t('app', 'Close') ?></button>
                <?=
                Html::a(Yii::t('app', 'Submit'), ['add-role', 'user_id' => $id], [
                    'type' => 'submit',
                    'data-method' => 'post',
                    'class' => 'btn btn-primary'
                ])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div> 
</div>
