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
                <div class="form-group field-mediasearch-keyword">
                    <label class="col-lg-6 col-md-6 control-label form-label" for="mediasearch-keyword"></label>
                    <div class="col-lg-6 col-md-6" style="padding-left: 0;">
                        <?php
                            $keyword = ArrayHelper::getValue($filters, 'MediaSearch.keyword');
                            echo kartik\helpers\Html::input('text', 'MediaSearch[keyword]', $keyword, [
                                'id' => 'mediasearch-keyword',
                                'class' => 'form-control',
                                'placeholder' => '请输入媒体名称或者标签',
                            ])
                        ?>
                        <div class="search-icon"><i class="glyphicon glyphicon-search"></i></div>
                    </div>
                </div>
            </div>
            <!--属性选项-->
            <div class="col-lg-12 col-md-12">
                <div class="form-group field-mediasearch-attribute_value_id">
                    <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id" style="padding-right: 0px;">
                        属性选项：</label>
                    <div class="col-lg-10 col-md-10" style="padding-left: 35px;">
                        <?php foreach ($attrMap as $atts): ?>
                
                            <?php if($atts['index_type'] > 0): ?>
                                <div id="DepDropdown_<?= $atts['attr_id'] ?>" class="dep-dropdown">

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
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
    // 搜索图标的点击事件
    $(".search-icon").click(function(){
        submitForm();
    })
        
    /**
     * 提交表单
     */
    window.submitForm = function(){
        $('#media-form').submit();
    }
JS;
    $this->registerJs($js,  View::POS_READY);
?>