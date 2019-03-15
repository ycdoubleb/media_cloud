<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\widgets\zTree\zTreeAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

MediaModuleAsset::register($this);
zTreeAsset::register($this);

$this->title = Yii::t('app', '{Create}{Media}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//加载 MEDIADATATR DOM 模板
$media_data_tr_dom = str_replace("\n", ' ', $this->render('____media_data_tr_dom'));

?>
<div class="media-create">

    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ]
    ]); ?>
    
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
        <li role="presentation" class="">
            <a href="#config" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">转码配置</a>
        </li>
        <?= Html::a('上传外链素材', ['media-import/index'], ['class' => 'pull-right', 'style' => 'display: block;margin-top: 10px;margin-right: 10px']) ?>
    </ul>
    
    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <!--存储目录-->
            <div class="form-group field-media-dir_id required">
                
                <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Storage}{Dir}：', [
                    'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
                ]), 'media-dir_id', ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
                
                <div class="col-lg-8 col-md-8">
                    
                    <div class="col-lg-12 col-md-12 clean-padding">
                        
                        <div class="zTree-dropdown-container zTree-dropdown-container--krajee">
                            <!-- 模拟select点击框 以及option的text值显示-->
                            <span id="zTree-dropdown-name" class="zTree-dropdown-selection zTree-dropdown-selection--single" onclick="showTree();" >
                                <span class="zTree-dropdown-selection__placeholder">全部</span>
                            </span> 
                            <!-- 模拟select右侧倒三角 -->
                            <i class="zTree-dropdown-selection__arrow"></i>
                            <!-- 存储 模拟select的value值 -->
                            <input id="zTree-dropdown-value" type="hidden" name="Media[dir_id]" />
                            <!-- zTree树状图 相对定位在其下方 -->
                            <div class="zTree-dropdown-options ztree"  style="display:none;"><ul id="zTree-dropdown"></ul></div>  
                        </div>
                        
                    </div>
                    
                    <div class="col-lg-12 col-md-12 clean-padding"><div class="help-block"></div></div>
                    
                </div>
                
            </div>
            
            <!--属性选择-->
            <?= $this->render('____form_attribute_dom', [
                'attrMap' => $attrMap,
                'isTagRequired' => $isTagRequired,
                'attrSelected' => isset($attrSelected) ? $attrSelected : null,
                'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
            ]) ?>

            <!--素材上传-->
            <?= $this->render('____form_upload_dom', [
                'model' => $model,
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
            <label class="col-lg-1 col-md-1 control-label form-label"></label>
            <div class="col-lg-1 col-md-1">
                <?= Html::button(Yii::t('app', 'Submit'), [
                    'id' => 'submitsave', 
                    'class' => 'btn btn-success btn-flat',
                    'data-toggle' => "tooltip", 
                    'data-placement' => "top", 
                    'title' => "Tooltip on top"
                ]) ?>
            </div> 
        </div>
    
    </div>  
        
    <?php ActiveForm::end(); ?>  

</div>

<!--模态框-->
<?= $this->render('/layouts/modal'); ?>
<?= $this->render('____submit_result_info_dom') ?>

<script type="text/javascript">
    //素材 tr dom
    var php_media_data_tr_dom = '<?= $media_data_tr_dom ?>';
    //批量上传控制器
    var mediaBatchUpload;
    //上传工具的素材
    var uploaderMedias = [];
    //树状图展示
    var treeDataList = <?= json_encode($dirDataProvider) ?>;
    //是否已上传完成所有文件
    window.isUploadFinished = true;
    
    /**
     * html 加载完成后初始化所有组件
     * @returns {void}
     */
    window.onload = function(){
        initBatchUpload();        //初始批量上传
        initWatermark();          //初始水印
        initSubmit();             //初始提交
        initzTreeDropdown();      //初始树状下拉
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
        $('#myModal').on('hidden.bs.modal', function (e) {
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
        var fileSummary = $('#uploader-container').data('uploader').getFileSummary();
        // 如果失败数、上传中数量、等待上传数量大于0则表示素材文件列表存在未完成上传文件，显示提示
        if(fileSummary.failed > 0 || fileSummary.progress > 0 || fileSummary.queue > 0){
            window.isUploadFinished = false;
        }else{
            window.isUploadFinished = true;
        }
        
        if(!!data){
            mediaBatchUpload.addMediaData(data);
        }
    }
    
    /**
     * 删除上传列表中的文件
     * @param {object} data
     * @returns {undefined}
     */
    function fileDequeued(data){
        if(!!data.dbFile){
            mediaBatchUpload.completed_num -= 1;
            mediaBatchUpload.delMediaData(data.dbFile);
        }
    }

    /************************************************************************************
     *
     * 初始化提交
     *
     ************************************************************************************/ 
    function initSubmit(){
        // 弹出提交结果
        $("#submitsave").click(function(){
            submitValidate();
            validateDirDepDropdownValue($('#zTree-dropdown-value'));
            validateWebuploaderValue($('#euploader-list tbody').find('tr').length, window.isUploadFinished);
            // 如果必选项有错误提示或素材列表存在非上传完成，则返回
            if($('div.has-error').length > 0) return;
            $('#myModal').modal("show");
            var formdata = $('#media-form').serialize();
            mediaBatchUpload.submit(formdata);
        });
        
        // 重新上传
        $("#btn-anewUpload").click(function(){
            $(this).addClass('hidden');
            $table = $('.result-info').find('table.result-table');
            $table.find('tbody').html('');
            var formdata = $('#media-form').serialize();
            mediaBatchUpload.submit(formdata);
        });
    }
    
    /**
     * 验证存储目录下拉框是否有选择值
     * @param {zTreeDropdown} _this
     * @returns {undefined}
     */
    function validateDirDepDropdownValue(_this){
        if(!_this.parents('div.form-group').hasClass('required')) return;

        if(_this.val() == ''){
            var label = _this.parents('div.form-group').find('label.form-label').text();
            var relabel = label.replace('*', "");
            _this.parents('div.form-group').addClass('has-error');
            _this.parents('div.form-group').find('div.help-block').html(relabel.replace('：', "") + '不能为空。');
            setTimeout(function(){
                _this.parents('div.form-group').removeClass('has-error');
                _this.parents('div.form-group').find('div.help-block').html('');
            }, 3000);
        }
    }
    
    /************************************************************************************
     *
     * 初始化树状下拉
     *
     ************************************************************************************/ 
    function initzTreeDropdown(){
        zTreeDropdown('zTree-dropdown', 'zTree-dropdown-name', 'zTree-dropdown-value', {}, treeDataList)
    }
    
</script>