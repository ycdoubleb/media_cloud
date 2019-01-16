<?php

use common\models\media\MediaIssue;
use common\models\vk\CustomerAdmin;
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this View */
/* @var $model CustomerAdmin */

GrowlAsset::register($this);

$this->title = Yii::t('app', '{Feedback}{Problem}',[
    'Feedback' => Yii::t('app', 'Feedback'),
    'Problem' => Yii::t('app', 'Problem')
]);

?>

<div class="feedback main mc-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            <div class="modal-body">
                <div class="mc-form clear-shadow">
                    <?php $form = ActiveForm::begin([
                        'options'=>[
                            'id' => 'form-admin',
                            'class'=>'form-horizontal',
                        ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-10 col-md-10\" style=\"padding-left: 0px; padding-right: 30px\">"
                                                . "{input}</div>\n<div class=\"col-lg-12 col-md-12\">{error}</div>",  
                            'labelOptions' => [
                                'class' => 'col-lg-2 col-md-2',
                                'style' => 'padding-top: 5px; text-align: right;'
                            ],  
                        ], 
                    ]); ?>
                    <!--媒体编号-->
                    <div class="form-group field-mediaissue-content has-success">
                        <label class="col-lg-2 col-md-2" style="text-align: right;" for="mediaissue-content">媒体编号：</label>
                        <div class="col-lg-10 col-md-10" style="padding-left: 0px;">
                            <div class=""><?= $model->media_id;?></div>
                        </div>
                    </div>
                    <!--媒体名称-->
                    <div class="form-group field-mediaissue-content has-success">
                        <label class="col-lg-2 col-md-2" style="text-align: right;" for="mediaissue-content">媒体名称：</label>
                        <div class="col-lg-10 col-md-10" style="padding-left: 0px;">
                            <div class=""><?= $model->media->name;?></div>
                        </div>
                    </div>
                    <!--媒体ID-->
                    <?= Html::activeHiddenInput($model, 'media_id') ?>
                    <!--问题类型-->
                    <?= $form->field($model, 'type')->radioList(MediaIssue::$issueName,[
                        'value' => MediaIssue::ISSUE_OTHER,   // 默认选中值
                        'itemOptions'=>[
                            'labelOptions'=>[
                                'style'=>[
                                    'margin'=>'5px 39px 10px 0',
                                    'color' => '#666666',
                                    'font-weight' => 'normal',
                                ]
                            ]
                        ],
                    ])->label(Yii::t('app', '{Problem}{Type}：',[
                        'Problem' => Yii::t('app', 'Problem'), 'Type' => Yii::t('app', 'Type')
                    ])) ?>
                    <!--问题描述-->
                    <?= $form->field($model, 'content')->textarea(['rows' => 6])
                        ->label(Yii::t('app', '{Problem}{Des}：',[
                            'Problem' => Yii::t('app', 'Problem'),
                            'Des' => Yii::t('app', 'Des')
                        ])) ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            
            <div class="modal-footer">
                <?= Html::button(Yii::t('app', 'Confirm'), [
                    'id'=>'submitsave','class'=>'btn btn-primary',
                    'data-dismiss'=>'modal','aria-label'=>'Close'
                ]) ?>
            </div>
       </div>
    </div>   
</div>

<?php
$js = <<<JS
    //提交表单
    $("#submitsave").click(function(){
        $.post("../media/feedback?id={$model->media_id}", $('#form-admin').serialize(),function(data){
            if(data['code'] == '0'){
                $.notify({
                    message: data['msg']
                },{
                    type: 'success'
                });
            }else{
                $.notify({
                    message: data['msg']
                },{
                    type: 'danger'
                });
            }
        });
    });   
JS;
    $this->registerJs($js,  View::POS_READY);
?>
