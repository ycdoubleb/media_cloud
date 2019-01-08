<?php

use common\models\media\Dir;
use common\models\media\Media;
use common\widgets\depdropdown\DepDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model Media */
/* @var $form ActiveForm */

?>

<!--存储目录-->
<?= $form->field($model, 'dir_id', [
    'template' => "{label}\n<div class=\"col-lg-10 col-md-10\">{input}</div>",
])->widget(DepDropdown::class,[
    'pluginOptions' => [
        'url' => Url::to(['/media_config/dir/search-children']),
        'max_level' => 10,
    ],
    'items' => Dir::getDirsBySameLevel($model->dir_id, null, true, true),
    'values' => $model->dir_id == 0 ? [] : array_values(array_filter(explode(',', Dir::getDirById($model->dir_id)->path))),
    'itemOptions' => [
        'style' => 'width: 175px; display: inline-block;',
    ],
])->label(Yii::t('app', '{Storage}{Dir}：', [
    'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
])) ?>

<!--媒体名称-->
<?= $form->field($model, 'name')->textInput([
    'placeholder' => '请输入媒体名称', 'maxlength' => true
])->label(Yii::t('app', '{Media}{Name}：', [
    'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
])) ?>

<!--媒体价格-->
<?= $form->field($model, 'price')->textInput([
    'placeholder' => '请输入媒体价格', 'maxlength' => true, 'type' => 'number'
])->label(Yii::t('app', '{Media}{Price}：', [
    'Media' => Yii::t('app', 'Media'), 'Price' => Yii::t('app', 'Price')
])) ?>

<!--媒体内容-->
<div class="form-group field-media-content">
    <?= Html::label(Yii::t('app', '{Media}{Content}：', [
        'Media' => Yii::t('app', 'Media'), 'Content' => Yii::t('app', 'Content')
    ]), 'field-media-content', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-7 col-md-7">
        <?= Html::textarea('Media[content]', !empty($model->detail) ? $model->detail->content : null, [
            'id' => 'MediaApprove-content', 
            'class' => 'form-control',
            'placeholder' => '请输入媒体内容',
            'maxlength' => true,
            'rows' => 10,
        ]) ?>
    </div>
</div>