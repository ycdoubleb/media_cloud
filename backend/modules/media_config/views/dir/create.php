<?php

use common\models\media\Dir;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Dir */

$this->title = Yii::t('app', '{Create}{Dir}', [
    'Create' => Yii::t('app', 'Create'), 'Dir' => Yii::t('app', 'Dir')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Storage}{Dir}', [
    'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dir-create">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'dir-form',
            'class' => 'form form-horizontal',
        ],
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-7 col-md-7\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-1 col-md-1 control-label form-label',
            ],  
        ], 
    ]); ?>
    
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                
                <div class="alert alert-warning alert-dismissible" style="margin-bottom: 0px" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong class="title">输入格式要求：</strong>
                    <p>1、学历/2018年/行政管理（专）/管理英语1/视频 </p>
                    <p>2、学历\2018年\行政管理（专）\管理英语1\视频 </p>
                    <p>3、学历>2018年>行政管理（专）>管理英语1>视频 </p>
                </div>
                
                <!--目录结构-->
                <div class="form-group field-dir_path">
                    <?= Html::label(Yii::t('app', '{Dir}{Path}：', [
                        'Dir' => Yii::t('app', 'Dir'), 'Path' => Yii::t('app', 'Path')
                    ]), 'field-dir_path', ['class' => 'col-lg-12 col-md-12']) ?>
                    <div class="col-lg-12 col-md-12">
                        <?= Html::textarea('dir_path', null, [
                            'id' => 'dir_path', 
                            'class' => 'form-control',
                            'maxlength' => true,
                            'rows' => 6,
                        ]) ?>
                    </div>
                </div>
                
            </div>
            
            <div class="modal-footer">

                <?= Html::button(Yii::t('app', 'Confirm'), [
                    'id' => 'submitsave', 'class' => 'btn btn-primary btn-flat', 'onclick' => 'submitForm();'
                ]) ?>

            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    var url = "<?= Yii::$app->request->getUrl() ?>"
    
    // 提交表单 
    function submitForm(){
        $.post(url, $('#dir-form').serialize(), function(response){
            if(response.code == "0"){
                window.location.reload();
            }
        });
    }

</script>