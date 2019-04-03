<?php

use common\models\AdminUserr;
use common\models\Config;
use common\modules\rbac\components\Helper;
use common\widgets\Menu;
use yii\helpers\ArrayHelper;

/* @var $user AdminUserr */
?>
<aside class="main-sidebar">
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <?php if(!Yii::$app->user->isGuest): ?>
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $user->avatar; ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= $user->nickname ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <?php 
            $params = Yii::$app->request->queryParams;      // 当前参数
            $config_value = Config::findOne(['config_name' => 'category_id'])->config_value;
            $category_id = ArrayHelper::getValue($params, 'category_id', $config_value);
            $menuItems = [['label' => 'Menu Yii2', 'options' => ['class' => 'header']]];
            if(Yii::$app->user->isGuest){
                $menuItems []= ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest];
            }else{
                $menuItems = array_merge($menuItems, [
                    ['label' => '清除缓存', 'icon' => 'eraser', 'url' => ['/system_admin/cache']],
                    [
                        'label' => '系统',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            ['label' => '配置管理', 'icon' => 'circle-o', 'url' => ['/system_admin/config/index'],],
                            ['label' => '日常任务', 'icon' => 'circle-o', 'url' => ['/system_admin/crontab/index'],],
                            ['label' => '数据库备份', 'icon' => 'database', 'url' => ['/system_admin/db-backup/index']],
                            ['label' => 'Redis缓存管理', 'icon' => 'circle-o', 'url' => ['/rediscache_admin/acl/index']],
                            ['label' => 'Banner管理', 'icon' => 'circle-o', 'url' => ['/system_admin/banner/index']],
                            ['label' => '上传文件管理', 'icon' => 'circle-o', 'url' => ['/system_admin/uploadfile/index']]
                        ],
                    ],
                    [
                        'label' => '权限与组织管理',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            ['label' => '用户列表', 'icon' => 'circle-o', 'url' => ['/user_admin/default/index'],],
                            ['label' => '用户角色', 'icon' => 'circle-o', 'url' => ['/rbac/user-role/index'],],
                            ['label' => '角色列表', 'icon' => 'circle-o', 'url' => ['/rbac/role/index'],],
                            ['label' => '权限列表', 'icon' => 'circle-o', 'url' => ['/rbac/permission/index'],],
                            ['label' => '路由列表', 'icon' => 'circle-o', 'url' => ['/rbac/route/index'],],
                            ['label' => '分组列表', 'icon' => 'circle-o', 'url' => ['/rbac/auth-group/index'],],
                        ],
                    ],
                    [
                        'label' => '素材管理',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            ['label' => '上传素材', 'icon' => 'circle-o', 'url' => array_merge(['/media_admin/media/create'], ['category_id' => $category_id])],
                            ['label' => '素材列表', 'icon' => 'circle-o', 'url' => array_merge(['/media_admin/media/index'], ['category_id' => $category_id])],
                            ['label' => '素材审核', 'icon' => 'circle-o', 'url' => array_merge(['/media_admin/approve/index'], ['category_id' => $category_id])],
                            ['label' => '回收站', 'icon' => 'circle-o', 'url' => array_merge(['/media_admin/recycle/index'], ['category_id' => $category_id])],
                        ],
                    ],
                    [
                        'label' => '素材配置',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            ['label' => '素材类库配置', 'icon' => 'circle-o', 'url' => ['/media_config/category/index']],
                            ['label' => '素材类型配置', 'icon' => 'circle-o', 'url' => ['/media_config/type/index']],
                            ['label' => '存储目录配置', 'icon' => 'circle-o', 'url' => array_merge(['/media_config/dir/index'], ['category_id' => $category_id])],
                            ['label' => '文件后缀配置', 'icon' => 'circle-o', 'url' => ['/media_config/type-detail/index']],
                            ['label' => '素材属性配置', 'icon' => 'circle-o', 'url' => array_merge(['/media_config/attribute/index'], ['category_id' => $category_id])],
                            ['label' => '素材水印配置', 'icon' => 'circle-o', 'url' => ['/media_config/watermark/index']],
                        ],
                    ],
                    [
                        'label' => '运营管理',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            
                            ['label' => '订单列表', 'icon' => 'circle-o', 'url' => ['/operation_admin/order/index']],
                            ['label' => '订单审核', 'icon' => 'circle-o', 'url' => ['/operation_admin/approve/index']],
                            ['label' => '素材运营', 'icon' => 'circle-o', 'url' => array_merge(['/operation_admin/goods/index'], ['category_id' => $category_id])],
                            ['label' => '访问路径', 'icon' => 'circle-o', 'url' => ['/operation_admin/acl/index']],
                            ['label' => '前台用户', 'icon' => 'circle-o', 'url' => ['/operation_admin/user/index']],
                        ],
                    ],
                    [
                        'label' => '统计',
                        'icon' => 'bars',
                        'url' => '#',
                        'items' => [
                            
                            ['label' => '总统计', 'icon' => 'circle-o', 'url' => ['/statistics/all-statistics/index']],
                            ['label' => '排行统计', 'icon' => 'circle-o', 'url' => ['/statistics/ranking-statistics/index']],
                            ['label' => '单独统计', 'icon' => 'circle-o', 'url' => ['/statistics/single-statistics/index']],
                        ]
                    ],
                ]);
                
                foreach($menuItems as &$menuItem){
                    if(isset($menuItem['items'])){
                        $visible = false;
                        foreach($menuItem['items'] as &$item){
                            $item['visible'] = Helper::checkRoute($item['url'][0]);
                            if($item['visible']){
                                $visible = true;
                            }
                        }
                        unset($item);
                        $menuItem['visible'] = $visible;
                    }else if(isset($menuItem['url'])){
                        $menuItem['visible'] = Helper::checkRoute($menuItem['url'][0]);;
                    }
                }
            }
        ?>
        <?= Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree  sidebar-open sidebar-mini', 'data-widget'=> 'tree'],
                'items' => $menuItems,
            ]
        ); ?>

    </section>

</aside>
