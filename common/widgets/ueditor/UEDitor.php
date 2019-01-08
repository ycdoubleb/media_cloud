<?php

namespace common\widgets\ueditor;

use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * UEDitor 富文本编辑器
 *
 * @author Administrator
 */
class UEDitor extends InputWidget{
    /**
     * 插件选项
     * @var array 
     */
    public $pluginOptions = [
        initialFrameHeight => 200,
        maximumWords => 100000,
        toolbars => [
            [
                'fullscreen', 'source', '|',
                'paragraph', 'fontfamily', 'fontsize', '|',
                'forecolor', 'backcolor', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', '|',
                'justifyleft', 'justifyright', 'justifycenter', 'justifyjustify', '|',
                'insertorderedlist', 'insertunorderedlist', 'simpleupload', 'horizontal', '|',
                'selectall', 'cleardoc',
                'undo', 'redo',
            ]
        ]
    ];

    public function __construct($config = array()) {
        $config['pluginOptions'] = array_merge($this->pluginOptions, isset($config['pluginOptions']) ? $config['pluginOptions'] : []);
        parent::__construct($config);
    }
    
    public function registerAssets() {
        $view = $this->getView();
        //获取flash上传组件路径
        $sourcePath = $view->assetManager->getPublishedUrl(UeditorAsset::register($view)->sourcePath);
        
        //设置组件配置
        $this->pluginOptions['sourcePath'] = $sourcePath;
        
        $config = Json::encode($this->pluginOptions);
        
        $js = <<< JS
            var editor = new UE.getEditor('$this->id',$config);
            //保存editor与div关系
            $("#$this->id").data('editor',editor);
JS;
        $view->registerJs($js, View::POS_READY);
    }
}
