<?php

namespace common\widgets\webuploader;

use yii\web\AssetBundle;

class WebUploaderAsset extends AssetBundle
{
    public $css = [
        //'style.css',  //样式冲突
        'euploader.css',
    ];
    public $js = [
        'webuploader.js',
        'require_wskeee.js',
        'euploader_v2.min.js',
        'bootstrap-paginator.min.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
    
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
    }
}
