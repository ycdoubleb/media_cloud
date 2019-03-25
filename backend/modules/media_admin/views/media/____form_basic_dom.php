<?php

use common\models\media\Media;
use common\widgets\zTree\zTreeDropDown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model Media */
/* @var $form ActiveForm */

?>

<!--存储目录-->
<?= $form->field($model, 'dir_id')->widget(zTreeDropDown::class, [
    'id' => 'media-dir_id',
    'name' => 'Media[dir_id]',
    'value' => $model->dir_id,
    'data' => $dirDataProvider,
    'url' => [
        'view' => Url::to(['/media_config/dir/search-children', 'category_id' => $model->category_id]),
        'create' => Url::to(['/media_config/dir/add-dynamic', 'category_id' => $model->category_id]),
        'update' => Url::to(['/media_config/dir/edit-dynamic']),
        'delete' => Url::to(['/media_config/dir/delete']),
    ],
])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Storage Dir') . '：') ?>

<!--素材名称-->
<?= $form->field($model, 'name')->textInput([
    'placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => true
])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Medias}{Name}：', [
    'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
])) ?>

<!--素材价格-->
<?= $form->field($model, 'price')->textInput([
    'placeholder' => '请输入素材价格', 'maxlength' => true, 'type' => 'number'
])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Price') . '：') ?>

<!--素材内容-->
<div class="form-group field-media-content">
    <?= Html::label(Yii::t('app', '{Medias}{Content}：', [
        'Medias' => Yii::t('app', 'Medias'), 'Content' => Yii::t('app', 'Content')
    ]), 'field-media-content', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-7 col-md-7">
        <?= Html::textarea('Media[content]', !empty($model->detail) ? $model->detail->content : null, [
            'id' => 'MediaApprove-content', 
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Input Placeholder'),
            'maxlength' => true,
            'rows' => 10,
        ]) ?>
    </div>
</div>