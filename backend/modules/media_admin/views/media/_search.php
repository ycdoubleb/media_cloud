<?php

use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\media\searchs\MediaSearch;
use common\widgets\zTree\zTreeAsset;
use common\widgets\zTree\zTreeDropDown;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaSearch */
/* @var $form ActiveForm */

zTreeAsset::register($this);

?>

<div class="media-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => array_merge(['index'], array_merge(['category_id' => ArrayHelper::getValue($filters, 'category_id')], $filters)),
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <!--素材编码-->
        <?= $form->field($model, 'id')->textInput([
            'placeholder' => Yii::t('app', 'Input Placeholder'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Media Number') . '：') ?>
        
        <!--关键字-->
        <?= $form->field($model, 'keyword')->textInput([
            'placeholder' => Yii::t('app', 'Please enter a name or label'), 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Keyword') . '：') ?>
        
        <!--存储目录-->
        <?= $form->field($model, 'dir_id')->widget(zTreeDropDown::class, [
            'data' => $dirDataProvider,
            'url' => [
                'index' => Url::to(['/media_config/dir/search-children', 'category_id' => $category_id]),
                'view' => Url::to(['/media_config/dir/view']),
            ],
            'pluginOptions' => [
                'type' => zTreeDropDown::TYPE_SEARCH,
                'edit' => [
                    'enable' => false
                ]
            ],
            'pluginEvents' => [
                'callback' => [
                    'onClick' => new JsExpression('function(event, treeId, treeNode){
                        zTreeDropdown.setVoluation(treeNode.id, treeNode.name);
                        zTreeDropdown.hideTree();  
                        submitForm();
                    }'),
                ]
            ],
        ])->label(Yii::t('app', 'Storage Dir') . '：') ?>
        
        <!--素材类型-->
        <?= $form->field($model, 'type_id', [
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}"
            . "<span class=\"selectall\" onclick=\"selectall();\">全选</span>|<span class=\"unselectall\" onclick=\"unselectall();\">反选</span></div>", 
        ])->checkboxList(MediaType::getMediaByType(), [
            'style' => 'display: inline-block;',
            'itemOptions'=>[
                'class' => 'pull-left',
                'onclick' => 'submitForm();',
                'labelOptions'=>[
                    'class' => 'checkbox-list-label'
                ]
            ],
        ])->label(Yii::t('app', '{Medias}{Type}：', [
            'Medias' => Yii::t('app', 'Medias'), 'Type' => Yii::t('app', 'Type')
        ])) ?>

        <!--属性选项-->
        <div class="form-group field-mediasearch-attribute_value_id">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id">
                <?= Yii::t('app', 'Attribute Option') . '：' ?>
            </label>
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
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">
                <?= Yii::t('app', 'Other Option') . '：' ?>
            </label>
            <div class="col-lg-6 col-md-6">
                
                <!--运营者-->
                <div id="DepDropdown_operator" class="dep-dropdowns">
                    <?= $form->field($model, 'owner_id',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
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
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
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
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
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
    
    // 全选
    function selectall(){
        $('#mediasearch-type_id').find('input[type="checkbox"]').prop('checked', true);
        submitForm();
    }
    
    // 反选
    function unselectall(){
        $('#mediasearch-type_id').find('input[type="checkbox"]').prop('checked', false);
        submitForm();
    }
    
</script>