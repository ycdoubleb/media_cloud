<?php

namespace backend\modules\statistics\assets;

use yii\web\AssetBundle;
use const YII_DEBUG;

/**
 * Main backend application asset bundle.
 */
class StatisticsModuleAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/statistics/assets';
    public $baseUrl = '@backend/modules/statistics/assets';
    public $css = [
        'css/statistics_module.css',
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
