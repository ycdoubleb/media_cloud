<?php

use common\models\media\MediaType;
use common\widgets\zTree\zTreeDropDown;
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
                'template' => "{label}\n<div class=\"col-lg-10 col-md-10\" style=\"padding-left: 35px;\">{input}</div>\n",
                'labelOptions' => [
                    'class' => 'col-lg-1 col-md-1 control-label form-label',
                    'style' => 'padding-right: 0px;',
                ],
            ],
        ]);?>

        <div class="col-lg-12 col-md-12 search-panel">
            <!--存储目录-->
            <div class="col-lg-12 col-md-12 search-dir">
                <?= $form->field($searchModel, 'dir_id')->widget(zTreeDropDown::class, [
                    'data' => $dirDataProvider,
                    'url' => [
                        'view' => Url::to(['search-children', 'category_id' => 1]),
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
            </div>
            
            <!--素材类型-->
            <div class="col-lg-12 col-md-12">
                <?= $form->field($searchModel, 'type_id')->checkboxList(MediaType::getMediaByType(), [
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
                ])->label(Yii::t('app', '{Medias}{Type}：', [
                    'Medias' => Yii::t('app', 'Medias'), 'Type' => Yii::t('app', 'Type')
                ])) ?>
            </div>
            <!--属性选项-->
            <div class="col-lg-12 col-md-12">
                <div class="form-group field-mediasearch-attribute_value_id">
                    <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id"
                           style="padding-right: 0px;"><?= Yii::t('app', 'Attribute Option') . '：' ?></label>
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
            <!--关键字-->
            <div class="col-lg-12 col-md-12">
                <div class="form-group field-mediasearch-keyword">
                    <label class="col-lg-1 col-md-1 control-label form-label" style="padding-right: 0px;" for="mediasearch-keyword">
                        <?= Yii::t('app', 'Keyword') . '：' ?>
                    </label>
                    <div class="col-lg-4 col-md-4" style="padding-left: 35px;">
                        <?php
                            $keyword = ArrayHelper::getValue($filters, 'MediaSearch.keyword');
                            echo Html::input('text', 'MediaSearch[keyword]', $keyword, [
                                'id' => 'mediasearch-keyword',
                                'class' => 'form-control',
                                'placeholder' => Yii::t('app', 'Please enter a name or label'),
                            ])
                        ?>
                        <div class="search-icon"><i class="glyphicon glyphicon-search"></i></div>
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
