<?php

use common\models\media\MediaAttribute;
use common\widgets\tagsinput\TagsInputAsset;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

TagsInputAsset::register($this);

?>

<!--标签-->
<div class="form-group field-media-tag_ids <?= isset($isTagRequired) && $isTagRequired ? 'required' : '' ?>">
    <?php 
        $for = 'field-media-tag_id';
        $options = ['class' => 'col-lg-1 col-md-1 control-label form-label', 'style' => 'width: 125px'];
        
        if(isset($isTagRequired) && $isTagRequired){
            echo Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Medias}{Tags}：', [
                'Medias' => Yii::t('app', 'Medias'), 'Tags' => Yii::t('app', 'Tags')
            ]), $for, $options);
        }else{
            echo Html::label(Yii::t('app', 'General Purpose Label') . '：', $for, $options);
        }
        
    ?>
    
    <div class="col-lg-8 col-md-8">
        <div class="col-lg-12 col-md-12 clean-padding">
            <?= Html::textInput('Media[tags]', !empty($tagsSelected) ? $tagsSelected : null, [
                'id' => 'media-tag_ids', 'class' => 'form-control media-tag_id', 'data-role' => 'tagsinput', 
                'onchange' => 'validateTags($(this))', 
                'placeholder' => isset($isTagRequired) && $isTagRequired ? null : Yii::t('app', 'Material Common label (Can be ignored)')
            ]) ?>
        </div>
        <div class="col-lg-12 col-md-12 clean-padding"><div class="help-block"></div></div>
    </div>
    
</div>

<script type="text/javascript">

    /**
     * 验证标签是否有值和标签个数的多少
     * @param {Object} _this
     * @returns {undefined}
     */
    function validateTags(_this)
    {
        if(!_this.parents('div.form-group').hasClass('required')) return;
        
        var tagName = _this.prop("tagName").toLowerCase();
        
        if(tagName == 'input'){
        
            var tags = _this.prev('div.bootstrap-tagsinput').find('span.tag');

            if(tags.length < 5){
                var label = _this.parents('div.form-group').find('label.form-label').text();
                var relabel = label.replace('*', "");
                _this.parents('div.form-group').addClass('has-error');
                if(tags.length <= 0){
                    _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
                }else{
                    _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '个数不能少于5个。');
                }
            }else{
                _this.parents('div.form-group').removeClass('has-error');
                _this.parents('div.form-group').find('div.help-block').html('');
            }
        }else{
            return;
        }
    }
  
</script>