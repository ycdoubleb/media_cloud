<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\searchs\MediaSearch;
use common\modules\rbac\components\Helper;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaSearch */
/* @var $dataProvider ActiveDataProvider */

MediaModuleAsset::register($this);


$this->title = Yii::t('app', '{Medias}{List}', [
    'Medias' => Yii::t('app', 'Medias'), 'List' => Yii::t('app', 'List')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">
  
    <?= $this->render('_search', [
        'model' => $searchModel,
        'filters' => $filters,
        'category_id' => $category_id,
        'dirDataProvider' => $dirDataProvider,
        'userMap' => $userMap,
        'attrMap' => $attrMap
    ]) ?>
        
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                
                <?php 
                    if(Helper::checkRoute(Url::to(['batch-edit-price'])) || Helper::checkRoute(Url::to(['batch-edit-attribute'])) 
                        || Helper::checkRoute(Url::to(['approve/add-apply'])) || Helper::checkRoute(Url::to(['approve/del-apply']))){
                        echo  Html::a(Yii::t('app', 'Reset Dir'), ['batch-edit-dir', 'category_id' => $category_id], [
                            'id' => 'btn-editDir', 'class' => 'btn btn-primary btn-flat'
                        ]); 
                        echo ' '. Html::a(Yii::t('app', 'Reset Price'), ['batch-edit-price', 'category_id' => $category_id], [
                            'id' => 'btn-editPrice', 'class' => 'btn btn-primary btn-flat'
                        ]); 
                        echo ' '.Html::a(Yii::t('app', 'Reset Attribute'), ['batch-edit-attribute', 'category_id' => $category_id], [
                            'id' => 'btn-editAttribute', 'class' => 'btn btn-primary btn-flat'
                        ]);
                        echo ' '.Html::a(Yii::t('app', 'Reset Tags'), ['batch-edit-tags', 'category_id' => $category_id], [
                            'id' => 'btn-editTags', 'class' => 'btn btn-primary btn-flat'
                        ]);
                        echo ' '.Html::a(Yii::t('app', 'Apply For Be Put In Storage'), ['approve/add-apply'], [
                            'id' => 'btn-addApply', 'class' => 'btn btn-danger btn-flat'
                        ]);
                        echo ' ' . Html::a(Yii::t('app', 'Apply For Delete'), ['approve/del-apply'], [
                            'id' => 'btn-delApply', 'class' => 'btn btn-danger btn-flat'
                        ]);
                    }
                ?>
            </div>
            
        </div>
    
        <div id="media_list">
            <div class="loading-box" style="text-align: center; padding: 20px">
                <span class="loading" style="display: none"></span>
            </div>
        </div>
        
        <!--总结-->
        <div class="summary"></div>

        <!--分页-->
        <div class="page">
            <ul class="pagination" style="float: left"></ul>
            <div class="pull-left" style="display: inline-block; margin: 20px 0px">
                <?= Html::dropDownList('pageSize', ArrayHelper::getValue($filters, 'pageSize', 10), [10 => 10, 20 => 20, 50 => 50, 100 => 100], [
                    'style' => 'height: 34px;', 'onchange' => 'setPerPageNum($(this).val())'
                ]) ?>
            </div>
        </div>
        
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$msg = Yii::t('app', 'Please select at least one.');    // 消息提示
$url = Yii::$app->request->url;
$pageSize = ArrayHelper::getValue($filters, 'pageSize', 10);
$params_js = json_encode($filters); //js参数
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
        window.page = page;
        window.params = $params_js;  //传值
        var isPageLoading = false;
        var totalCount = $totalCount;
        var pageSize = $pageSize;
        var pageCount = Math.ceil(totalCount / pageSize);
        var pagination = $('.pagination').data()['bootstrapPaginator'];
        
        pagination.setOptions({totalPages: pageCount});
        
        // 获取素材数据 
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.get("/media_admin/media/list", $.extend(params, {page: page}), function(response){
                isPageLoading = false;  //取消设置提交当中...
                $('#media_list').html(response);
            });
            $('.loading-box .loading').show();
        }
        
        // 如果页数大于等于2的显示
        if(pageCount >= 2){
            $('.summary').html('第 <b>' + Number((page - 1) * pageSize + 1) + '</b>-<b>' + (page == pageCount ? totalCount : page * pageSize) + '</b> 条，总共 <b>' + totalCount + '</b> 条数据。');
        }
    } 
        
    // 弹出素材申请面板
    $('#btn-addApply, #btn-delApply').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?media_ids=" + val);
        }else{
            alert("{$msg}");
        }
    });
       
    // 出素材编辑标签面板
    $('#btn-editDir, #btn-editPrice, #btn-editAttribute, #btn-editTags').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "&id=" + val);
        }else{
            alert("{$msg}");
        }
    }); 
        
    /**
     * 获取 getCheckBoxsValue
     * @returns {Array|getcheckBoxsValue.val}
     */
    function getCheckBoxsValue(){
        var val = [],
            checkBoxs = $('input[name="selection[]"]');
        // 循环组装素材id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        
        return val
    }
        
            
    /**
     * 对象转url参数
     *  @param string url    地址 
     * @param array data    参数对象
     */
    function urlEncode (param, key, encode) {  
        if(param==null) return '';  
        var paramStr = '';  
        var t = typeof (param);  
        if (t == 'string' || t == 'number' || t == 'boolean') {  
            paramStr += '&' + key + '=' + ((encode==null||encode) ? encodeURIComponent(param) : param);  
        } else {  
            for (var i in param) {  
                var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);  
                paramStr += urlEncode(param[i], k, encode);  
            }  
        }  
        return paramStr;  
    }
            
    /**
     * 设置每页显示的数量
     * @param string value    值 
     */
    window.setPerPageNum = function (value){
        var urlParams = $.extend(params, {page: 1, pageSize: value});
        window.location.href = "/media_admin/media/index?"+urlEncode(urlParams).substr(1);
    }   
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>
