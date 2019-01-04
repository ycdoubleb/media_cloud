<?php

use common\models\media\Dir;
use common\models\media\MediaType;
use common\models\media\searchs\MediaSearch;
use common\widgets\depdropdown\DepDropdown;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
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
        <?= $form->field($model, 'keyword')->textInput(['placeholder' => '请输入媒体名称或者标签'])
            ->label(Yii::t('app', 'Keyword') . '：') ?>
        
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
        
        <!--媒体类型-->
        <?= $form->field($model, 'type_id')->checkboxList(MediaType::getMediaByType(), [
//            'value' => ArrayHelper::getValue($filters, 'CourseSearch.level', ''),
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
                <?php 
                    /**
                     * 生成属性选项
                     */
                    $attrValMap = [];
                    foreach ($attrMap as $attr){
                        // 分割属性的值，生成属性对应值的数组
                        $mediaAttrValue = explode(',', $attr['attr_value']);
                        // 组装生成属性值下拉选择框
                        foreach ($mediaAttrValue as $attr_val){
                            // 分割成val=>name
                            $value = explode('_', $attr_val);
                            // 生成以属性id索引的下拉列表
                            $attrValMap[$attr['id']][$value[0]] = $value[1];
                        }
                        echo "<div id='DepDropdown_{$attr['id']}' . class='dep-dropdown'>";
                        echo Select2::widget([
                            'id' => "attribute_value_{$attr['id']}",
                            'name' => 'MediaSearch[attribute_value_id][]',
                            'data' => $attrValMap[$attr['id']],
                            'hideSearch' => true,
                            'options' => ['placeholder' => $attr['name']],
                        ]);                       
                        echo '</div>';
                    } 
                ?>
            </div>
        </div>
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">其他选项：</label>
            <div class="col-lg-6 col-md-6">
                
                <!--运营者-->
                <div id="DepDropdown_operator" class="dep-dropdown" style="margin-right: 0">
                    <?= Select2::widget([
                        'id' => 'mediasearch-owner_id',
                        'name' => 'MediaSearch[owner_id]',
                        'data' => [],
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Operator')],
                    ]) ?>
                </div>
                
                <!--上传者-->
                <div id="DepDropdown-uploader" class="dep-dropdown" style="margin-right: 0">
                    <?= Select2::widget([
                        'id' => 'mediasearch-created_by',
                        'name' => 'MediaSearch[created_by]',
                        'data' => [],
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Uploader')],
                    ]) ?>
                </div>
                
                 <!--状态-->
                <div id="DepDropdown_status" class="dep-dropdown" style="margin-right: 0">
                    <?= Select2::widget([
                        'id' => 'mediasearch-status',
                        'name' => 'MediaSearch[status]',
                        'data' => [],
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Status')],
                    ]) ?>
                </div>
                
            </div>
        </div>
        
    </div>    

    <?php ActiveForm::end(); ?>

</div>
