<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\widgets\zTree\zTreeDropDown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Create}{Medias}', [
    'Create' => Yii::t('app', 'Create'), 'Medias' => Yii::t('app', 'Medias')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Medias}{List}', [
    'Medias' => Yii::t('app', 'Medias'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//加载 MEDIADATATR DOM 模板
$media_data_tr_dom = str_replace("\n", ' ', $this->render('____media_data_tr_dom'));

?>
<div class="media-create">

    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-8 col-md-8\">{input}</div>\n<div class=\"col-lg-8 col-md-8\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>
    
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">
                <?= Yii::t('app', 'Basic Info') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a href="#config" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', 'Transcoding Config') ?>
            </a>
        </li>
        <?= Html::a(Yii::t('app', 'Upload external chain material'), array_merge(['media-import/index'], ['category_id' => $category_id]), [
            'class' => 'pull-right', 'style' => 'display: block;margin-top: 10px;margin-right: 10px'
        ]) ?>
    </ul>
    
    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <!--存储目录-->
            <?= $form->field($model, 'dir_id')->widget(zTreeDropDown::class, [
                'data' => $dirDataProvider,
                'url' => [
                    'view' => Url::to(['/media_config/dir/search-children', 'category_id' => $category_id]),
                    'create' => Url::to(['/media_config/dir/add-dynamic', 'category_id' => $category_id]),
                    'update' => Url::to(['/media_config/dir/edit-dynamic']),
                    'delete' => Url::to(['/media_config/dir/delete']),
                ],
            ])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Storage Dir') . '：') ?>
            
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
    // 素材链接
    var url = '<?= Url::to(['create', 'category_id' => $category_id]) ?>';
    //素材 tr dom
    var php_media_data_tr_dom = '<?= $media_data_tr_dom ?>';
    //批量上传控制器
    var mediaBatchUpload;
    //上传工具的素材
    var uploaderMedias = [];
    //树状图展示
    var treeDataList = <?= json_encode($dirDataProvider) ?>;
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
            media_url: url,
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
            if(mediaBatchUpload.completed_num > 0){
                mediaBatchUpload.completed_num -= 1;
            }
            mediaBatchUpload.delMediaData(data.dbFile);
        }
    }
    
    /**
     * 是否已上传完成所有文件
     * @returns {Boolean}
     */
    function isFileUploadFinished(){
        var fileSummary = $('#uploader-container').data('uploader').getFileSummary();
        // 如果失败数、上传中数量、等待上传数量大于0则表示素材文件列表存在未完成上传文件，显示提示
        if(fileSummary.failed > 0 || fileSummary.progress > 0 || fileSummary.queue > 0){
            return false;
        }else{
            return true;
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
            validateDirDepDropdownValue($('#media-dir_id'));
            validateWebuploaderValue($('#euploader-list tbody').find('tr').length, isFileUploadFinished());
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
    
</script>