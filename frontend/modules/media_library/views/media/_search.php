<?php

use common\models\media\Dir;
use common\models\media\MediaType;
use common\widgets\depdropdown\DepDropdown;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
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
            <div class="col-lg-12 col-md-12" style="padding-left: 0px;">
                <!--存储目录-->
                <div class="col-lg-6 col-md-6 search-dir" style="padding-left: 3px;">
                    <?= $form->field($searchModel, 'dir_id', [
                        'template' => "{label}\n<div class=\"col-lg-9 col-md-9\" style=\"padding-left: 0px; margin-left: -3px\">{input}</div>",
                    ])->widget(DepDropdown::class,[
                        'pluginOptions' => [
                            'url' => Url::to(['search-children']),
                            'max_level' => 10,
                            'onChangeEvent' => new JsExpression('function(){ submitForm()}')
                        ],
                        'items' => Dir::getDirsBySameLevel($searchModel->dir_id, Yii::$app->user->id, true, true),
                        'values' => $searchModel->dir_id == 0 ? [] : array_values(array_filter(explode(',', 
                                Dir::getDirById($searchModel->dir_id)->path))),
                        'itemOptions' => [
                            'style' => 'width: 155px; padding: 8px 10px',
                        ],
                    ])->label('存储目录：') ?>
                </div>
                <!--关键字-->
                <div class="col-lg-6 col-md-6">
                    <div class="form-group field-mediasearch-keyword">
                        <label class="col-lg-6 col-md-6 control-label form-label" for="mediasearch-keyword"></label>
                        <div class="col-lg-6 col-md-6" style="padding-left: 0;">
                            <?php
                                $keyword = ArrayHelper::getValue($filters, 'MediaSearch.keyword');
                                echo Html::input('text', 'MediaSearch[keyword]', $keyword, [
                                    'id' => 'mediasearch-keyword',
                                    'class' => 'form-control',
                                    'placeholder' => '请输入素材名称或者标签',
                                ])
                            ?>
                            <div class="search-icon"><i class="glyphicon glyphicon-search"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--素材类型-->
            <div class="col-lg-6 col-md-6" style="padding-left: 3px;">
                <?= $form->field($searchModel, 'type_id')->checkboxList(MediaType::getMediaByType(), [
                    'value' => ArrayHelper::getValue($filters, 'MediaSearch.type_id', [1, 2, 3, 4]),
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
            
            <!--属性选项-->
            <div class="col-lg-12 col-md-12">
                <div class="form-group field-mediasearch-attribute_value_id">
                    <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id"
                           style="padding-right: 0px;">属性选项：</label>
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
            <!--选项卡 显示列表or图表-->
            <?= Html::hiddenInput('pages', $pages, '')?>
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