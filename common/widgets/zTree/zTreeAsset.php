<?php

namespace common\widgets\zTree;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class zTreeAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/zTree';
    public $css = [
        'css/zTree-dropdown.css',
        'css/bootstrapStyle/bootstrapStyle.css',
    ];
    public $js = [
        'js/jquery.ztree.core.min.js',
        'js/jquery.ztree.excheck.min.js',
        'js/jquery.ztree.exedit.min.js',
        'js/ztree-dropdown.js',
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
