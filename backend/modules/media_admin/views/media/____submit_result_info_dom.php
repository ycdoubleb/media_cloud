<?php

use yii\helpers\Html;

?>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= Yii::t('app', '{Submit}{Result}', [
                        'Submit' => Yii::t('app', 'Submit'), 'Result' => Yii::t('app', 'Result')
                    ]) ?>
                </h4>
            </div>
            
            <div class="modal-body result-info" id="myModalBody">
                
                <!--结果进度-->
                <div class="progress">
                    <div class="progress-bar result-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%; line-height: 18px">0%</div>
                </div>
                
                <!--结果提示-->
                <p class="text-default result-hint" style="font-size: 13px; margin-top: 10px">
                    共有 <span class="max_num">0</span> 个需要上传，其中 <span class="completed_num">0</span> 个成功！
                </p>
                
                <!--文本-->
                <p class="text-default" style="font-size: 13px;"><?= Yii::t('app', 'The following is a failure list:') ?></p>
                
                <!--失败列表-->
                <table class="table table-striped table-bordered result-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th style="width: 210px;">
                                <?= Yii::t('app', '{File}{Name}', [
                                    'File' => Yii::t('app', 'File'), 'Name' => Yii::t('app', 'Name')
                                ]) ?>
                            </th>
                            <th style="width: 300px;">
                                <?= Yii::t('app', '{Fail}{Cause}', [
                                    'Fail' => Yii::t('app', 'Fail'), 'Cause' => Yii::t('app', 'Cause')
                                ]) ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                
                <!--总结-->
                <div class="summary"></div>
                
                <!--分页-->
                <div class="page"><ul class="pagination"></ul></div>
                
            </div>
            
            <div class="modal-footer">
                <span class="text-default hidden" style="font-size: 13px;">提交完成</span>
                <?= Html::button(Yii::t('app', '{Anew}{Submit}', ['Anew' => Yii::t('app', 'Anew'), 'Submit' => Yii::t('app', 'Submit')
                    ]), ['id' => 'btn-anewUpload', 'class' => 'btn btn-primary hidden']) 
                ?>
                <?= Html::button(Yii::t('app', 'Close'), ['id' => 'btn-close', 'class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
            </div>
            
       </div>
    </div> 
</div>
