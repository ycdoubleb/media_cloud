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
    <?= $this->render('____media_warning_box_dom') ?>
    
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