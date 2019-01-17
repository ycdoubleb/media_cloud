<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Dir;
use common\models\media\Media;
use common\widgets\depdropdown\DepDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Create}{Media}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//加载 WATERMARK_DOM 模板
$media_data_tr_dom = str_replace("\n", ' ', $this->render('____media_data_tr_dom'));

?>
<div class="media-create">

    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-6 col-md-6\">{input}</div>\n<div class=\"col-lg-6 col-md-6\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>
    
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
        <li role="presentation" class="">
            <a href="#config" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">转码配置</a>
        </li>
    </ul>
    
    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <!--存储目录-->
            <?= $form->field($model, 'dir_id', [
                'template' => "{label}\n<div class=\"col-lg-10 col-md-10\">{input}</div>",
            ])->widget(DepDropdown::class,[
                'pluginOptions' => [
                    'url' => Url::to(['/media_config/dir/search-children']),
                    'max_level' => 10,
                ],
                'items' => Dir::getDirsBySameLevel($model->dir_id, Yii::$app->user->id, true, true),
                'itemOptions' => [
                    'style' => 'width: 175px; display: inline-block;',
                ],
            ])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Storage}{Dir}：', [
                'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
            ])) ?>
            
            <?= $this->render('____form_attribute_dom', [
                'attrMap' => $attrMap,
                'attrSelected' => isset($attrSelected) ? $attrSelected : null,
                'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
            ]) ?>

            <?= $this->render('____form_upload_dom', [
                'mimeTypes' => $mimeTypes
            ]) ?>

        </div>
        
        <!--转码配置-->
        <div role="tabpanel" class="tab-pane fade" id="config" aria-labelledby="config-tab">
            
            <?= $this->render('____form_watermark_dom', [
                'isNewRecord' => $model->isNewRecord ? 1 : 0,
                'wateFiles' => $wateFiles,
                'wateSelected' => $wateSelected,
            ]) ?>
            
        </div>
    
        <div class="form-group">
            <?= Html::label(null, null, ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
            <div class="col-lg-11 col-md-11">
                <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 'class' => 'btn btn-success btn-flat']) ?>
            </div> 
        </div>
    
    </div>  
        
    <?php ActiveForm::end(); ?>  

</div>

<!--模态框-->
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
                <?= Html::button(Yii::t('app', '{Anew}{Upload}', ['Anew' => Yii::t('app', 'Anew'), 'Upload' => Yii::t('app', 'Upload')
                    ]), ['id' => 'btn-anewUpload', 'class' => 'btn btn-primary']) 
                ?>
                <?= Html::button(Yii::t('app', 'Close'), ['id' => 'btn-close', 'class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
            </div>
            
       </div>
    </div> 
</div>

<script type="text/javascript">
    //媒体 tr dom
    var php_media_data_tr_dom = '<?= $media_data_tr_dom ?>';
    //批量上传控制器
    var mediaBatchUpload;
    //上传工具的媒体
    var uploaderMedias = [];
    
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        initBatchUpload();        //初始批量上传
        initWatermark();          //初始水印
        initSubmit();             //初始提交
    }
    
    /************************************************************************************
    *
    * 初始化批量上传
    *
    ************************************************************************************/
    function initBatchUpload(){
        mediaBatchUpload = new mediaupload.MediaBatchUpload({
            media_data_tr_dom : php_media_data_tr_dom,
        });
        
        // 关闭模态框事件
        $('.myModal').on('hidden.bs.modal', function (e) {
            $table = $('.result-info').find('table.result-table');
            $table.find('tbody').html('');
        });
    }
    
    /**
     * 上传完成后返回的文件数据
     * @param {object} data
     * @returns {Array|uploaderMedias}
     */
    function uploadComplete(data){
        mediaBatchUpload.addMediaData(data);
    }
    
    /**
     * 删除上传列表中的文件
     * @param {object} data
     * @returns {undefined}
     */
    function fileDequeued(data){
        mediaBatchUpload.delMediaData(data.dbFile);
    }

    /************************************************************************************
     *
     * 初始化提交
     *
     ************************************************************************************/ 
    function initSubmit(){
        // 弹出提交结果
        $("#submitsave").click(function(){
            $('.myModal').modal("show");
            var formdata = $('#media-form').serialize();
            mediaBatchUpload.submit(formdata);
        });
        // 重新上传
        $("#btn-anewUpload").click(function(){
            $table = $('.result-info').find('table.result-table');
            $table.find('tbody').html('');
            var formdata = $('#media-form').serialize();
            mediaBatchUpload.submit(formdata);
        });
    }
    
</script>