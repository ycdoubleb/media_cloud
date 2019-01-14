<?php

use common\models\media\MediaType;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;

?>

<div class="header">
    <div class="media-form container mc-form">
        <?php
        $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'id' => 'media-form',
                'class' => 'form-horizontal',
            ],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-9 col-md-9\" style=\"padding-left: 0;\">{input}</div>\n",
                'labelOptions' => [
                    'class' => 'col-lg-3 col-md-3 control-label form-label',
                    'style' => 'text-align: center;',
                ],
            ],
        ]);?>

        <div class="col-lg-12 col-md-12 search-panel">
            <!--媒体类型-->
            <div class="col-lg-6 col-md-6" style="padding-left: 3px;">
                <?= $form->field($searchModel, 'type_id')->checkboxList(MediaType::getMediaByType(), [
//                    'value' => ArrayHelper::getValue($filters, 'CourseSearch.level', ''),
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
            </div>
            <!--关键字-->
            <div class="col-lg-6 col-md-6">
                <?= $form->field($searchModel, 'keyword')->textInput([
                    'placeholder' => '请输入媒体名称或者标签',
                    'onchange' => 'submitForm();',
                ])->label(Yii::t('app', 'Keyword') . '：') ?>
            </div>
            <!--属性选项-->
            <div class="col-lg-12 col-md-12">
                <div class="form-group field-mediasearch-attribute_value_id">
                    <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id" style="padding-right: 0px;">
                        属性选项：</label>
                    <div class="col-lg-10 col-md-10" style="padding-left: 35px;">
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
                                if($attr['index_type'] > 0){
                                    echo "<div id='DepDropdown_{$attr['id']}' . class='dep-dropdown'>";
                                    echo Select2::widget([
                                        'id' => "attribute_value_{$attr['id']}",
                                        'name' => 'MediaSearch[attribute_value_id][]',
                                        'value' => ArrayHelper::getValue($filters, 'MediaSearch.attribute_value_id'),
                                        'data' => $attrValMap[$attr['id']],
                                        'hideSearch' => true,
                                        'options' => ['placeholder' => $attr['name']],
                                        'pluginOptions' => ['allowClear' => true],
                                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                                    ]);                       
                                    echo '</div>';
                                }
                            } 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
    /**
     * 提交表单
     */
    window.submitForm = function(){
        $('#media-form').submit();
    }
JS;
    $this->registerJs($js,  View::POS_READY);
?>