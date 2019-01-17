<?php

namespace backend\modules\media_admin\assets;

use yii\web\AssetBundle;
use const YII_DEBUG;

/**
 * Main backend application asset bundle.
 */
class MediaModuleAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/media_admin/assets';
    public $baseUrl = '@backend/modules/media_admin/assets';
    public $css = [
        'css/media_module.css',
    ];
    public $js = [
        'js/media-batch-upload.js',
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
