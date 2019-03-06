<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
?>

<?php

NavBar::begin([
    'brandImage' => '/imgs/site/logo.png?rand='. rand(1, 10),
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
]);

$menuItems = [
    ['label' => '素材库', 'url' => ['/media_library/media'], 'visible' => !Yii::$app->user->isGuest],
    ['label' => '个人中心', 'url' => ['/order_admin/order'], 'visible' => !Yii::$app->user->isGuest],
];

$moduleId = Yii::$app->controller->module->id;   //模块ID
if ($moduleId == 'app-frontend') {
    //站点经过首页或登录，直接获取当前路由
    $route = Yii::$app->controller->getRoute();
} else {
    $urls = [];
    $vals = [];
    $menuUrls = ArrayHelper::getColumn($menuItems, 'url');
    foreach ($menuUrls as $url) {
        $urls[] = array_filter(explode('/', $url[0]));
    }
    $lastUrls = end($urls);     //获取最后一个模型URL（课工厂模块）
    foreach ($urls as $val) {
        $vals[$val[1]] = implode('/', $val);
    }
    try {
        $route = substr($vals[$moduleId], 0);
    } catch (Exception $ex) {
        $route = Yii::$app->controller->getRoute();
    }
}
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-left'],
    //'encodeLabels' => false,
    'items' => $menuItems,
    'activateParents' => false, //启用选择【子级】【父级】显示高亮
    'route' => $route,
]);

//购物车
$cat = common\models\order\Cart::findAll(['is_del' => 0, 'created_by' => Yii::$app->user->id]); 
$cat_num = count($cat) > 99 ? '99' : count($cat);
$is_hidden = $cat_num == 0 ? 'hidden' : '';

$menuItems = [
    [
        'label' => "<i class='glyphicon glyphicon-shopping-cart'><div id='cart-num' class='cart-num $is_hidden'>$cat_num</div></i>",
        'url' => ['/order_admin/cart/index'],
        'visible' => !Yii::$app->user->isGuest,
        'encode' => false,
    ],
    [
        'label' => !Yii::$app->user->isGuest ? Html::img(Yii::$app->user->identity->avatar, 
                ['width' => 33, 'height' => 33, 'class' => 'img-circle']) : null,
        'url' => "#",
        'options' => ['class' => 'logout'],
        'linkOptions' => ['class' => 'logout'],
        'items' => [
            [
                'label' => '<span class="nickname">' . (Yii::$app->user->isGuest ? "游客" : Yii::$app->user->identity->nickname ) . '</span>',
                'encode' => false,
            ],
            [
                'label' => '<i class="fa fa-sign-out"></i>' . Yii::t('app', 'Login Out'),
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post', 'class' => 'logout'],
                'encode' => false,
            ],
        ],
        'visible' => !Yii::$app->user->isGuest,
        'encode' => false,
    ],
    //未登录
    ['label' => Yii::t('app', 'Signup'), 'url' => ['/site/signup'], 'visible' => Yii::$app->user->isGuest],
    ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login'], 'visible' => Yii::$app->user->isGuest],
];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'activateItems' => false,
    'items' => $menuItems,
]);
NavBar::end();
?>

<?php

$js = <<<JS
   

JS;
$this->registerJs($js, View::POS_READY);
?>
