<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\components\aliyuncs\Aliyun;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', '{Batch}{Import}{Media}', [
    'Batch' => Yii::t('app', 'Batch'),  'Import' => Yii::t('app', 'Import'),  'Media' => Yii::t('app', 'Media')
]);

?>

<div class="media-import-index">
    
    <!--警告框-->
    <div class="alert alert-danger alert-dismissible" style="margin-bottom: 0px" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="title">外链媒体上传流程：</div>
        <p>1、从eefile导出excel文件 <?= Html::a('（eefile平台）', ['/build_course/teacher/import'], ['class' => 'alert-link', 'target' => '_black']) ?> </p>
        <p>2、填写上传信息表<?= Html::a('（表格模板下载）', Aliyun::absolutePath('static/doc/template/video_import_template.xlsx?rand='. rand(0, 9999)), ['class' => 'alert-link']) ?></p>
        <p>3、导入上传信息表</p>
        <p>4、配置媒体属性</p>
        <p>5、提交</p>
    </div>
    
    <div class="title">上传信息表：</div>
        
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-import-form',
            'class'=>'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'action' => ['create'],
    ]); ?>

    <div class="uploader">
        <div class="btn btn-pick">选择文件</div>
        <div class="file-box">

            <?= Html::fileInput('importfile', null, [
                'id' => 'importfile',
                'class' => 'file-input',
                'accept' => 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'onchange' => 'uploadForm();'
            ]) ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>
   
</div>

<script type="text/javascript">

    function uploadForm(){
        $('#media-import-form').submit();
    }

</script>