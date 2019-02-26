<?php

use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\media\searchs\MediaSearch;
use common\widgets\depdropdown\DepDropdown;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaSearch */
/* @var $form ActiveForm */

?>

<div class="media-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <!--关键字-->
        <?= $form->field($model, 'keyword')->textInput([
            'placeholder' => '请输入素材名称或者标签', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Keyword') . '：') ?>
        
        <!--存储目录-->
        <?= $form->field($model, 'dir_id', [
            'template' => "{label}\n<div class=\"col-lg-10 col-md-10\">{input}</div>",
        ])->widget(DepDropdown::class,[
            'pluginOptions' => [
                'url' => Url::to(['/media_config/dir/search-children']),
                'max_level' => 10,
                'onChangeEvent' => new JsExpression('function(){ submitForm()}')
            ],
            'items' => Dir::getDirsBySameLevel($model->dir_id, Yii::$app->user->id, true, true),
            'values' => $model->dir_id == 0 ? [] : array_values(array_filter(explode(',', Dir::getDirById($model->dir_id)->path))),
            'itemOptions' => [
                'style' => 'width: 175px; display: inline-block;',
            ],
        ])->label(Yii::t('app', '{Storage}{Dir}：', [
            'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
        ])) ?>
        
        <!--素材类型-->
        <?= $form->field($model, 'type_id')->checkboxList(MediaType::getMediaByType(), [
            'itemOptions'=>[
                'onclick' => 'submitForm();',
                'labelOptions'=>[
                    'style'=>[
                        'margin'=>'5px 30px 10px 0px',
                        'color' => '#666666',
                        'font-weight' => 'normal',
                    ]
                ]
            ],
        ])->label(Yii::t('app', '{Media}{Type}：', [
            'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
        ])) ?>

        <!--属性选项-->
        <div class="form-group field-mediasearch-attribute_value_id">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id">属性选项：</label>
            <div class="col-lg-10 col-md-10">
                
                <?php foreach ($attrMap as $atts): ?>
                
                    <?php if($atts['index_type'] > 0): ?>
                        <div id="DepDropdown_<?= $atts['attr_id'] ?>" class="dep-dropdowns">

                            <?= Select2::widget([
                                'id' => "attribute_value_{$atts['attr_id']}",
                                'name' => 'MediaSearch[attribute_value_id][]',
                                'value' => ArrayHelper::getValue($filters, 'MediaSearch.attribute_value_id'),
                                'data' => ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'),
                                'hideSearch' => true,
                                'options' => ['placeholder' => $atts['name']],
                                'pluginOptions' => ['allowClear' => true],
                                'pluginEvents' => ['change' => 'function(){ submitForm()}']
                            ]) ?>

                        </div>
                    <?php endif; ?>
               
                <?php endforeach;?>
                
            </div>
        </div>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">其他选项：</label>
            <div class="col-lg-6 col-md-6">
                
                <!--运营者-->
                <div id="DepDropdown_operator" class="dep-dropdowns">
                    <?= $form->field($model, 'owner_id',[
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Operator')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                <!--上传者-->
                <div id="DepDropdown-uploader" class="dep-dropdowns">
                    <?= $form->field($model, 'created_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Uploader')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                 <!--状态-->
                <div id="DepDropdown_status" class="dep-dropdowns">
                    <?= $form->field($model, 'status',[
                        'template' => "<div class=\"col-lg-12 col-md-12\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => Media::$statusName,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Status')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
            </div>
        </div>
        
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#media-search-form').submit();
    }   
    
</script>