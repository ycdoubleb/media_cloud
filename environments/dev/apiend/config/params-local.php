<?php
return [
    /* 接口加密 aes128加密 */
    'encryption' => [
        'secret_key' => 'api.mediacloud',        //密码
        'method' => 'aes-128-ecb',              //加密方法
        'options' => OPENSSL_RAW_DATA,          //选项
    ],
    /* 素材配置 */
    'media' => [
        'use' => [
            //外部访问素材地址路径 eg:url?sn=xxx
            'link_url' => 'http://tt.mediacloud.studying8.com/media/use/link',
            //外部访问临时素材地址路径 eg:url?sn=xxx
            'temp_link_url' => 'http://tt.mediacloud.studying8.com/media/use/temp-link',
            //外部下载临时素材地址路径 eg:url?sn=xxx
            'temp_download_url' => 'http://tt.mediacloud.studying8.com/media/use/temp-download',
        ]
    ]
];
