<?php

use frontend\assets\SiteAssets;
use frontend\assets\TimerButtonAssets;
use frontend\models\SignupForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model SignupForm */

SiteAssets::register($this);
TimerButtonAssets::register($this);

$this->title = Yii::t('app', 'Signup');

?>

<div class="site-signup">
    <div class="mediacloud">
        <div class="content">
        <?php $form = ActiveForm::begin([
            'options' => [
                'id' => 'msform',
                'class' => 'form-horizontal',
                'enctype' => 'multipart/form-data',
                'onkeydown' => 'if(event.keyCode==13){return false;}', //去掉form表单的input回车提交事件
            ],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-12 col-md-12\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",
            ],
        ]); ?>
            <!-- progressbar -->
            <ul id="progressbar">
                <li class="active">设置账号信息</li>
                <li>设置用户信息</li>
            </ul>

            <!-- fieldsets 账号密码/联系方式 -->
            <fieldset>
                <h2 class="fs-title">账号信息</h2>
                <h3 class="fs-subtitle">填写您的账号密码及联系方式</h3>
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true,
                    'placeholder' => '手机号...'])->label('') ?>
                    <!--注释信息-->
                    <div id="user-phone-info" class="name-info"><span></span></div>
                <div class="form-group field-user-code required">
                    <div class="col-lg-12 col-md-12" style="width: 100%; display: inline-block">
                        <?= Html::input('input', 'User[code]', '', [
                            'placeholder' => '验证码...',
                            'id' => 'code', 'class' => 'form-control',
                            'style' => 'width:55%; float:left; display:inline-block'
                        ])?>
                        <div id="j_getVerifyCode" class="time_button disabled">获取手机验证码</div>
                    </div>
                    <!--注释信息-->
                    <div id="code-info" class="name-info"><span></span></div>
                </div>
                <?= $form->field($model, 'password_hash')->passwordInput(['minlength' => 6,'maxlength' => 20,
                    'placeholder' => '密码...'])->label('') ?>

                <input type="button" name="next" id="user-next" class="action-button" value="下一步" />

            </fieldset>

            <!-- fieldsets 姓名/公司/部门 -->
            <fieldset>
                <h2 class="fs-title">账号信息</h2>
                <h3 class="fs-subtitle">填写您的姓名、公司和部门名称</h3>
                <?= $form->field($model, 'nickname')->textInput(['maxlength' => true,
                    'placeholder' => '真实姓名...'])->label('') ?>
                <!--公司名称-->
                <div class="form-group field-company required">
                    <div class="col-lg-12 col-md-12" style="width: 100%; display: inline-block">
                        <?= Html::input('input', 'User[company]', '', [
                            'placeholder' => '公司名称...',
                            'id' => 'company', 'class' => 'form-control',
                        ])?>
                    </div>
                </div>
                <!--部门名称-->
                <div class="form-group field-department required">
                    <div class="col-lg-12 col-md-12" style="width: 100%; display: inline-block">
                        <?= Html::input('input', 'User[department]', '', [
                            'placeholder' => '部门名称...',
                            'id' => 'department', 'class' => 'form-control',
                        ])?>
                    </div>
                </div>

                <input type="button" name="previous" class="previous action-button" value="上一步" />
                <input type="submit" name="submit" class="submit action-button" value="提交" />

            </fieldset>
        <?php ActiveForm::end();?>
        </div>
    </div>
</div>

<?php

$js = <<<JS
    /** 滚动到登录框 */
    if(window.innerHeight < 800){
        $('html,body').animate({scrollTop: ($(".content").offset().top) - 100}, 200);
    };
        
    //检查号码是否已被注册
    $("#user-phone").change(function(){
        $.post("/site/chick-phone",{'phone': $("#user-phone").val()}, function(data){
            if(data['code'] != 0){
                $("#j_getVerifyCode").addClass('disabled');
                $("#user-phone-info > span").html('该号码已被注册!请<a href="/site/login">直接登录</a>');
            }else if($("#user-phone").val().length == 11){
                $("#j_getVerifyCode").removeClass('disabled');
            }
        })
    });
        
    //检查验证码是否正确
    $("#code").change(function(){
        $.post("/site/proving-code",{'code': $("#code").val()},function(data){
            if(data['code'] == 0){
                $("#code").after('<i class="glyphicon glyphicon-ok-sign icon-y"></i>');
            }else{
                $("#code").after('<i class="glyphicon glyphicon-remove-sign icon-n"></i>');
                $("#code-info > span").html(data['msg']);
            }
        });
    });
    $("#user-phone").bind("input propertychange change",function(event){
        $("#user-phone-info > span").empty();  //移除注释
    });
    $("#code").bind("input propertychange change",function(event){
        $(".glyphicon").remove();               //移除右侧图标
        $("#code-info > span").empty();  //移除注释
    });

    //提交表单
    $(".submit").click(function(){
        $.post("/site/signup",$('#msform').serialize());
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>