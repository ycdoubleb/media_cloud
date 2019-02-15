<?php

use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\media_library\assets\ModuleAssets;
use kartik\growl\GrowlAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

MainAssets::register($this);
ModuleAssets::register($this);
GrowlAsset::register($this);

$this->title = Yii::t('app', '{Media}{Online}',[
    'Media' => Yii::t('app', 'Media'),
    'Online' => Yii::t('app', 'Online')
]);

$pages = ArrayHelper::getValue($filters, 'pages', 'list');   //表格显示

?>

<div class="media_library mediacloud">
    <!--页头搜索-->
    <?= $this->render('_search',[
        'searchModel' => $searchModel,
        'attrMap' => $attrMap,
        'filters' => $filters,
        'pages' => $pages,
    ])?>
    <!--内容部分-->
    <div class="container content">
        <div class="media-index common">
            <!--按钮-->
            <div class="mc-title clear-margin">
                <div class="result-num">结果列表（<span>共查询到 <?= $totalCount; ?> 个媒体</span>）</div>
                <div class="btngroup pull-right">
                    <?php
                        echo Html::a('加入购物车', 'javascript:;', ['id' => 'add-carts',
                            'data-url' => Url::to(['add-carts']),
                            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '加入购物车']);
                        echo Html::a('立即购买', 'javascript:;', ['id' => 'buys',
                            'data-url' => Url::to(['checking-order']),
                            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '立即购买']);
                        echo Html::a('<i class="glyphicon glyphicon-th-list"></i>', 
                            array_merge(['index'], array_merge($filters, ['pages' => 'list'])),
                                ['id' => 'list', 'title' => '列表显示']);
                        echo Html::a('<i class="glyphicon glyphicon-th-large"></i>',
                            array_merge(['index'], array_merge($filters, ['pages' => 'chart'])),
                                ['id' => 'chart', 'title' => '图表显示']);
                    ?>
                </div>
            </div>
            <!--列表or图表-->
            <div class="mc-panel set-bottom">
                <?php if ($pages == 'list') {?>
                    <!--列表显示-->
                    <div class="meida-table">
                        <table class="table table-bordered table-striped mc-table">
                            <thead>
                                <tr>
                                    <th style="width: 30px"><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th>
                                    <th style="width: 90px">预览</th>
                                    <th style="width: 75px">媒体编号</th>
                                    <th style="width: 160px">媒体名称</th>
                                    <th style="width: 160px">存储目录</th>
                                    <th style="width: 50px">类型</th>
                                    <th style="width: 75px">媒体价格</th>
                                    <th style="width: 65px">时长</th>
                                    <th style="width: 75px">大小</th>
                                    <th>标签</th>
                                    <th style="width: 75px">操作</th>
                                </tr>
                            </thead>
                            <tbody class="meida-details">

                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <!--图表显示-->
                    <div class="meida-details">

                    </div>
                <?php } ?>
            </div>
            <!--加载-->
            <div class="loading-box">
                <span class="loading" style="display: none"></span>
                <span class="no_more" style="display: none">没有更多了</span>
            </div>

            <!--总结记录-->
            <div class="summary set-bottom">
                <span>共 <b><?= $totalCount; ?></b> 条记录</span>
            </div>
        </div>
    </div>
</div>

<?php
$params_js = json_encode($filters); //js参数
//加载 DOM 模板
if ($pages == 'list') {
    $renderView = '____list';
} else {
    $renderView = '____chart';
}
$details_dom = json_encode(str_replace(array("\r\n", "\r", "\n"), " ", 
$this->renderFile("@frontend/modules/media_library/views/media/$renderView.php")));

$js = <<<JS
    //选中效果
    $(".mc-title .btngroup a[id=$pages]").addClass('active'); 
        
    /**
     * 滚屏自动换页
     */
    var page = 0; //页数
    var isPageLoading = false;
    $(window).scroll(function(){
        if($(document).scrollTop() >= $(document).height() - $(window).height() - 300){
            loaddata(page, '/media_library/media/media-data');
        }
    });
        
    //加载第一页的课程数据
    loaddata(page, '/media_library/media/media-data');
    /**
     * 加载数据
     * @param int target_page 指定页
     * @param string url 指定的链接
     */
    function loaddata (target_page, url) {
        var maxPageNum =  $totalCount / 10;
        // 当前页数是否大于最大页数
        if(target_page >= Math.ceil(maxPageNum)){
            $('.loading-box .loading').hide();
            $('.loading-box .no_more').show();
            return;
        }
        /**
         * 如果页面非加载当中执行
         */
        if(!isPageLoading){
            isPageLoading = true;   //设置已经加载当中...
            var params = $.extend($params_js, {page: (target_page + 1)});  //传值
            $.get(url, params, function(rel){
                isPageLoading = false;      //取消设置加载当中...
                var data = rel.data;        //获取返回的数据
                page = Number(data.page);   //当前页
                //请求成功返回数据，否则提示错误信息
                if(rel['code'] == '0'){
                    for(var i in data.result){
                        var item = $(Wskeee.StringUtil.renderDOM($details_dom, data.result[i])).appendTo($(".meida-details"));
                        //鼠标经过、离开事件
                        item.hover(function(){
                            $(this).addClass('hover');
                            $(this).find(".checkbox").removeClass('hidden');
                        }, function(){
                            $(this).removeClass('hover');
                            if(!$(this).find(".checkbox").is(':checked')){  //选中时不再隐藏
                                $(this).find(".checkbox").addClass('hidden');
                            }
                        });
                        //图表显示时
                        item.find('.checkdiv').click(function(){
                            if($(this).find(".checkbox").is(':checked')){
                                $(this).find(".checkbox").prop("checked",false);
                            }else{
                                $(this).find(".checkbox").prop("checked",true);
                            }
                        });
                        //点击复选框事件
                        item.find('input[name="selection[]"]').click(function(evt){
                            evt.stopPropagation();  //阻止事件传递
                            var selected = 0;
                                checkboxs = $('input[name="selection[]"]'),
                                total = checkboxs.length;   //复选框总数
                            // 复选框选中的个数
                            checkboxs.each(function(){
                                if($(this).is(':checked')){
                                    selected++
                                }
                            });
                            if(total == selected){
                                $('input[name="selection_all"]').prop("checked",true);
                            }else{
                                $('input[name="selection_all"]').prop("checked",false);
                            }
                        });
                        //加入购物车
                        item.find('.add-cart').click(function(){
                            var url = $(this).attr('data-url'),
                                ids = $(this).attr('data-id');
                            $.post(url, {ids}, function(rel){
                                window.location.reload();  //刷新页面
                            });
                        });
                    }
                    //如果当前页大于最大页数显示“没有更多了”
                    if(page >= Math.ceil(maxPageNum)){
                        $('.loading-box .no_more').show();
                    }
                }else{
                    $.notify({
                        message: rel['message'],    //提示消息
                    },{
                        type: "danger", //错误类型
                    });
                }
                $('.loading-box .loading').hide();   //隐藏loading
            });
            $('.loading-box .loading').show();
            $('.loading-box .no_more').hide();
        }
    }
        
    // 单击全选或取消全选
    $('input[name="selection_all"]').click(function(){
        if($(this).is(':checked')){
            $('input[name="selection[]"]').each(function(){
                $(this).prop("checked",true);
            });
        }else{
            $('input[name="selection[]"]').each(function(){
                $(this).prop("checked",false);
            });
        }
    });
        
    // 添加到购物车
    $("#add-carts").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            ids += $(this).val()+',';                    
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        var url=$(this).attr('data-url');
        $.post(url, {ids}, function(rel){
            location.reload();  //刷新页面
            // 把复选框全取消
//            $('input[name="selection_all"]').prop("checked",false);
//            $('input[name="selection[]"]').each(function(){
//                $(this).prop("checked",false);
//            });
//            if(rel['code'] == '0'){
//                $.notify({
//                    message: '成功加入购物车' 
//                },{
//                    type: 'success'
//                });
//                
//            }else{
//                $.notify({
//                    message: rel['msg'] 
//                },{
//                    type: 'danger'
//                });
//            }
        });
    });
        
    // 立即购买
    $("#buys").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            ids += $(this).val()+',';                       
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        var url=$(this).attr('data-url');
        //$.post(url, {ids});
        window.location.href = url + "?id=" + ids;
    });
JS;
$this->registerJs($js, View::POS_READY);
?>