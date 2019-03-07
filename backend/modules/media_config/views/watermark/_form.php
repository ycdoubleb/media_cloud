<?php

use common\components\aliyuncs\Aliyun;
use common\models\Watermark;
use common\widgets\watermark\WatermarkAsset;
use common\widgets\webuploader\ImagePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Watermark */
/* @var $form ActiveForm */

WatermarkAsset::register($this);

// 大小
$sizeSelected = $model->width > 1 || $model->height > 1 ? 1 : 0;
// 偏移
$shiftSelected = $model->isNewRecord || ($model->dx > 1 || $model->dy > 1) ? 1 : 0;
// 设置偏移默认值
$model->dx = $model->isNewRecord ? 10 : $model->dx;
$model->dy = $model->isNewRecord ? 10 : $model->dy;
//水印图路径
$path = !$model->isNewRecord ? $model->url : '';

?>

<div class="watermark-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'watermark-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-7 col-md-7\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>

    

    <!--水印名称-->
    <?= $form->field($model, 'name')->textInput([
        'placeholder' => '请输入水印名称', 'maxlength' => true
    ])->label(Yii::t('app', '{Watermark}{Name}', [
        'Watermark' => Yii::t('app', 'Watermark'), 'Name' => Yii::t('app', 'Name')
    ])) ?>

    <!--水印位置-->
    <?= $form->field($model, 'refer_pos')->radioList(Watermark::$referPosMap, [
        'itemOptions'=>[
            'labelOptions'=>[
                'style'=>[
                    'margin'=>'5px 29px 10px 0px',
                    'color' => '#666666',
                    'font-weight' => 'normal',
                ]
            ],
            'onchange' => 'changeRefer_pos()'
        ],
    ])->label(Yii::t('app', '{Watermark}{Position}', [
        'Watermark' => Yii::t('app', 'Watermark'), 'Position' => Yii::t('app', 'Position')
    ])) ?>

    <!--宽-->
    <?php 
        //下拉选择
        $downList = Html::dropDownList(null, $sizeSelected, ['百分比', '像素'], [
            'class' => 'form-control', 'onchange' => 'changeInputMode($(this))'
        ]);
        echo $form->field($model, 'width', [
            'template' => "{label}\n<div class=\"col-lg-3 col-md-3\" style=\"padding-right: 0px;\">{input}</div>"
                . "<div class=\"clear-padding pull-left\">{$downList}</div>\n"
                . "<div class=\"col-lg-7 col-md-7\">{error}</div>",
        ])->textInput([
            'type' => 'number', 'min' => $sizeSelected ? 8 : 0, 'max' => $sizeSelected ? 4096 : 1, 
            'step' => $sizeSelected ? 1 : 0.01, 'onchange' => 'changeRefer_pos()',
        ]);
    ?>

    <!--高-->
    <?php
        //下拉选择
        $downList = Html::dropDownList(null, $sizeSelected, ['百分比', '像素'], [
            'class' => 'form-control', 'onchange' => 'changeInputMode($(this))'
        ]);
        echo $form->field($model, 'height', [
            'template' => "{label}\n<div class=\"col-lg-3 col-md-3\" style=\"padding-right: 0px;\">{input}</div>"
                . "<div class=\"clear-padding pull-left\">{$downList}</div>\n"
                . "<div class=\"col-lg-7 col-md-7\">{error}</div>",
        ])->textInput([
            'type' => 'number', 'min' => $sizeSelected ? 8 : 0, 'max' => $sizeSelected ? 4096 : 1, 
            'step' => $sizeSelected ? 1 : 0.01, 'onchange' => 'changeRefer_pos()',
        ]);
    ?>

    <!--水平偏移-->
    <?php
        //下拉选择
        $downList = Html::dropDownList(null, $shiftSelected, ['百分比', '像素'], [
            'class' => 'form-control', 'onchange' => 'changeInputMode($(this))'
        ]);
        echo $form->field($model, 'dx', [
            'template' => "{label}\n<div class=\"col-lg-3 col-md-3\" style=\"padding-right: 0px;\">{input}</div>"
                . "<div class=\"clear-padding pull-left\">{$downList}</div>\n"
                . "<div class=\"col-lg-7 col-md-7\">{error}</div>",
        ])->textInput([
            'type' => 'number', 'value' => $model->dx, 
            'min' => $shiftSelected ? 8 : 0, 'max' => $shiftSelected ? 4096 : 1, 
            'step' => $shiftSelected ? 1 : 0.01, 'onchange' => 'changeRefer_pos()',
        ])->label(Yii::t('app', '{Level}{Shifting}', [
            'Level' => Yii::t('app', 'Level'), 'Shifting' => Yii::t('app', 'Shifting')
        ]));
    ?>

    <!--垂直偏移-->
    <?php 
        //下拉选择
        $downList = Html::dropDownList(null, $shiftSelected, ['百分比', '像素'], [
            'class' => 'form-control', 'onchange' => 'changeInputMode($(this))'
        ]);
        echo $form->field($model, 'dy', [
            'template' => "{label}\n<div class=\"col-lg-3 col-md-3\" style=\"padding-right: 0px;\">{input}</div>"
                . "<div class=\"clear-padding pull-left\">{$downList}</div>\n"
                . "<div class=\"col-lg-7 col-md-7\">{error}</div>",
        ])->textInput([
            'type' => 'number', 'value' => $model->dy, 
            'min' => $shiftSelected ? 8 : 0, 'max' => $shiftSelected ? 4096 : 1, 
            'step' => $shiftSelected ? 1 : 0.01, 'onchange' => 'changeRefer_pos()',
        ])->label(Yii::t('app', '{Vertical}{Shifting}', [
            'Vertical' => Yii::t('app', 'Vertical'), 'Shifting' => Yii::t('app', 'Shifting')
        ])); 
    ?>

    <!--水印文件-->
    <?= $form->field($model, 'url')->widget(ImagePicker::class, [
        'id' => 'watermark-url',
        'pluginOptions' =>[
            'fileSingleSizeLimit' => 1*1024*1024,
            //设置允许选择的文件类型
            'accept' => [
                'mimeTypes' => 'image/png',
            ],
        ],
        'pluginEvents' => [
            'uploadComplete' => 'function(evt, data){uploadComplete(data)}',
            'fileDequeued' => 'function(evt, file){fileDequeued()}'
	]
    ])->label(Yii::t('app', '{Watermark}{File}', [
        'Watermark' => Yii::t('app', 'Watermark'), 'File' => Yii::t('app', 'File')
    ]));?>

    <!--oss_key-->
    <?= Html::activeHiddenInput($model, 'oss_key', ['value' => $model->isNewRecord ? '' : $model->oss_key]) ?>
    
    <!--是否选中-->
    <?= $form->field($model, 'is_selected')->checkbox(['value' => 1, 'style' => 'margin-top: 14px'], false)->label(Yii::t('app', 'Is Selected')) ?>

    <!--预览-->
    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Preview'), 'watermark-preview', [
            'class' => 'col-lg-1 col-md-1 control-label form-label'
        ]) ?>
        <div class="col-lg-7 col-md-7">
            <div id="preview-watermark"></div>
        </div>
    </div>
            
    <div class="form-group">
        <label class="col-lg-1 col-md-1 control-label form-label"></label>
        <div class="col-lg-1 col-md-1">
            <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 
                'class' => 'btn btn-success btn-flat', 'onclick' => 'submitForm()']) ?>
        </div> 
    </div>
    
    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    
    var watermark;
    var path = "<?= $path ?>";
    var pos = "<?= $model->refer_pos ?>";
    var w = "<?= $model->width ?>";
    var h = "<?= $model->height ?>";
    var dx = "<?= $model->dx ?>";
    var dy = "<?= $model->dy ?>";
               
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        initWatermark();        //初始水印
    }
    
    /**
     * 提交表单
     * @returns {undefined}
     */
    function submitForm(){
        $('#watermark-form').submit();
    }
            
    //初始化组件        
    function initWatermark(){
        watermark = new wate.Watermark();
       
        //添加一个水印
        watermark.addWatermark({
            refer_pos: pos, url: path,
            width: w, height: h, 
            dx: dx, dy: dy
        });
    }        
                
    /**
     * 上传完成
     * @param {json} data
     * @returns {undefined}
     */
    function uploadComplete(data){
        $('#watermark-oss_key').val(data['oss_key']);
        path = data['url'];
        changeRefer_pos();
    }    
    
    /**
     * 删除水印
     * @returns {undefined}
     */
    function fileDequeued(){
        path = '';
        changeRefer_pos();
    }
                
    /**
     * 变更数值，更改对应参数
     * @returns {undefined}
     */
    function changeRefer_pos (){
        var pos = $('input[name="Watermark[refer_pos]"]:checked').val(), 
            w = $('input[name="Watermark[width]"]'),
            h = $('input[name="Watermark[height]"]'),
            dx = $('input[name="Watermark[dx]'),
            dy = $('input[name="Watermark[dy]');
    
        w.val(valuableNumber(w.val(), w.attr('max'), w.attr('min')));
        h.val(valuableNumber(h.val(), h.attr('max'), h.attr('min')));
        dx.val(valuableNumber(dx.val(), dx.attr('max'), dx.attr('min')));
        dy.val(valuableNumber(dy.val(), dy.attr('max'), dy.attr('min')));
        
        watermark.updateWatermark({
            refer_pos: pos, url: path,
            width: w.val(), height: h.val(), 
            dx: dx.val(), dy: dy.val()
        });
    }
        
    /**
     * 更换输入方式
     * @param {obj} _this    触发事件的对象
     * @returns {undefined}
     */
    function changeInputMode(_this){
        var inputMode = _this.parent().prev().children();
        if(_this.find("option:selected").val() == 1){
            $(inputMode).attr({min: 8, max: 4096, step: 1});
            $(inputMode).val(8);
        }else{
            $(inputMode).attr({min: 0, max: 1, step: 0.01});
            $(inputMode).val(0);
        }
        changeRefer_pos();
   }
   
    /**
     * 验证数字
     * @param {Number} value    验证的数值
     * @param {Number} max      最大
     * @param {Number} min      最小
     * @return {Number|@var;value}
     */
    function valuableNumber (value, max, min){
        value = Number(value);  //转为数字
        max = Number(max);  //转为数字
        min = Number(min);  //转为数字
        if (value > max) {
            value = max;
        } else if(value < min) {
            value = min;
        }
        
        return value;
    }
    
</script>