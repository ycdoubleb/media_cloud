<?php

namespace common\widgets\webuploader;

use Exception;
use yii\helpers\BaseHtml;

/**
 * 图片获取器
 * 功能：上传头像，或者多长图片
 *
 * @author Administrator
 */
class FilePicker extends WebuploaderInput{
    protected $extensions = '*';
    protected $mimeTypes = '*/*';
    
    public function __construct($config = array()) {
        $this->pluginOptions['fileNumLimit'] = 1;
        $this->pluginOptions['accept'] = [
            'title' => '选择文件',
            'extensions' => $this->extensions,
            'mimeTypes' => $this->mimeTypes,
        ];
        parent::__construct($config);
    }
    
    public function init(){
        $this->pluginOptions['type'] = self::TYPE_TILE;
        $this->pluginOptions['targetAttribute'] = 'url';
        if(isset($this->pluginOptions['mini']) && $this->pluginOptions['mini']){
            $this->pluginOptions['rootAddedClass'] = 'mini';
        }
        
        parent::init();
    }
    
    /**
     * 由url转换数据文件
     * @return string json
     */
    protected function convertDbfiles(){
        $value = $this->hasModel() ? BaseHtml::getAttributeValue($this->model, $this->attribute) : $this->value;
        if(empty($value)){
            return [];
        }else{
            $datas = [];
            $urls = [];
            
            if(is_string($value)){
                $urls = explode(',', $value);
            }else if(is_array($value)){
                $urls = $value;
            }else{
                throw new Exception('value 格式无法识别！');
            }
            foreach($urls as $url){
                $datas []= $this->createDbfile($url);
            }
            return $datas;
        }
    }
    
    /**
     * 通过 url 创建object
     * @param string $url
     */
    protected function createDbfile($url){
        return [
            'id' => "file-picker-". rand(10000, 99999),
            'thumb_url' => $url,
            'url' => $url,
            'ext' => pathinfo($url,PATHINFO_EXTENSION),
            'size' => 0,
            'name' => '',
        ];
    }
}
