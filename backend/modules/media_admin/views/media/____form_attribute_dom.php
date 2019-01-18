<?php

use common\models\media\MediaAttribute;
use common\widgets\tagsinput\TagsInputAsset;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

TagsInputAsset::register($this);

?>

<!--属性-->
<?php foreach ($attrMap as $atts): ?>

    <div class="form-group field-media-attribute_value <?= $atts['is_required'] ? 'required' : '' ?>">
        
        <?= Html::label(($atts['is_required'] ? '<span class="form-must text-danger">*</span>' : null) . $atts['name'] . '：', 
            'field-media-attribute_value', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
        
        <div class="col-lg-7 col-md-7">
            <?php switch ($atts['input_type']){
                case MediaAttribute::SINGLE_SELECT_INPUT_TYPE:
                    echo Select2::widget([
                        'id' => "media-attribute_value-{$atts['attr_id']}",
                        'name' => "Media[attribute_value][{$atts['attr_id']}]",
                        'data' => ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'),
                        'value' => !empty($attrSelected) ? $attrSelected[$atts['attr_id']] : null, 
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'All')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => [
                            'change' => 'function(){ validateAttributeValue($(this))}'
                        ]
                    ]);
                    break;
                case MediaAttribute::MULTPLE_SELECT_INPUT_TYPE:
                    echo Html::checkboxList("Media[attribute_value][{$atts['attr_id']}][]", 
                            !empty($attrSelected) ? explode('，', $attrSelected[$atts['attr_id']]) : null, 
                            ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'), 
                            [
                                'style' => 'margin-right: -35px;',
                                'itemOptions'=>[
                                    'onclick' => 'validateCheckboxList($(this))',
                                    'labelOptions'=>[
                                        'style'=>[
                                            'margin'=>'5px 25px 10px 0px',
                                            'color' => '#666666',
                                            'font-weight' => 'normal',
                                        ]
                                    ]
                                ],
                            ]
                        );
                    break;
            }?>
        </div>
        
        <div class="col-lg-7 col-md-7">
            <div class="help-block"></div>
        </div>
        
    </div>

<?php endforeach; ?>

<!--标签-->
<div class="form-group field-media-tag_ids required">
    <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Tag') . '：', 'field-media-tag_ids', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    <div class="col-lg-7 col-md-7">
        <?= Html::textInput('Media[tag_ids]', !empty($tagsSelected) ? implode(',', $tagsSelected) : null, [
            'id' => 'media-tag_ids', 'class' => 'form-control', 'data-role' => 'tagsinput', 
//            'placeholder' => '请输入至少5个标签'
        ]) ?>
    </div>
    <div class="col-lg-7 col-md-7"><div class="help-block"></div></div>
</div>

<script type="text/javascript">

    /**
     * 验证下拉框是否有选择值
     * @param {Object} _this
     * @returns {undefined}
     */
    function validateAttributeValue(_this)
    {
        if(!_this.parents('div.form-group').hasClass('required')) return;
        
        if(_this.val() == ''){
            var label = _this.parents('div.form-group').find('label.form-label').text();
            var relabel = label.replace('*', "");
            _this.parents('div.form-group').addClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
        }else{
            _this.parents('div.form-group').removeClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html('');
        }
    }
    
    /**
     * 验证复选框是否有选择值
     * @param {Object} _this
     * @returns {undefined}
     */
    var checkBoxVals =  [];
    function validateCheckboxList(_this)
    {
        if(!_this.parents('div.form-group').hasClass('required')) return;
       
        var name = _this.attr('name');
        var checkBoxs = $('input[name="'+name+'"]');
        for(var i in checkBoxs){
            if(checkBoxs[i].checked){
               checkBoxVals.push(checkBoxs[i].value);
            }
        }
        
        if(checkBoxVals.length <= 0 ){
            var label = _this.parents('div.form-group').find('label.form-label').text();
            var relabel = label.replace('*', "");
            _this.parents('div.form-group').addClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
        }else{
            _this.parents('div.form-group').removeClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html('');
        }
        
        checkBoxVals = [];
    }
        
        
    /** 验证标签不少于5个 */
    function validateTags(_this)
    {
        if($('.field-media-tag_ids').find('span.tag').length < 5){
            $('.field-media-tag_ids').addClass('has-error');
            $('.field-media-tag_ids .help-block').html('标签个数不能少于5个');
        }else{
            $('.field-media-tag_ids').removeClass('has-error');
            $('.field-media-tag_ids .help-block').html('');
        }
    }
    
//    $('.bootstrap-tagsinput > input').change(function(){
//        validateTags();
//    });  
  
</script>

<?php
$js = <<<JS
        
      
        
JS;
    //$this->registerJs($js,  View::POS_READY);
?>