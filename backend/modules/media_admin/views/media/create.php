<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Dir;
use common\models\media\Media;
use common\widgets\depdropdown\DepDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
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
        <?= Html::a('上传外链媒体', ['media-import/index'], ['class' => 'pull-right', 'style' => 'display: block;margin-top: 10px;margin-right: 10px']) ?>
    </ul>
    
    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <!--存储目录-->
            <?= $form->field($model, 'dir_id', [
                'template' => "{label}\n"
                . "<div class=\"col-lg-7 col-md-7\">"
                    . "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}"
                        . "<div class=\"col-lg-1 col-md-1 clean-padding\">"
                            . "<a href=\"/media_config/dir/create\" class=\"btn btn-default\" onclick=\"showModal($(this)); return false;\">新建目录</a>"
                        . "</div>"
                    . "</div>\n"
                    . "<div class=\"col-lg-12 col-md-12 clean-padding\">{error}</div>"
                . "</div>", 
                'labelOptions' => [
                    'class' => 'col-lg-1 col-md-1 control-label form-label',
                ],  
            ])->widget(DepDropdown::class,[
                'pluginOptions' => [
                    'url' => Url::to(['/media_config/dir/search-children']),
                    'max_level' => 10,
                    'onChangeEvent' => new JsExpression("function(){ validateDirDepDropdownValue($('.dep-dropdown').children('select')) }")
                ],
                
                'items' => Dir::getDirsBySameLevel(null, Yii::$app->user->id, true, true),
                'itemOptions' => [
                    'style' => 'width: 175px; display: inline-block;',
                ],
            ])->label('<span class="form-must text-danger">*</span>' . Yii::t('app', '{Storage}{Dir}：', [
                'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
            ])) ?>
            
            <?= $this->render('____form_attribute_dom', [
                'attrMap' => $attrMap,
                'isTagRequired' => $isTagRequired,
                'attrSelected' => isset($attrSelected) ? $attrSelected : null,
                'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
            ]) ?>

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
            <?= Html::label(null, null, ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
            <div class="col-lg-11 col-md-11">
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
    //媒体 tr dom
    var php_media_data_tr_dom = '<?= $media_data_tr_dom ?>';
    //批量上传控制器
    var mediaBatchUpload;
    //上传工具的媒体
    var uploaderMedias = [];
    //是否已上传完成所有文件
    window.isUploadFinished = false;
    
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
        mediaBatchUpload.addMediaData(data);
    }
    
    /**
     * 删除上传列表中的文件
     * @param {object} data
     * @returns {undefined}
     */
    function fileDequeued(data){
        mediaBatchUpload.completed_num -= 1;
        mediaBatchUpload.delMediaData(data.dbFile);
    }
    
    /**
     * 完成上传列表中的所有文件
     * @param {object} data
     * @returns {undefined}
     */
    function uploadFinished(data){        
        window.isUploadFinished = true;
    }

    /************************************************************************************
     *
     * 初始化提交
     *
     ************************************************************************************/ 
    function initSubmit(){
        // 弹出提交结果
        $("#submitsave").click(function(){
            validateDirDepDropdownValue($('.dep-dropdown').children('select'));
            submitValidate();
            validateWebuploaderValue(mediaBatchUpload.medias.length);
            if($('div.has-error').length > 0 || !window.isUploadFinished) return;
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
     * @param {DepDropdown} _this
     * @returns {undefined}
     */
    function validateDirDepDropdownValue(_this){
        validateDepDropdownValue(_this);
    }
    
</script>