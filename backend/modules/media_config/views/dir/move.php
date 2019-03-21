<?php

use wbraganca\fancytree\FancytreeAsset;
use wbraganca\fancytree\FancytreeWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Move}{Dir}', [
    'Move' => Yii::t('app', 'Move'), 'Dir' => Yii::t('app', 'Dir')
]);

?>
<div class="dir-move">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body" style="padding: 0px">
                
                <?php echo FancytreeWidget::widget([
                    'options' =>[
                        'id' => 'table-fancytree_2', // 设置整体id
                        'source' => $dataProvider,
                        'extensions' => ['table'],
                        'table' => [
                            'indentation' => 20,
                            'nodeColumnIdx' => 0
                        ],
                    ]
                ]); ?>
                
                <div class="table-responsive">
                    <table id="table-fancytree_2" class="table table-hover vk-table">
                        
                        <colgroup>
                            <col width="*"></col>
                        </colgroup>
                        
                        <thead class="hidden">
                            <tr><th><?= Yii::t('app', 'Name') ?></th></tr>
                        </thead>
                        
                        <tbody>
                            <tr>
                                <td style="text-align: left;"></td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
            </div>
            
            <div class="modal-footer">
                <a href="javascript:;" id="submitsave" class="btn btn-primary pull-right" data-dismiss="modal" aria-label="Close">确定</a>
            </div>
            
       </div>
    </div>
    
</div>

<?php
$js = <<<JS
    //移动视频到指定目录
    var moveIds = "$move_ids";
    $('#submitsave').click(function(){
        var _nodes = $("#table-fancytree_2").fancytree("getActiveNode");
        $.post('/media_config/dir/move?move_ids=' + moveIds + '&target_id=' + _nodes.key);
    });               
JS;
    $this->registerJs($js,  View::POS_READY);
?>