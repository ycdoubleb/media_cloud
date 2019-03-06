<?php

use yii\data\Pagination;
use yii\web\View;
use yii\widgets\LinkPager;


/* @var $this View */

$totalCount = count($dataProvider);
$dataProvider = json_encode($dataProvider);
//加载 MEDIAUPLOADTR DOM 模板
$media_upload_tr_dom = str_replace("\n", ' ', $this->render('____media_upload_tr_dom'));

?>

<div class="media-upload-table">
  
    <span class="title">素材列表：</span>
    
    <!--导入的素材列表-->
    <table id="mediaUploadTable" class="table table-striped table-bordered">
        
        <thead>
            
            <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 146px; padding: 8px 4px;">素材名称</th>
                <th style="width: 96px; padding: 8px 4px;">缩略图</th>
                <th style="width: 176px; padding: 8px 4px;">路径</th>
                <th style="width: 76px; padding: 8px 4px;">时长</th>
                <th style="width: 86px; padding: 8px 4px;">大小</th>
                <th style="width: 176px; padding: 8px 4px;">标签</th>
            </tr>
            
        </thead>
        
        <tbody></tbody>
        
    </table>
    
    <!--总结-->
    <div class="summary"></div>

    <!--分页-->
    <div class="page"><ul class="pagination"></ul></div>
   
</div>

<?php
$js = <<<JS
    initPageNav();
    /**
     * 初始分页
     * @private
     */
    function initPageNav(){
        var options = {
            currentPage: 1,
            totalPages: 1,
            bootstrapMajorVersion: 3,
            onPageChanged: function(event, oldPage, newPage){
                reflashPageNav(newPage);
            }
        };

        $('.pagination').bootstrapPaginator(options);
        reflashPageNav(1)
    }    
        
    /**
     * 刷新分页
     * @private
     */
    function reflashPageNav(page){
        // 数据提供者
        var data = $dataProvider;   
        //加载模板 
        var media_upload_tr_dom = '$media_upload_tr_dom';    
        // 总数
        var totalCount = $totalCount;
        var pageSize = 10;
        var pageCount = Math.ceil(totalCount / pageSize);
        var pagination = $('.pagination').data()['bootstrapPaginator'];
        
        pagination.setOptions({totalPages: pageCount});
        
        loadUploadTable(data, page, media_upload_tr_dom);
        // 如果页数大于等于2的显示
        if(pageCount >= 2){
            $('.summary').html('第 <b>' + Number((page - 1) * pageSize + 1) + '</b>-<b>' + (page == pageCount ? totalCount : page * pageSize) + '</b> 条，总共 <b>' + totalCount + '</b> 条数据。');
        }
    } 
    
    /**
     * 加载生成素材列表
     * @param {array} data
     * @param {int} page
     * @param {json} media_upload_tr_dom
     * @returns {undefined}
     */
    function loadUploadTable(data, page, media_upload_tr_dom){
        $('#mediaUploadTable').find('tbody').html('');
        var i = (page - 1) * 10;        
        do {
            if(!data[i]){
                break;
            }
            $(Wskeee.StringUtil.renderDOM(media_upload_tr_dom, $.extend({id: Number(i + 1)}, data[i]))).appendTo($('#mediaUploadTable').find('tbody'));
            i++;
        } while (i < page * 10);
    }
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>