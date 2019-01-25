<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Dir;
use common\models\media\Media;
use common\widgets\depdropdown\DepDropdown;
use common\widgets\pagination\PaginationAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

MediaModuleAsset::register($this);
PaginationAsset::register($this);

$this->title = Yii::t('app', '{Submit}{Media}', [
    'Submit' => Yii::t('app', 'Submit'), 'Media' => Yii::t('app', 'Media')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//加载 WATERMARK_DOM 模板
$media_data_tr_dom = str_replace("\n", ' ', $this->render('____media_data_tr_dom'));

?>
<div class="media-import-create">

    <!--警告框-->
    <?= $this->render('____media_warning_box_dom') ?>
    
    <span class="title">媒体公共属性配置：</span>
    
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-form',
            'class' => 'form form-horizontal',
        ]
    ]); ?>

        <!--存储目录-->
        <?= $form->field($model, 'dir_id', [
            'template' => "{label}\n"
            . "<div class=\"col-lg-7 col-md-7\">"
                . "<div class=\"col-lg-12 col-md-12 clean-padding\">{input}</div>\n"
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
            'attrSelected' => isset($attrSelected) ? $attrSelected : null,
            'tagsSelected' => isset($tagsSelected) ? $tagsSelected : null ,
        ]) ?>
        
        <?= $this->render('____media_upload_table_dom', [
            'dataProvider' => $medias
        ]) ?>
        
        <div class="form-group">
            <div class="col-lg-11 col-md-11">
                <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 'class' => 'btn btn-success btn-flat']) ?>
            </div> 
        </div>
            
    <?php ActiveForm::end(); ?>  

</div>

<!--模态框-->
<?= $this->render('____submit_result_info_dom') ?>

<script type="text/javascript">
    //媒体 tr dom
    var php_media_data_tr_dom = '<?= $media_data_tr_dom ?>';
    // 所有上传的外链媒体
    var medias = <?= json_encode($medias) ?>;
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
        
        mediaBatchUpload.init(medias);
        
        // 关闭模态框事件
        $('.myModal').on('hidden.bs.modal', function (e) {
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
            validateDirDepDropdownValue($('.dep-dropdown').children('select'));
            submitValidate();
            if($('div.has-error').length > 0) return;
            $('.myModal').modal("show");
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