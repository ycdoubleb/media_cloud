<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            //生产机
            //'dsn' => 'mysql:host=172.16.146.156;dbname=mediacloud',
            //'username' => 'mediacloud',
            //'password' => 'eecn.cn',
            //生产机
            
            //测试机
            'dsn' => 'mysql:host=172.16.146.156;dbname=mediacloud',
            'username' => 'mediacloud',
            'password' => 'eecn.cn',
            //测试机
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'tablePrefix' => 'mc_'   //加入前缀名称fc_
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
