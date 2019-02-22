<?php

use common\models\order\Favorites;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Favorites */
/* @var $form ActiveForm */
?>

<div class="favorites-form main-search">
    <div class="mc-form">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'id' => 'order-admin-form',
                'class' => 'form-horizontal',
            ],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-12 col-md-12\">{input}</div>\n",
                'labelOptions' => [
                    'class' => 'control-label form-label',
                ], 
            ],
        ]);?>
        
        <div class="col-log-12 col-md-12">
            <div class="col-log-6 col-md-6" style="padding-left: 5px;">
                <div class="col-log-10 col-md-10 search-name">
                    <?= $form->field($searchModel, 'keyword')->textInput([
                        'placeholder' => '素材编号，素材名称', 'maxlength' => true,
                    ])->label('');?>
                </div>
                <div class="col-log-2 col-md-2 form-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-default btn-flat-lg']) ?>
                </div>
            </div>
            <div class="col-log-6 col-md-6" style="padding-right: 5px;">
                <div class="pull-right">
                    <?php
                    echo Html::a('加入购物车', 'javascript:;', ['id' => 'add-carts',
                        'data-url' => Url::to(['favorites/add-cart']),
                        'class' => 'btn btn-highlight btn-flat-lg', 'title' => '加入购物车',
                    ]);
                    echo Html::a('取消收藏', 'javascript:;', ['id' => 'del-favorites',
                        'data-url' => Url::to(['favorites/del-favorites']),
                        'style' => 'margin-right:0px',
                        'class' => 'btn btn-default btn-flat-lg', 'title' => '取消收藏']);
                    ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = <<<JS
    // 添加到购物车
    $("#add-carts").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            ids += $(this).parents('tr').attr('data-value')+',';                  
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        var url=$(this).attr('data-url');
        $.post(url, {ids}, function(rel){
            location.reload();  //刷新页面
            // 把复选框全取消
//            $('input[name="selection_all"]').prop("checked",false);
//            $('input[name="selection[]"]').each(function(){
//                $(this).prop("checked",false);
//            });
//            if(rel['code'] == '0'){
//                $.notify({
//                    message: '成功加入购物车' 
//                },{
//                    type: 'success'
//                });
//            }else{
//                $.notify({
//                    message: rel['msg'] 
//                },{
//                    type: 'danger'
//                });
//            }
        });
    });
    
    // 取消收藏
    $("#del-favorites").click(function(){
        var many_check = $("input[name='selection[]']:checked");
        var ids = "";
        $(many_check).each(function(){
            console.log($(this).parents('tr').attr('data-value'));
            ids += $(this).parents('tr').attr('data-value')+',';                       
        });
        // 去掉最后一个逗号
        if (ids.length > 0) {
            ids = ids.substr(0, ids.length - 1);
        }else{
            alert('请选择至少一条记录！'); return false;
        }
        var url=$(this).attr('data-url');
        $.post(url, {ids}, function(rel){
            location.reload();  //刷新页面
        });
    });
JS;
$this->registerJs($js, View::POS_READY);
?>
