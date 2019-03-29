<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

MediaModuleAsset::register($this);

$this->title = Yii::t('app', 'Upload external chain material');

?>

<div class="media-import-index">
    
    <!--警告框-->
    <?= $this->render('____media_warning_box_dom') ?>
    
    <span class="title">
        <?= Yii::t('app', 'Upload information table:') ?>
    </span>
        
    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'media-import-form',
            'class'=>'form form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'action' => array_merge(['create'], ['category_id' => $category_id]),
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