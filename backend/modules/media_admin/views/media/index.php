<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\searchs\MediaSearch;
use common\modules\rbac\components\Helper;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaSearch */
/* @var $dataProvider ActiveDataProvider */

MediaModuleAsset::register($this);


$this->title = Yii::t('app', '{Media}{List}', [
    'Media' => Yii::t('app', 'Media'), 'List' => Yii::t('app', 'List')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-index">
  
    <?= $this->render('_search', [
        'model' => $searchModel,
        'filters' => $filters,
        'userMap' => $userMap,
        'attrMap' => $attrMap
    ]) ?>
    
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                
                <?php 
                    if(Helper::checkRoute(Url::to(['batch-edit-price'])) || Helper::checkRoute(Url::to(['batch-edit-attribute'])) 
                        || Helper::checkRoute(Url::to(['approve/add-apply'])) || Helper::checkRoute(Url::to(['approve/del-apply']))){
                        echo  Html::a(Yii::t('app', '{Reset}{Price}', [
                            'Reset' => Yii::t('app', 'Reset'), 'Price' => Yii::t('app', 'Price')
                        ]), ['batch-edit-price'], ['id' => 'btn-editPrice', 'class' => 'btn btn-primary btn-flat']); 
                        echo ' '.Html::a(Yii::t('app', '{Reset}{Tag}', [
                            'Reset' => Yii::t('app', 'Reset'), 'Tag' => Yii::t('app', 'Tag')
                        ]), ['batch-edit-attribute'], ['id' => 'btn-editAttribute', 'class' => 'btn btn-primary btn-flat']);
                        echo ' '.Html::a(Yii::t('app', '{Apply}{Into}{DB}', [
                            'Apply' => Yii::t('app', 'Apply'), 'Into' => Yii::t('app', 'Into'), 'DB' => Yii::t('app', 'DB')
                        ]), ['approve/add-apply'], ['id' => 'btn-addApply', 'class' => 'btn btn-danger btn-flat']);
                        echo ' ' . Html::a(Yii::t('app', '{Apply}{Delete}', [
                            'Apply' => Yii::t('app', 'Apply'), 'Delete' => Yii::t('app', 'Delete')
                        ]), ['approve/del-apply'], ['id' => 'btn-delApply', 'class' => 'btn btn-danger btn-flat']);
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
        <div class="page"><ul class="pagination"></ul></div>
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
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
        var pageSize = 10;
        var pageCount = Math.ceil(totalCount / pageSize);
        var pagination = $('.pagination').data()['bootstrapPaginator'];
        
        pagination.setOptions({totalPages: pageCount});
        
        // 获取素材数据 
        if(!isPageLoading){
            isPageLoading = true;   //设置已经提交当中...
            $.get("/media_admin/media/list?page=" + page,  params, function(response){
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
            alert("请选择需要申请的素材");
        }
    });
       
    // 出素材编辑标签面板
    $('#btn-editPrice, #btn-editAttribute').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("请选择需要重置的素材");
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
    
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>
