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
  
    <span class="title">媒体列表：</span>
    
    <!--导入的媒体列表-->
    <table id="mediaUploadTable" class="table table-striped table-bordered">
        
        <thead>
            
            <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 146px; padding: 8px 4px;">媒体名称</th>
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
    <div class="summary">第<b class="first"></b>-<b class="last"></b>条，总共<b class="totalCount"></b>条数据。</div>
    
    <!--分页-->
    <div id="pagination">
        <div class="first"><<</div><div class="prev"><</div>
        <ul class="list"></ul>
        <div class="next disable">></div><div class="last disable">>></div>
    </div>
    
</div>

<?php
$js = <<<JS
        
    var media_upload_tr_dom = '$media_upload_tr_dom';  //加载模板
    var data = $dataProvider;   // 数据提供者
    var totalCount = $totalCount;
    var pageCount = Math.ceil(totalCount / 10);
                
    // 分页
    $('#pagination').paging({
        nowPage: 1,
        allPages: pageCount,
        displayPage: 5,
        callBack: function (now) {
            loadUploadTable(now - 1);
            $('.summary').find('b.first').html(Number((now - 1) * 10 + 1));
            $('.summary').find('b.last').html(now * 10);
            $('.summary').find('b.totalCount').html(totalCount);
        }
    });
        
    // 加载生成媒体列表
    function loadUploadTable(page){
        $('#mediaUploadTable').find('tbody').html('');
        var i = page * 10;
        do {
            $(Wskeee.StringUtil.renderDOM(media_upload_tr_dom, $.extend({id: Number(i + 1)}, data[i]))).appendTo($('#mediaUploadTable').find('tbody'));
            i++;
        } while (i <= Number((page + 1) * 10 - 1));
    }
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>