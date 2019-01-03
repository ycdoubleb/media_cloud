<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\media\searchs\MediaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="media-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'type_id') ?>

    <?= $form->field($model, 'owner_id') ?>

    <?= $form->field($model, 'dir_id') ?>

    <?php // echo $form->field($model, 'file_id') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'cover_url') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'duration') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'mts_status') ?>

    <?php // echo $form->field($model, 'del_status') ?>

    <?php // echo $form->field($model, 'is_link') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
