<?php

namespace backend\modules\media_admin\assets;

use yii\web\AssetBundle;
use const YII_DEBUG;

/**
 * Main backend application asset bundle.
 */
class ModuleAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/media_admin/assets';
    public $baseUrl = '@backend/modules/media_admin/assets';
    public $css = [
        'css/module.css',
    ];
    public $js = [
        'js/media-batch-upload.js',
        'js/media-batch-operate.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}
