<?php

use common\models\media\Dir;
use common\models\media\MediaType;
use common\utils\I18NUitl;
use common\widgets\zTree\zTreeDropDown;
use frontend\modules\media_library\searchs\MediaSearch;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $searchModel MediaSearch  */

$currentDir = Dir::getDirById($searchModel->dir_id);
$dirSelectIds = $currentDir == null ? [0] : explode(',', $currentDir->path);
$dirLevels = $dirDatas['dirLevels'];
$dirCounts = $dirDatas['dirCounts'];

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
            
            <!-- 目录 -->
            <div class="col-lg-12 col-md-12 dir-box">
                <?php foreach ($dirLevels as $index => $dirs): ?>
                    <div class="filter-row form-group">
                        <label class="filter-label col-lg-1 col-md-1 control-label form-label"><?= $index == 0 ? "课程：" : "" ?></label>
                        
                        <div class="filter-control col-lg-10 col-md-10">
                            <?php
                            /*
                             * 什么时候显示选择【全部】？
                             * 可通过获取当前已选择分类的path获取分类的父级路径，
                             * 如 当前已选择A分类，A分类上是顶级分类，A下面还有一级分类，那么$dirLevels的长度为3，而A.path的长度为3(0,2,26)
                             * 所以A显示选中状态，而A下级的分类即显示选择【全部】
                             * 
                             * 只有最后一排分类才需要考虑是否显示选择【全部】 
                             */
                            $all_active = ($index == count($dirLevels) - 1 && count($dirLevels) >= count($dirSelectIds));
                            
                            ?>
                            <a href="javascript:" onclick="searchF(<?= $dirSelectIds[$index]; ?>)" class="filter-item filter-all <?= $all_active ? 'active' : '' ?>">全部</a>
                            <?php foreach ($dirs as $cid => $cname): ?>
                                <?php 
                                    //当前选择样式
                                    $activedClass = (isset($dirSelectIds[$index + 1]) && $cid == $dirSelectIds[$index + 1]) ? 'active' : '';
                                    //是否禁用样式，目录下没有媒体时显示禁用
                                    $disabledClass = (isset($dirCounts[$cid]) && ($dirCounts[$cid] > 0)) ? '' : 'disabled';
                                ?>
                                <a href="javascript:" onclick="searchF(<?= $cid ?>)" 
                                   class="filter-item <?= $activedClass ?> <?= $disabledClass ?>"><?= $cname ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?= Html::activeHiddenInput($searchModel, 'dir_id') ?>
            </div>
            
            
            <!--素材类型-->
            <div class="col-lg-12 col-md-12">
                <?= $form->field($searchModel, 'type_id', [
                    'template' => "{label}\n<div class=\"col-lg-6 col-md-6 \" style=\"padding-left: 32px;\">"
                    . "<span class=\"selectall\" onclick=\"selectall();\">全选</span>{input}</div>", 
                ])->checkboxList(MediaType::getMediaByType(), [
                    'style' => 'display: inline-block;',
                    'itemOptions'=>[
                        'class' => 'pull-left',
                        'onclick' => 'submitForm();',
                        'labelOptions'=>[
                            'class' => 'checkbox-list-label',
                        ]
                    ],
                ])->label(I18NUitl::t('app', '{Medias}{Type}：')) ?>
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
                                        'hideSearch' => false,
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
                                'onchange' => 'submitForm()'
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

<script>
    /**
     * 目录分类改变 
     **/
    function searchF(value){
        $('#mediasearch-dir_id').val(value);
        submitForm();
    }
    
    // 全选
    function selectall(){
        var selected = 0;
            checkboxs = $('#mediasearch-type_id').find('input[type="checkbox"]'),
            total = checkboxs.length;   //复选框总数
        // 复选框选中的个数
        checkboxs.each(function(){
            if($(this).is(':checked')){
                selected++;
            }
        });
        if(total === selected){
            $('#mediasearch-type_id').find('input[type="checkbox"]').prop('checked', false);
            submitForm(1000);
        }else{
            $('#mediasearch-type_id').find('input[type="checkbox"]').prop('checked', true);
            submitForm();
        }
    }
</script>

<?php
$js = <<<JS
    // 搜索图标的点击事件
    $(".search-icon").click(function(){
        submitForm();
    })
        
    // 定时器
    var set_timeout = null;
        
    /**
     * 提交表单
     */
    window.submitForm = function(timeout){
        clearTimeout(set_timeout);
        if(timeout == undefined || timeout == null){
            timeout = 100;
        }
        set_timeout = setTimeout(function(){
            $('#media-form').submit();
        }, timeout);
    }
JS;
    $this->registerJs($js,  View::POS_READY);
?>
