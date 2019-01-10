<?php

namespace backend\modules\operation_admin\assets;

use yii\web\AssetBundle;
use const YII_DEBUG;

/**
 * Main backend application asset bundle.
 */
class OperationModuleAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/operation_admin/assets';
    public $baseUrl = '@backend/modules/operation_admin/assets';
    public $css = [
        'css/operation_module.css',
    ];
    public $js = [
      
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
