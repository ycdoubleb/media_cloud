<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */

$this->title = Yii::t('app', '{Handle}{Result}', [
    'Handle' => Yii::t('app', 'Handle'), 'Result' => Yii::t('app', 'Result')
]);

// 当前action
$action = Yii::$app->controller->action->id

?>
<div class="acl-refresh_cach">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div id="myModalBody" class="modal-body">
                
                <!--结果进度-->
                <div class="progress">
                    <div class="progress-bar result-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%; line-height: 18px">0%</div>
                </div>
                    
            </div>
            
       </div>
    </div>
  
</div>

<?php
$js = <<<JS
   
    var ids = $ids;
    var action = "$action";
    var increment = 0,
        complete_num = 0,
        max_num = ids.length,
        progress = $('#myModalBody').find('div.result-progress');
        
    $.each(ids, function(index, id){
        $.post('/media_admin/recycle/' + action + '?id=' + id, function(response){
            if(response.code == "0"){
                complete_num = ++increment;
                if(index >= max_num - 1){
                    window.location.replace(window.location.href);
                }
            }
            progress.css({width: parseInt(complete_num / max_num * 100) + '%'}).html(parseInt(complete_num / max_num * 100) + '%');
        });
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>