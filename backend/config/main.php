<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\AdminUser',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        
        'authManager' => [
            'class' => 'common\modules\rbac\RbacManager',
            'cache' => [
                'class' => 'yii\caching\FileCache',
                'cachePath' => dirname(dirname(__DIR__)) . '/frontend/runtime/cache'
            ]
        ],
    ],
    'params' => $params,
    'modules' => [
        //权限控制
        'rbac' => [
            'class' => 'common\modules\rbac\Module',
        ],
        
        //权限控制
        'user_admin' => [
            'class' => 'backend\modules\user_admin\Module',
        ],
        //系统管理
        'system_admin' => [
            'class' => 'backend\modules\system_admin\Module',
        ],
        //媒体管理
        'media_admin' => [
            'class' => 'backend\modules\media_admin\Module',
        ],
        //媒体配置
        'media_config' => [
            'class' => 'backend\modules\media_config\Module',
        ],
        //运营管理
        'operation_admin' => [
            'class' => 'backend\modules\operation_admin\Module',
        ],
        //帮助中心管理
        'helpcenter_admin' => [
            'class' => 'backend\modules\helpcenter_admin\Module',
        ],
    ],
    'params' => $params,
    'as access' => [
        'class' => 'common\modules\rbac\components\AccessControl',
        'allowActions' => [
            'site/*',
            'webuploader/*',
            'ueditor/*',
            'external/*',
            'gii/*',
            'debug/*',
        // The actions listed here will be allowed to everyone including guests.
        // So, 'admin/*' should not appear here in the production, of course.
        // But in the earlier stages of your development, you may probably want to
        // add a lot of actions here until you finally completed setting up rbac,
        // otherwise you may not even take a first step.
        ]
    ],
];
