<?php

use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\media\searchs\MediaSearch;
use common\widgets\zTree\zTreeAsset;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model MediaSearch */
/* @var $form ActiveForm */

zTreeAsset::register($this);

?>

<div class="media-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    <div class="col-lg-12 col-md-12 panel">
    
        <!--关键字-->
        <?= $form->field($model, 'keyword')->textInput([
            'placeholder' => '请输入素材名称或者标签', 'onchange' => 'submitForm()'
        ])->label(Yii::t('app', 'Keyword') . '：') ?>
        
        <!--存储目录-->
        <div class="form-group field-mediasearch-dir_id">

            <?= Html::label(Yii::t('app', '{Storage}{Dir}：', [
                'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
            ]), 'mediasearch-dir_id', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>

            <div class="col-lg-6 col-md-6">

                <div class="col-lg-12 col-md-12 clean-padding">

                    <div class="zTree-dropdown-container zTree-dropdown-container--krajee">
                        <!-- 模拟select点击框 以及option的text值显示-->
                        <span id="zTree-dropdown-name" class="zTree-dropdown-selection zTree-dropdown-selection--single" onclick="showTree();" >
                            <?php 
                                $dir_id = ArrayHelper::getValue($filters, 'MediaSearch.dir_id');
                                $dirModel = Dir::getDirById($dir_id);
                                if(!empty($dirModel->name)){
                                    echo $dirModel->name;
                                }else{
                                    echo '<span class="zTree-dropdown-selection__placeholder">全部</span>';
                                }
                            ?>
                        </span> 
                        <!-- 模拟select右侧倒三角 -->
                        <i class="zTree-dropdown-selection__arrow"></i>
                        <!-- 存储 模拟select的value值 -->
                        <input id="zTree-dropdown-value" type="hidden" name="MediaSearch[dir_id]" />
                        <!-- zTree树状图 相对定位在其下方 -->
                        <div class="zTree-dropdown-options ztree"  style="display:none;"><ul id="zTree-dropdown"></ul></div>  
                    </div>

                </div>

            </div>

        </div>
        
        <!--素材类型-->
        <?= $form->field($model, 'type_id')->checkboxList(MediaType::getMediaByType(), [
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

        <!--属性选项-->
        <div class="form-group field-mediasearch-attribute_value_id">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-attribute_value_id">属性选项：</label>
            <div class="col-lg-10 col-md-10">
                
                <?php foreach ($attrMap as $atts): ?>
                
                    <?php if($atts['index_type'] > 0): ?>
                        <div id="DepDropdown_<?= $atts['attr_id'] ?>" class="dep-dropdowns">

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
        
        <!--其他选项-->
        <div class="form-group field-mediasearch-other_options">
            <label class="col-lg-1 col-md-1 control-label form-label" for="mediasearch-other_options">其他选项：</label>
            <div class="col-lg-6 col-md-6">
                
                <!--运营者-->
                <div id="DepDropdown_operator" class="dep-dropdowns">
                    <?= $form->field($model, 'owner_id',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Operator')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                <!--上传者-->
                <div id="DepDropdown-uploader" class="dep-dropdowns">
                    <?= $form->field($model, 'created_by',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => $userMap,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Uploader')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
                 <!--状态-->
                <div id="DepDropdown_status" class="dep-dropdowns">
                    <?= $form->field($model, 'status',[
                        'template' => "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>",  
                    ])->widget(Select2::class, [
                        'data' => Media::$statusName,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Status')],
                        'pluginOptions' => ['allowClear' => true],
                        'pluginEvents' => ['change' => 'function(){ submitForm()}']
                    ]) ?>
                </div>
                
            </div>
        </div>
        
    </div>    

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#media-search-form').submit();
    }   
    
    /************************************************************************************
     *
     * 初始化树状下拉
     *
     ************************************************************************************/ 
    //树状图展示
    var treeDataList = <?= json_encode($dirDataProvider) ?>;
    
    //配置
    var treeConfig = {
        view:{},
        edit: {
            enable: false
        },
        //回调
        callback: {
            onClick: zTreeOnClick,
            onExpand: zTreeOnExpand,
        }
    }
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        zTreeDropdown('zTree-dropdown', 'zTree-dropdown-name', 'zTree-dropdown-value', treeConfig, treeDataList);
        
    }
    
    //节点点击事件
    function zTreeOnClick(event, treeId, treeNode) {
        $('#'+treeName).html(treeNode.name);
        $('#'+treeValue).val(treeNode.id);
        submitForm();
        hideTree();  
    };
    
    //点击展开项, 添加节点  第一次展开的时候
    function zTreeOnExpand(event,treeId, treeNode) {   
        var treeObj = $.fn.zTree.getZTreeObj(treeId);
        var parentZNode = treeObj.getNodeByParam("id", treeNode.id, null);//获取指定父节点
        var childNodes = treeObj.transformToArray(treeNode);//获取子节点集合
        //childNodes.length 小于等于1，就加载(第一次加载)
        if(childNodes.length <= 1){
            $.get('/media_config/dir/search-children?id=' + treeNode.id, function(response){
                if(response.data.length > 0){
                    treeObj.addNodes(parentZNode, response.data, false);     //添加节点     
                }
            });
        }
    } 
    
</script>