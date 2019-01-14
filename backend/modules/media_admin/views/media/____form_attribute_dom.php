<?php

use common\models\media\MediaAttribute;
use common\widgets\tagsinput\TagsInputAsset;
use kartik\widgets\Select2;
use yii\helpers\Html;

TagsInputAsset::register($this);

?>

<!--属性-->
<?php 
    $attrValMap = [];
    foreach ($attrMap as $attr): 
        // 分割属性的值，生成属性对应值的数组
        $mediaAttrValue = explode(',', $attr['attr_value']);
        foreach ($mediaAttrValue as $attr_val) {
            // 分割成val=>name
            $value = explode('_', $attr_val);   
            // 生成以属性id索引的下拉列表
            $attrValMap[$attr['id']][$value[0]] = $value[1];
        }
?>

<?php if($attr['is_required']): ?>

<div class="form-group field-media-attribute_value required">
    <?= Html::label('<span class="form-must text-danger">*</span>' . $attr['name'] . '：', 'field-media-attribute_value', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-7 col-md-7">
        <?php
            switch ($attr['input_type']){
                case MediaAttribute::SINGLE_SELECT_INPUT_TYPE:
                    echo Select2::widget([
                        'id' => "media-attribute_value-{$attr['id']}",
                        'name' => "Media[attribute_value][{$attr['id']}]",
                        'data' => $attrValMap[$attr['id']],
                        'value' => isset($attrSelected) ? $attrSelected[$attr['id']] : null,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'All')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => [
//                            'change' => 'function(){ validateAttributeValue($(this))}'
                        ]
                    ]);
                    break;
                case MediaAttribute::MULTPLE_SELECT_INPUT_TYPE:
                    echo Html::checkboxList("Media[attribute_value][{$attr['id']}][]", 
                        isset($attrSelected) ? explode('，', $attrSelected[$attr['id']]) : null, $attrValMap[$attr['id']], [
                        'style' => 'margin-right: -35px;',
                        'itemOptions'=>[
//                            'onchange' => "validateCheckboxList()",
                            'labelOptions'=>[
                                'style'=>[
                                    'margin'=>'5px 25px 10px 0px',
                                    'color' => '#666666',
                                    'font-weight' => 'normal',
                                ]
                            ]
                        ],
                    ]);
                    break;
            }
        ?>
    </div>
    <div class="col-lg-7 col-md-7"><div class="help-block"></div></div>
</div>

<?php endif; ?>

<?php endforeach; ?>

<!--标签-->
<div class="form-group field-media-tag_ids required">
    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Tag') . '：', 'field-media-tag_ids', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-7 col-md-7">
        <?= Html::textInput('Media[tag_ids]', isset($tagsSelected) ? implode(',', $tagsSelected) : null, [
            'id' => 'media-tag_ids', 'class' => 'form-control', 'data-role' => 'tagsinput', 
//            'placeholder' => '请输入至少5个标签'
        ]) ?>
    </div>
    <div class="col-lg-7 col-md-7"><div class="help-block"></div></div>
</div>

<?php
$js = <<<JS
        
    /**
     * 验证下拉框是否有选择值
     * @param {Object} _this
     * @returns {undefined}
     */
    function validateAttributeValue(_this)
    {
        if(_this.val() == ''){
            var label = _this.parents('div.form-group').find('label.form-label').text();
            _this.parents('div.form-group').addClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html(label + '不能为空。');
        }else{
            _this.parents('div.form-group').removeClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html('');
        }
    }
        
    /** 验证标签不少于5个 */
    function validateTags(){
        if($('.field-media-tag_ids').find('span.tag').length < 5){
            $('.field-media-tag_ids').addClass('has-error');
            $('.field-media-tag_ids .help-block').html('标签个数不能少于5个');
        }else{
            $('.field-media-tag_ids').removeClass('has-error');
            $('.field-media-tag_ids .help-block').html('');
        }
    }
    $('.bootstrap-tagsinput > input').change(function(){
        validateTags();
    });    
        
JS;
    //$this->registerJs($js,  View::POS_READY);
?>