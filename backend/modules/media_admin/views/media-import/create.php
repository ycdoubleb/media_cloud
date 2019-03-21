<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\widgets\zTree\zTreeAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

MediaModuleAsset::register($this);
zTreeAsset::register($this);

$this->title = Yii::t('app', 'Submit upload external chain material');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Upload external chain material'), 'url' => array_merge(['index'], ['category_id' => $category_id])];
$this->params['breadcrumbs'][] = $this->title;

//加载 WATERMARK_DOM 模板
$media_data_tr_dom = str_replace("\n", ' ', $this->render('____media_data_tr_dom'));

?>
<div class="media-import-create">

    <!--警告框-->
    <?= $this->render('____media_warning_box_dom') ?>
    
    <span class="title">
        <?= Yii::t('app', 'Material common attribute configuration:') ?>
    </span>
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ]
    ]); ?>

        <!--存储目录-->
        <div class="form-group field-media-dir_id required">

            <?= Html::label('<span class="form-must text-danger">*</span>' . Yii::t('app', 'Storage Dir') . '：', 'media-dir_id', [
                'class' => 'col-lg-1 col-md-1 control-label form-label'
            ]) ?>

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

        <?= $this->render('____form_attribute_dom', [
            'attrMap' => $attrMap,
            'attrSelected' => isset($attrSelected) ? $attrSelected : null,
            'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
        ]) ?>
        
        <?= $this->render('____media_upload_table_dom', [
            'dataProvider' => $medias
        ]) ?>
        
        <div class="form-group">
            <div class="col-lg-1 col-md-1 clean-padding">
                <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 'class' => 'btn btn-success btn-flat']) ?>
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
    // 所有上传的外链素材
    var medias = <?= json_encode($medias) ?>;
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
            media_url: url,
            media_data_tr_dom : php_media_data_tr_dom,
        });
        
        mediaBatchUpload.init(medias);
        
        // 关闭模态框事件
        $('#myModal').on('hidden.bs.modal', function (e) {
            $table = $('.result-info').find('table.result-table');
            $table.find('tbody').html('');
        });
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
     * @param {DepDropdown} _this
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