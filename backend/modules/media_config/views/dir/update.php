<?php

use common\models\media\Dir;
use common\widgets\depdropdown\DepDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Dir */

$this->title = Yii::t('app', "{Update}{Dir}", [
    'Update' => Yii::t('app', 'Update'), 'Dir' => Yii::t('app', 'Dir')
]);

?>
<div class="dir-update">

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
    
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                
                <!--名称-->
                <?= $form->field($model, 'name')->textInput([
                    'placeholder' => Yii::t('app', 'Input Placeholder'), 'maxlength' => true
                ]) ?>

                <!--所属父级-->
                <?php
                    $id = Yii::$app->request->getQueryParam('id');
                    //默认情况下的值
                    $max_level = 1;
                    $items = Dir::getDirsBySameLevel(null, Yii::$app->user->id);
                    $values = [];
                    //如果有传参id，则拿传参id的Dir模型
                    if($id != null){
                        $dir = Dir::getDirById($id);
                        $dirsBySameLevel = Dir::getDirsBySameLevel($dir->id, Yii::$app->user->id, true, true);
                        //max_level = 传参id的Dir模型的level
                        $max_level = $dir->level;
                        //如果传参id的Dir模型的parent_id非0，则执行
                        if($dir->parent_id != 0){
                            //values = 传参id的Dir模型的父级path
                            $values = array_values(array_filter(explode(',', Dir::getDirById($dir->parent_id)->path)));
                        }
                        //如果是【更新】的情况下
                        if(!$model->isNewRecord){
                            //items = 传参id的Dir模型的当前（包括父级）分类同级的所有分类(不包含自己)
                            foreach ($dirsBySameLevel as $index => $dirLevel){
                                if(in_array($dir->id, array_keys($dirLevel))){
                                    unset($dirsBySameLevel[$index][$dir->id]);
                                    break;
                                }
                            }
                            $items = $dirsBySameLevel;
                        //【创建】的情况下
                        }else{
                            //items = 传参id的Dir模型的当前（包括父级）分类同级的所有分类
                            $items = $dirsBySameLevel;
                            //$values = [传参id的Dir模型的id] and 传参id的Dir模型的父级path
                            $values = array_merge($values, [$dir->id]);
                        }
                        
                        echo $form->field($model, 'parent_id')->widget(DepDropdown::class,[
                            'pluginOptions' => [
                                'url' => Url::to(['search-children', 'target_id' => $model->id]),
                                'max_level' => $max_level,
                            ],
                            'items' => $items,
                            'values' => $values,
                            'itemOptions' => [
                                'style' => 'width: 135px; display: inline-block;',
                                'disabled' => $model->isNewRecord || $id == null ? true : false,
                            ],
                        ]);
                    }
                ?>
                
                <!--是否启用-->
                <?php
//                    echo $form->field($model, 'is_del')->checkbox(['value' => 1, 'style' => 'margin-top: 14px'], false)->label(Yii::t('app', 'Is Use')) 
                ?>
                
            </div>
            
            <div class="modal-footer">

                <?= Html::submitButton(Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary btn-flat']) ?>

            </div>
                
       </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
