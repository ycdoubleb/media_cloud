<?php

use backend\modules\system_admin\assets\SystemAssets;
use yii\helpers\Html;
use yii\web\View;


/* @var $this View */

SystemAssets::register($this);

$this->title = Yii::t('app', 'Uploadfile Admin');
$this->params['breadcrumbs'][] = Yii::t('app', '{File}{List}', [
    'File' => Yii::t('app', 'File'), 'List' => Yii::t('app', 'List')
]);

?>

<div class="uploadfile-index">
    <div class="mc-tabs">
        <ul class="list-unstyled">
            <li id="file">
                <?= Html::a(Yii::t('app', '{File}{Admin}', [
                    'File' => Yii::t('app', 'File'), 'Admin' => Yii::t('app', 'Admin')
                ]), array_merge(['index'], array_merge($filters, ['tabs' => 'file']))) ?>
            </li>
            <li id="chunk">
                <?= Html::a(Yii::t('app', '{Filechip}{Admin}', [
                    'Filechip' => Yii::t('app', 'Filechip'), 'Admin' => Yii::t('app', 'Admin')
                ]), array_merge(['index'], array_merge($filters, ['tabs' => 'chunk']))) ?>
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
$delMsg = Yii::t('app', 'Are you sure you want to delete?');    // 删除提示
$selMsg = Yii::t('app', 'Please select at least one.');  // 选择提示
$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
        
    // 删除文件
    $("#delete").click(function(e){
        e.preventDefault();
        var val = [],
            url = $(this).attr("href"),
            checkBoxs = $('input[name="selection[]"]');
        // 循环组装素材id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        
        if(val.length > 0){
            if(confirm("{$delMsg}") == true){
                $.post(url, {ids: val}, function(response){
                    if(response.code == "0"){ location.reload();}  //刷新页面
                });
            }
        }else{
            alert("{$selMsg}");
        }
    });  
JS;
$this->registerJs($js, View::POS_READY);
?>