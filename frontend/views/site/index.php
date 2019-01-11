<?php

use common\models\User;
use common\widgets\ueditor\UEDitor;
use common\widgets\ueditor\UeditorAsset;
use common\widgets\webuploader\ImagePicker;
use common\widgets\webuploader\Webuploader;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

$this->title = 'My Yii Application';
$model = new User([
    'des' => '<p>圭安<span style="color: rgb(255, 0, 0);">塞懂</span>法<span style="font-size: 24px;">守</span>法</p>'
        ]);
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div id="uploader-container">
            <?= Webuploader::widget(['name' => 'files',]); ?>
        </div>
        <div>
            <?php $form = ActiveForm::begin(['id' => 'update-user']); ?>
            <?= $form->field($model, 'nickname')->textInput() ?>
            <?= $form->field($model, 'sex')->checkboxList(['1' => 'a','2'=>'b']) ?>
            <?= $form->field($model, 'sex')->widget(Select2::class,['data' => ['']]) ?>
            <?= $form->field($model, 'avatar')->widget(ImagePicker::class) ?>
            <?= $form->field($model, 'des')->widget(UEDitor::class) ?>
            <?php ActiveForm::end(); ?>
            <?= UEDitor::widget(['id' => 'editor', 'name' => 'des']) ?>
        </div>
    </div>

    <?= Html::a('更新用户1', Url::to(['/site/update-user', "id" => "1"]), ['class' => 'btn btn-default','id' => 'b1']) ?>
    <?= Html::a('查看', "#", ['class' => 'btn btn-default' ,'id' => 'check_btn']) ?>
</div>
<div id="my-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">

</div><!-- /.modal -->
<script>
    function show() {
        console.log($('#myEditor').val());
    }

    window.onload = function () {
        $('#b1').on('click', function () {
            $("my-modal").html("");
            $('#my-modal').modal("show").load($(this).attr('href'));
            return false;
        });
    }
</script>