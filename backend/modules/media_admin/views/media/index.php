<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\searchs\MediaSearch;
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
                <?= Html::a(Yii::t('app', '{Reset}{Price}', [
                    'Reset' => Yii::t('app', 'Reset'), 'Price' => Yii::t('app', 'Price')
                ]), ['batch-edit-price'], ['id' => 'btn-editPrice', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= Html::a(Yii::t('app', '{Reset}{Tag}', [
                    'Reset' => Yii::t('app', 'Reset'), 'Tag' => Yii::t('app', 'Tag')
                ]), ['batch-edit-attribute'], ['id' => 'btn-editAttribute', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', '{Apply}{Into}{DB}', [
                    'Apply' => Yii::t('app', 'Apply'), 'Into' => Yii::t('app', 'Into'), 'DB' => Yii::t('app', 'DB')
                ]), ['approve/add-apply'], ['id' => 'btn-addApply', 'class' => 'btn btn-danger btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', '{Apply}{Delete}', [
                    'Apply' => Yii::t('app', 'Apply'), 'Delete' => Yii::t('app', 'Delete')
                ]), ['approve/del-apply'], ['id' => 'btn-delApply', 'class' => 'btn btn-danger btn-flat']); ?>
            </div>
            
        </div>
    
        <div id="media_list"></div>
        
        <!--总结-->
        <div class="summary">第 <b class="first"></b>-<b class="last"></b> 条，总共 <b class="totalCount"></b> 条数据。</div>

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
        var params = $params_js;  //传值
        var totalCount = $totalCount;
        var pageSize = 10;
        var pageCount = Math.ceil(totalCount / pageSize);
        var pagination = $('.pagination').data()['bootstrapPaginator'];
        
        pagination.setOptions({
            totalPages: pageCount
        });
        
        // 获取媒体数据 
        $.get("/media_admin/media/list?page=" + page,  params, function(response){
            $('#media_list').html(response);
        });
        
        $('.summary').find('b.first').html(Number((page - 1) * pageSize + 1));
        if(page == pageCount){
            $('.summary').find('b.last').html(totalCount);
            
        }else{
            $('.summary').find('b.last').html(page * pageSize);
        }
        $('.summary').find('b.totalCount').html(totalCount);
    } 
        
    // 弹出媒体申请面板
    $('#btn-addApply, #btn-delApply').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?media_id=" + val);
        }else{
            alert("请选择需要申请的媒体");
        }
    });
       
    // 出媒体编辑标签面板
    $('#btn-editPrice, #btn-editAttribute').click(function(e){
        e.preventDefault();
        var val = getCheckBoxsValue(), 
            url = $(this).attr("href");
        if(val.length > 0){
            $(".myModal").html("");
            $('.myModal').modal("show").load(url + "?id=" + val);
        }else{
            alert("请选择需要申请的媒体");
        }
    }); 
        
    /**
     * 获取 getCheckBoxsValue
     * @returns {Array|getcheckBoxsValue.val}
     */
    function getCheckBoxsValue(){
        var val = [],
            checkBoxs = $('input[name="selection[]"]');
        // 循环组装媒体id
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
