<?php

use frontend\modules\order_admin\assets\MainAssets;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */


MainAssets::register($this);

//$this->title = Yii::t('app', '我的');

?>

<?php
$menuHtml = '';
$moduleId = Yii::$app->controller->module->id;
$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;

/**
 * 子菜单导航
 * $menuItems = [
 *      菜单分类 => [
 *          module => 模块,
 *          controller => 控制器,
 *          action => 操作方法,
 *          label => 菜单名,
 *          url => 菜单链接,
 *          icons => 图标,
 *          condition => 条件,
 *          options => 菜单配置 
 *      ]
 * ]
 */
$menuItems = [
    'admin' => [
        [
            'module' => 'order_admin',
            'controller' => 'order',
            'action' => 'index',
            'label' => Yii::t('app', '我的订单'),
            'url' => ['/order_admin/order/index'],
            'icons' => null, 
            'condition' => true,
            'options' => ['class' => "links"]
        ],
        [
            'module' => 'order_admin',
            'controller' => 'resources',
            'action' => 'index',
            'label' => Yii::t('app', '我的资源'),
            'url' => ['/order_admin/resources/index'],
            'icons' => null, 
            'condition' => true,
            'options' => ['class' => "links"]
        ],
        [
            'module' => 'order_admin',
            'controller' => 'favorites',
            'action' => 'index',
            'label' => Yii::t('app', '我的收藏'),
            'url' => ['/order_admin/favorites/index'],
            'icons' => null, 
            'condition' => true,
            'options' => ['class' => "links"]
        ],
        [
            'module' => 'order_admin',
            'controller' => 'cart',
            'action' => 'index',
            'label' => Yii::t('app', '我的购物车'),
            'url' => ['/order_admin/cart/index'],
            'icons' => null, 
            'condition' => true,
            'options' => ['class' => 'links']
        ],
    ]
];
end($menuItems['admin']);   //数组中的最后一个元素的值
$lastIndex = key($menuItems['admin']);  //获取数组最后一个的index
//循环组装子菜单导航
foreach ($menuItems as $index => $items) {
    foreach ($items as $key => $value) {
        $is_select = $value['module'] == $moduleId 
            && ($value['controller'] == $controllerId 
               || (is_array($value['controller']) ? in_array($controllerId, $value['controller']) : false));
        if($value['condition']){
            $menuHtml[$index][] = ($is_select ? '<li class="active"><div class="white"></div>' : ($lastIndex == $key ? '<li class="remove">' : '<li class="">')).
                Html::a($value['icons'] . $value['label'], $value['url'], $value['options']).'</li>';
        }
    }
}
$admin = implode("", $menuHtml['admin']);

$userDetails = common\models\User::findOne(Yii::$app->user->id);
$userAvatar = $userDetails->avatar;
$userName = $userDetails->username;
$html = <<<Html
    <!-- 头部 -->
    <header style="height:30px;"></header>
    <!-- 内容 -->
    <div class="container content">
        <div class="left-block">
            <!-- 用户头像块 -->
            <div class="user-block">
                <div class="user-avatar">
                    <img src="{$userAvatar}"/>
                </div>
                <div class="user-name">
                    <span>{$userName}</span>
                </div>
                <div class="set-statistics">
                    <div class="setting">
                        <a href="/order_admin/user-info/setting"><i class="glyphicon glyphicon-cog"></i></a>
                    </div>
                    <div class="statistics">
                        <a href="/order_admin/user-info/statistics"><i class="glyphicon glyphicon-stats"></i></a>
                    </div>
                </div>
            </div>
            <!-- 子菜单 -->
            <nav class="subnav">
                <ul>{$admin}</ul>
            </nav>
        </div>
Html;

    $content = $html . $content . '</div>';
    echo $this->render('@app/views/layouts/main',['content' => $content]); 
?>

