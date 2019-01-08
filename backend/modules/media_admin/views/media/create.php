<?php

use backend\modules\media_admin\assets\ModuleAsset;
use common\models\media\Dir;
use common\models\media\Media;
use common\widgets\depdropdown\DepDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Media */

ModuleAsset::register($this);

$this->title = Yii::t('app', '{Create}{Media}', [
    'Create' => Yii::t('app', 'Create'), 'Media' => Yii::t('app', 'Media')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                'items' => Dir::getDirsBySameLevel($model->dir_id, null, true, true),
                'itemOptions' => [
                    'style' => 'width: 175px; display: inline-block;',
                ],
            ])->label(Yii::t('app', '{Storage}{Dir}：', [
                'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
            ])) ?>
            
            <?= $this->render('____form_attribute_dom', [
                'attrMap' => $attrMap
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
            ]) ?>
            
        </div>
    
        <div class="form-group">
            <?= Html::label(null, null, ['class' => 'col-lg-1 col-md-1 control-label form-label']) ?>
            <div class="col-lg-11 col-md-11">
                <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 'class' => 'btn btn-success btn-flat']) ?>
                <span id="submit-result"></span>
            </div> 
        </div>
    
    </div>  
        
    <?php ActiveForm::end(); ?>  

</div>

<?php
$js = <<<JS
        
    // 初始化
    window.mediaBatchUpload = new mediaupload.MediaBatchUpload();
        
    /** 上传完成 */
    $(mediaBatchUpload).on('submitFinished',function(){
        var max_num = this.medias.length;
        var completed_num = 0;
        $.each(this.medias,function(){
            if(this.submit_result){
                completed_num++;
            }
        });
        $('#submit-result').html("共有 "+max_num+" 个需要上传，其中 "+completed_num+" 个成功， "+(max_num - completed_num)+" 个失败！");
    });
        
    // 提交表单    
    $("#submitsave").click(function(){
        var formdata = $('#media-form').serialize();
        window.mediaBatchUpload.submit(formdata);
    });

JS;
    $this->registerJs($js,  View::POS_READY);
?>