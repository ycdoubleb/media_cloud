<?php

use common\components\aliyuncs\Aliyun;
use common\widgets\webuploader\ImagePicker;
use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* 线下支付页 */

$this->title = Yii::t('app', 'Offline Payment');

MainAssets::register($this);
ModuleAssets::register($this);

?>

<div class="order_admin mediacloud">
    <!--头部信息-->
    <div class="header play-approve">
        <div class="container">
            <div class="media-top">
                <div class="pull-left">
                    <div class="cloud-name">
                        <span class="cloud-title">资源在线</span>
                        <span class="cloud-website">www.resonline.com</span>
                    </div>
                    <div class="cloud-cart">线下支付 <span class="glyphicon glyphicon-question-sign"></span></div>
                </div>
                <div class="pull-right">
                    <img src="/imgs/site/pay.png">
                </div>
            </div>
            <!--警告框-->
            <div class="alert alert-danger alert-dismissible" style="margin-bottom: 0px" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p class="alert-link">线下支付审批</p>
                <p>说明：本审批xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx!</p>
                <p></p>
                <p>流程：1、下载线下支付审批申请模板<?= Html::a('（下载）', ['download', 'id' => $model->order_id], 
                        ['class' => 'alert-link']) ?>，
                    媒体清单<?= Html::a('（下载）', ['export-list', 'id' => $model->order_id], ['class' => 'alert-link'])?>
                </p>
                <p style="text-indent: 3em;">2、完成必要信息填写。</p>
                <p style="text-indent: 3em;">3、回到本页面上传线下支付审批申请（支付凭证）。</p>
                <p style="text-indent: 3em;">4、等候审核通过。</p>
            </div>
            <!--支付凭证表单-->
            <div class="information main">
                <span>填写支付信息</span>
                <div class="approve-form mc-form">
                    <?php $form = ActiveForm::begin([
                        //'action' => ['play-approve'],
                        'method' => 'post',
                        'options' => [
                            'id' => 'approve-form',
                            'class' => 'form-horizontal',
                        ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-11 col-md-11\" style=\"padding-left: 0;\">{input}</div>\n",
                            'labelOptions' => [
                                'class' => 'col-lg-1 col-md-1 control-label form-label',
                                'style' => 'padding-left: 0;'
                            ], 
                        ],
                    ]);?>
                    <!--媒体ID-->
                    <?= Html::activeHiddenInput($model, 'order_id') ?>

                    <?= $form->field($model, 'content')->textInput([
                            'placeholder' => '填写支付说明', 'maxlength' => true,
                        ])->label('支付说明：');?>

                    <?= $form->field($model, 'certificate_url')->widget(ImagePicker::class)->label('支付凭证：'); ?>

                    <?php ActiveForm::end(); ?>
                </div>

                <?= Html::button(Yii::t('app', 'Submit'), ['id' => 'submitsave', 'class' => 'btn btn-highlight btn-flat']) ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    //提交表单 
    $("#submitsave").click(function(){
        $('#approve-form').submit();
    })
JS;
    $this->registerJs($js,  View::POS_READY);
?>