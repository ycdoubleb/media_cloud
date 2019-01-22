<?php

use yii\helpers\Html;

?>

<div class="modal fade myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">提交结果</h4>
            </div>
            
            <div class="modal-body result-info" id="myModalBody">
                
                <!--结果进度-->
                <div class="progress">
                    <div class="progress-bar result-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%; line-height: 18px">0%</div>
                </div>
                
                <!--结果提示-->
                <p class="text-default result-hint" style="font-size: 13px; margin-top: 10px">
                    共有 <span class="max_num">0</span> 个需要上传，其中 <span class="completed_num">0</span> 个成功 <span class="lose_num">0</span> 个失败！
                </p>
                
                <!--文本-->
                <p class="text-default" style="font-size: 13px;">以下为失败列表：</p>
                
                <!--失败列表-->
                <table class="table table-striped table-bordered result-table">
                    <thead>
                        <tr><th style="width: 30px;">#</th><th style="width: 210px;">文件名</th><th style="width: 300px;">失败原因</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="modal-footer">
                <span class="text-default hidden" style="font-size: 13px;">提交完成</span>
                <?= Html::button(Yii::t('app', '{Anew}{Upload}', ['Anew' => Yii::t('app', 'Anew'), 'Upload' => Yii::t('app', 'Upload')
                    ]), ['id' => 'btn-anewUpload', 'class' => 'btn btn-primary hidden']) 
                ?>
                <?= Html::button(Yii::t('app', 'Close'), ['id' => 'btn-close', 'class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
            </div>
            
       </div>
    </div> 
</div>
