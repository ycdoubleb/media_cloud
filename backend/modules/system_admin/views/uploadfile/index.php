<?php

use backend\modules\system_admin\assets\SystemAssets;
use yii\helpers\Html;
use yii\web\View;


/* @var $this View */

SystemAssets::register($this);

$this->title = Yii::t('app', '{Upload}{File}{Admin}',[
    'Upload' => Yii::t('app', 'Upload'),
    'File' => Yii::t('app', 'File'),
    'Admin' => Yii::t('app', 'Admin'),
]);
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="uploadfile-index">
    <div class="mc-tabs">
        <ul class="list-unstyled">
            <li id="file">
                <?= Html::a('文件管理', array_merge(['index'], array_merge($filters, ['tabs' => 'file']))) ?>
            </li>
            <li id="chunk">
                <?= Html::a('文件分片管理', array_merge(['index'], array_merge($filters, ['tabs' => 'chunk']))) ?>
            </li>
        </ul>
    </div>
    
    <div class="mc-panel">
        <?php if($tabs == 'file'){
                echo $this->render('____file', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
            } else {
                echo $this->render('____chunk', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
            }
        ?>
    </div>
</div>

<?php

$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
        
    // 删除文件
    $("#delete").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            ids += $(this).parents('tr').attr('data-value')+',';                       
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        console.log(ids);
        console.log($(this).attr('data-url'));
        var url=$(this).attr('data-url');
        $.post(url, {ids}, function(rel){
            location.reload();  //刷新页面
        });
    });  
JS;
$this->registerJs($js, View::POS_READY);
?>