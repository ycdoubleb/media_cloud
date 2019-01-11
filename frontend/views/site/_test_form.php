<?php

use common\models\User;
use common\widgets\ueditor\UEDitor;
use common\widgets\webuploader\ImagePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model User */
$model;
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modal title</h4>
        </div>
        <div class="modal-body">
            <?php $form = ActiveForm::begin(['id' => 'update-user-form']); ?>
            <?= $form->field($model, 'nickname')->textInput() ?>
            <?= $form->field($model, 'avatar')->widget(ImagePicker::class, ['id' => '111']) ?>
            <?= $form->field($model, 'des')->widget(UEDitor::class, ['id' => 'des-editor-form','pluginOptions' => ['zIndex' => 2000]]) ?>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <?= Html::a('提交', Url::to(['/site/update-user', 'id' => $model->id]), ['id' => 'submit_btn', 'class' => 'btn btn-primary']) ?>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#submit_btn').on('click', function () {
        $.post($(this).attr('href'), $('#update-user-form').serializeArray(), function (r) {
            if (r.code == "0") {
                $('#my-modal').modal('hide');
            } else {
                alert("更新出错:"+r.msg);
            }
        });
        return false;
    });
    $('#my-modal').on('hide.bs.modal', function (e) {
        //清除
        $('#my-modal').off('hide.bs.modal');
        $('#des-editor-form').data('editor').destroy();
    });
</script>