<?php

use common\models\media\MediaAttribute;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<!--属性-->
<?php foreach ($attrMap as $atts): ?>

    <div class="form-group field-media-attribute_value <?= $atts['is_required'] ? 'required' : '' ?>">
        
        <?= Html::label(($atts['is_required'] ? '<span class="form-must text-danger">*</span>' : null) . $atts['name'] . '：', 
            'field-media-attribute_value', ['class' => 'col-lg-1 col-md-1 control-label form-label', 'style' => 'width: 125px']) ?>
        
        <div class="col-lg-8 col-md-8">
            
            <div class="col-lg-12 col-md-12 clean-padding">
                <?php switch ($atts['input_type']){
                    case MediaAttribute::SINGLE_SELECT_INPUT_TYPE:
                        echo Select2::widget([
                            'id' => "media-attribute_value-{$atts['attr_id']}",
                            'name' => "Media[attribute_value][{$atts['attr_id']}]",
                            'data' => ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'),
                            'value' => !empty($attrSelected) && isset($attrSelected[$atts['attr_id']])? $attrSelected[$atts['attr_id']] : null, 
                            'hideSearch' => true,
                            'options' => [
                                'class' => 'media-attribute_value',
                                'placeholder' => Yii::t('app', 'Select Placeholder')
                            ],
                            'pluginOptions' => ['allowClear' => true],
                            'pluginEvents' => [
                                'change' => 'function(){ validateDepDropdownValue($(this))}'
                            ]
                        ]);
                        break;
                    case MediaAttribute::MULTPLE_SELECT_INPUT_TYPE:
                        echo Html::checkboxList("Media[attribute_value][{$atts['attr_id']}][]", 
                                !empty($attrSelected) ? explode('，', $attrSelected[$atts['attr_id']]) : null, 
                                ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'), 
                                [
                                    'id' => "media-attribute_value-{$atts['attr_id']}",
                                    'class' => 'form-control form-checkbox-control media-attribute_value',
                                    'style' => 'margin-right: -30px;',
                                    'itemOptions'=>[
                                        'onclick' => 'validateCheckboxList($(this))',
                                        'labelOptions'=>[
                                            'style'=>[
                                                'display' => 'inline',
                                                'margin'=>'5px 20px 10px 0px',
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

            <div class="col-lg-12 col-md-12 clean-padding"><div class="help-block"></div></div>
       
        </div>
            
    </div>

<?php endforeach; ?>

<script type="text/javascript">

    /**
     * 验证下拉框是否有选择值
     * @param {Object} _this
     * @returns {undefined}
     */
    function validateDepDropdownValue(_this)
    {
        if(!_this.parents('div.form-group').hasClass('required')) return;
        
        var tagName = _this.prop("tagName").toLowerCase();
        
        if(tagName == 'select'){
            
            if(_this.val() == ''){
                var label = _this.parents('div.form-group').find('label.form-label').text();
                var relabel = label.replace('*', "");
                _this.parents('div.form-group').addClass('has-error');
                _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
            }else{
                _this.parents('div.form-group').removeClass('has-error');
                _this.parents('div.form-group').find('div.help-block').html('');
            }
            
        }else{
            return;
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
        
        var tagName = _this.prop("tagName").toLowerCase();
        
        if(tagName == 'input'){
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
        
        }else{
            return;
        }
    }
    
</script>