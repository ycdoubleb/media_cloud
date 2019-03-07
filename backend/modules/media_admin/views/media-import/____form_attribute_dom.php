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
        
        <div class="col-lg-8 col-md-8">
            
            <div class="col-lg-12 col-md-12 clean-padding">
                <?php switch ($atts['input_type']){
                    case MediaAttribute::SINGLE_SELECT_INPUT_TYPE:
                        echo Select2::widget([
                            'id' => "media-attribute_value-{$atts['attr_id']}",
                            'name' => "Media[attribute_value][{$atts['attr_id']}]",
                            'data' => ArrayHelper::map($atts['childrens'], 'attr_val_id', 'attr_val_value'),
                            'value' => null, 
                            'hideSearch' => true,
                            'options' => [
                                'class' => 'media-attribute_value',
                                'placeholder' => Yii::t('app', 'All')
                            ],
                            'pluginOptions' => ['allowClear' => true],
                            'pluginEvents' => [
                                'change' => 'function(){ validateDepDropdownValue($(this))}'
                            ]
                        ]);
                        break;
                    case MediaAttribute::MULTPLE_SELECT_INPUT_TYPE:
                        echo Html::checkboxList("Media[attribute_value][{$atts['attr_id']}][]", null, 
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

<!--标签-->
<div class="form-group field-media-tag_ids">
    <?= Html::label(Yii::t('app', '{Public}{Tag}：', [
        'Public' => Yii::t('app', 'Public'), 'Tag' => Yii::t('app', 'Tag')
    ]), 'field-media-tag_id', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
    
    <div class="col-lg-8 col-md-8">
        <div class="col-lg-12 col-md-12 clean-padding">
            <?= Html::textInput('Media[tags]', null, [
                'id' => 'media-tag_ids', 'class' => 'form-control media-tags', 'data-role' => 'tagsinput',
                'placeholder' => '素材公用标签（可以不填）'
            ]) ?>
        </div>
        <div class="col-lg-12 col-md-12 clean-padding"><div class="help-block"></div></div>
    </div>
    
</div>

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
  
    /**
     * 提交时验证
     * @returns {undefined}
     */
    function submitValidate()
    {
        $('div.form-group').find('.media-attribute_value').each(function(){
            validateDepDropdownValue($(this));
            validateCheckboxList($(this).find('input'));
        });
    }
  
</script>