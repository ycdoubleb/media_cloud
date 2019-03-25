<?php

namespace common\widgets\zTree;

use ReflectionException;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Description of zTreeDropDown
 *
 * @author Kiwi
 */
class zTreeDropDown extends InputWidget {
    
    /** 搜索类型 */
    const TYPE_SEARCH = 'search';
    
    /** 编辑类型 */
    const TYPE_EDITED = 'edited';

    /**
     * 初始数据
     * @var array 
     */
    public $data = [];
    
    /**
     * 操作链接
     * @var type 
     */
    public $url = [
        'view' => '', 
        'create' => '',
        'update' => '',
        'delete' => '' 
    ];

    /**
     * 组件参数
     * @var array 
     */
    public $pluginOptions = [
        'type' => self::TYPE_EDITED,
        'data' => [
            'simpleData' => [
                'enable' => true,
            ]
        ],
        'edit' => [
            'enable' => true
        ]
    ];
    
    /**
     * 组件事件
     * @var array 
     */
    public $pluginEvents = [
        'view' => [
            'addHoverDom' => '',
            'removeHoverDom' => '',
        ],
        'callback' => [
            'onClick' => '',
            'onExpand' => '',
            'onRename' => '',
            'beforeRemove' => '',
        ],
    ];
    
    /**
     * 构造函数
     * @param array $config
     */
    public function __construct($config = array()) {
        $config['pluginOptions'] = array_merge(array_filter($this->pluginOptions), isset($config['pluginOptions']) ? $config['pluginOptions'] : []);
        foreach ($this->pluginEvents as $key => &$events) {
            $events = array_merge(array_filter($this->pluginEvents[$key]), isset($config['pluginEvents'][$key]) ? $config['pluginEvents'][$key] : []);
        }
        $config['url'] = array_merge(array_filter($this->url), isset($config['url']) ? $config['url'] : []);
        // 如果没有设置value，则为空
        if(empty($config['value']) || !isset($config['value'])){
            $this->value = '';
        }
        
        parent::__construct($config);
    }
    
    /**
     * 初始化
     */
    public function init(){
        parent::init();

        // 如果没有设置id并且不是模型输出，则使用默认id
        if(!isset($config['id']) && !$this->hasModel()){
            $this->setId('zTreeDropDown_' . rand(100000, 999999));
        }else{
            // 设置id
            $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;
        }
        
        // 设置名称
        $this->name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        
        // 设置值
        $this->value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        
        $this->options['class'] = 'ztree';
        
        $this->options['placeholder'] = Yii::t('app', 'Select Placeholder');
        
        $this->pluginOptions['container'] = "zTreeDropDown_" . rand(100000, 999999);   
    }
    
    /**
     * @inheritdoc
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function run()
    {
        parent::run();
        $this->renderWidget();
    }
    
    /**
     * 初始化并渲染小部件
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function renderWidget(){
        
        echo $this->render('demo', [
            'plugin_container' => $this->pluginOptions['container'],
            'id' => $this->id,
            'name' => $this->name,
            'class' => $this->options['class'],
            'placeholder' => $this->options['placeholder'],
        ]);
        
        $this->registerAssets();
    }
    
    /**
     * 注册客户端资源
     */
    public function registerAssets() {
        $view = $this->getView();
        // 加载资源
        zTreeAsset::register($view);
        
        // 初始树状数据
        $treeDataList = Json::encode($this->data);
        
        // 配置常用事件
        foreach ($this->pluginEvents as &$events){
            switch ($this->pluginOptions['type']){
                case self::TYPE_EDITED:
                    $events['addHoverDom'] = new JsExpression('zTreeDropdown.addHoverDom');
                    $events['removeHoverDom'] = new JsExpression('zTreeDropdown.removeHoverDom');
                    $events['onClick'] = new JsExpression('zTreeDropdown.zTreeOnClick');
                    $events['onExpand'] = new JsExpression('zTreeDropdown.zTreeOnExpand');
                    $events['onRename'] = new JsExpression('zTreeDropdown.zTreeOnRename');
                    $events['beforeRemove'] = new JsExpression('zTreeDropdown.zTreeBeforeRemove');
                    break;
                case self::TYPE_SEARCH :
                    $events['onExpand'] = new JsExpression('zTreeDropdown.zTreeOnExpand');
                    break;
            }
        }
        
        // 配置
        $treeConfig = Json::encode(array_merge($this->pluginOptions, $this->pluginEvents));
        
        // url
        $url = Json::encode($this->url);
       
        $js = <<< JS
                
            var zTreeDropdown = new zTree.zTreeDropdown();
                
            zTreeDropdown.init({
                dropdown: "{$this->id}",
                value: "{$this->value}",
                placeholder: "{$this->options['placeholder']}",
                treeid: "{$this->pluginOptions['container']}",
                class: "{$this->options['class']}",
                config: $treeConfig,
                dataList: $treeDataList,
                url: $url,
            });
            
            // 单击显示下拉列表
            $("#{$this->id}-text").bind("click", function(){
                zTreeDropdown.showTree();
            });
JS;
        $view->registerJs($js, View::POS_READY);
    }
}
