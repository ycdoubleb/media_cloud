<?php

use common\widgets\watermark\WatermarkAsset;
use yii\helpers\Html;
use yii\web\View;

WatermarkAsset::register($this);

// 加载 ITEM_DOM 模板 
$water_item_dom = '<div class="media-watermark">';
$water_item_dom .= Html::checkbox('Media[mts_watermark_ids][]', false, ['value' => '{%id%}']);
$water_item_dom .= Html::img('{%path%}', ['width' => 64, 'height' => 40]);
$water_item_dom .= '</div>';
$item_dom = json_encode($water_item_dom);

// 判断是否为新建
$isNewRecord = $isNewRecord;
// 所有水印文件
$wateFiles = json_encode($wateFiles);
// 已选中的水印
$wateSelected = json_encode($wateSelected);

?>

<!--转码-->
<?php if($isNewRecord): ?>
    <div class="form-group field-media-mts_need">
        <?= Html::label(Yii::t('app', 'Transcoding') . '：', 'field-media-mts_need', [
            'class' => 'col-lg-1 col-md-1 control-label form-label'
        ]) ?>
        <div class="col-lg-7 col-md-7">
            <?= Html::radioList('Media[mts_need]', $isNewRecord, [1 => '自动', 0 => '手动'],[
                'itemOptions'=>[
                    'labelOptions'=>[
                        'style'=>[
                            'margin'=>'10px 15px 10px 0',
                            'color' => '#999999',
                            'font-weight' => 'normal',
                        ]
                    ]
                ],
            ]) ?>
        </div>
        <div class="col-lg-7 col-md-7"><div class="help-block"></div></div>
    </div>
<?php endif; ?>

<!--水印-->
<div class="form-group field-media-mts_watermark_ids">
    <?= Html::label(Yii::t('app', 'Watermark') . '：', 'field-media-mts_watermark_ids', [
        'class' => 'col-lg-1 col-md-1 control-label form-label'
    ]) ?>
    <div class="col-lg-7 col-md-7">
        <div id="media-mts_watermark_ids">
            <!--加载-->
            <div class="loading-box"><span class="loading"></span></div>
        </div>
        <br/>
        <!--预览-->
        <div id="preview-watermark" class="preview"></div>
    </div>
</div>

<script type="text/javascript">

    var watermark;
    var wateFiles = <?= $wateFiles ?>;                 // 水印文件
    var isNewRecord = <?= $isNewRecord ?>;             // 获取flash上传组件路径
    var wateSelected = <?= $wateSelected ?>;           // 已选中的水印
    var item_dom = <?= $item_dom ?>;                   // 加载 ITEM_DOM 模板 
    var isPageLoading = false;                          // 取消加载Loading状态
    
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        initWatermark();        //初始水印
    }
            
    //初始化组件        
    function initWatermark(){
        watermark = new wate.Watermark({container: '#preview-watermark'});
        /** 显示客户下已启用的水印图 */
        $.each(wateFiles, function(){
            if(!isPageLoading) $('#media-mts_watermark_ids').html('');
            //创建情况下显示默认选中，更新情况下如果id存在已选的水印里则this.is_selected = true，否则不显示选中
            if(!isNewRecord){
                this.is_selected = $.inArray(this.id, wateSelected) != -1 ? 1 : 0;
            }
            var wate = $(Wskeee.StringUtil.renderDOM(item_dom, this)).appendTo($('#media-mts_watermark_ids'));
            wate.find('input').attr('name', 'Media[mts_watermark_ids][]').prop('checked', parseInt(this.is_selected));
            //check 更改后通知preview显示更改
            wate.find('input').on('change',function(){
                checkedWatermark($(this));
            });
            //如果是默认选中，则在预览图上添加该选中的水印
            if(parseInt(this.is_selected)) watermark.addWatermark('vkcw' + this.id, this);
            isPageLoading = true;
        });
    }
    
    /**
     * 选中水印图
     * @param {object} _this
     * @returns {undefined}
     */
    function checkedWatermark(_this){
        /* 判断用户是否有选中水印图，如果选中，则添加水印，否则删除水印 */
        if($(_this).is(":checked")){
            $.each(wateFiles, function(){
                //如果水印的id等于用户选中的值，则在预览图上添加水印
                if(this.id == $(_this).val()){
                    watermark.addWatermark('vkcw' + this.id, this);
                    return false;
                }
            });
        }else{
            watermark.removeWatermark('vkcw' + $(_this).val());
        }
    }

</script>