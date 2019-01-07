<?php

use frontend\modules\media_library\assets\ModuleAssets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

ModuleAssets::register($this);

$this->title = Yii::t('app', '{Resources}{Online}',[
    'Resources' => Yii::t('app', 'Resources'),
    'Online' => Yii::t('app', 'Online')
]);

$pages = ArrayHelper::getValue($filters, 'pages', 'list');   //表格显示

?>

<!--页头搜索-->
<?= $this->render('_search',[
    'searchModel' => $searchModel,
    'attrMap' => $attrMap,
    'filters' => $filters,
])?>

<div class="container content">
    <div class="default-index common">
        <!--按钮-->
        <div class="mc-title clear-margin">
            <span>结果列表（共查询到 <?= $totalCount; ?> 个资源）</span>
            <div class="btngroup pull-right">
                <?php
                echo Html::a('加入购物车', 'javascript:;', ['id' => 'add-carts',
                    'data-url' => Url::to(['add-carts']),
                    'class' => 'btn btn-highlight btn-flat', 'title' => '加入购物车']) . '&nbsp;';
                echo Html::a('立即购买', 'javascript:;', ['id' => 'buys',
                    'data-url' => Url::to(['checking-order']),
                    'class' => 'btn btn-highlight btn-flat', 'title' => '立即购买']) . '&nbsp;';
                echo Html::a('<i class="glyphicon glyphicon-th-list"></i>', 
                    array_merge(['index'], array_merge($filters, ['pages' => 'list'])),
                        ['id' => 'list', 'title' => '列表模式']);
                echo Html::a('<i class="glyphicon glyphicon-th-large"></i>',
                    array_merge(['index'], array_merge($filters, ['pages' => 'chart'])),
                        ['id' => 'chart', 'title' => '图表模式']);
                ?>
            </div>
        </div>
        <!--列表or图表-->
        <div class="mc-panel set-bottom">
            <?php if ($pages == 'list') {?>
                <!--列表显示-->
                <div class="meida-table">
                    <table class="table table-bordered mc-table">
                        <thead>
                            <tr>
                                <th style="width: 30px"><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th>
                                <th style="width: 80px">预览</th>
                                <th>资源编号</th>
                                <th>资源名称</th>
                                <th>存储目录</th>
                                <th style="width: 50px">类型</th>
                                <th>资源价格</th>
                                <th style="width: 90px">时长</th>
                                <th style="width: 100px">大小</th>
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

<?php
$params_js = json_encode($filters); //js参数
//加载 DOM 模板
if ($pages == 'list') {
    $renderView = '____list';
} else {
    $renderView = '____chart';
}
$details_dom = json_encode(str_replace(array("\r\n", "\r", "\n"), " ", 
    $this->renderFile("@frontend/modules/media_library/views/default/$renderView.php")));
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
            loaddata(page, '/media_library/default/index');
        }
    });
        
    //加载第一页的课程数据
    loaddata(page, '/media_library/default/index');
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
                isPageLoading = false;  //取消设置加载当中...
                var data = rel.data;     //获取返回的数据
                page = Number(data.page);    //当前页
                //请求成功返回数据，否则提示错误信息
                if(rel['code'] == '200'){
                    for(var i in data.result){
                        var item = $(Wskeee.StringUtil.renderDOM($details_dom, data.result[i])).appendTo($(".meida-details"));
                        //鼠标经过、离开事件
                        item.hover(function(){
                            $(this).addClass('hover');
                            $(this).find(".checkbox").removeClass('hidden');
                        }, function(){
                            $(this).removeClass('hover');
                            $(this).find(".checkbox").addClass('hidden');
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
        console.log(111);
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
            console.log($(this).parents('tr').attr('data-value'));
            ids += $(this).parents('tr').attr('data-value')+',';                       
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        // console.log(ids);
        var url=$(this).attr('data-url');
        // console.log(url);
        $.post(url, {ids});
    });
        
    // 立即购买
    $("#buys").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            console.log($(this).parents('tr').attr('data-value'));
            ids += $(this).parents('tr').attr('data-value')+',';                       
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        // console.log(ids);
        var url=$(this).attr('data-url');
        // console.log(url);
        //$.post(url, {ids});
        window.location.href = url + "?id=" + ids;
    });
JS;
$this->registerJs($js, View::POS_READY);
?>