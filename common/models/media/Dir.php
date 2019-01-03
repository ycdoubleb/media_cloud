<?php

namespace common\models\media;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\Cache;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "{{%dir}}".
 *
 * @property string $id
 * @property string $name   分类名称
 * @property int $level     等级：0顶级 1~3
 * @property string $path   继承路径，多个逗号分隔
 * @property string $parent_id 父级id
 * @property int $sort_order    排序
 * @property string $image  图标路径
 * @property int $is_del    是否显示
 * @property int $is_public 是否公共目录： 1是，0否
 * @property string $des    描述
 * @property string $created_by 创建者ID，关联admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property Media[] $medias    获取所有的媒体素材
 */
class Dir extends ActiveRecord
{
    /* @var $cache Cache */
    private static $cache;

    /**
     * @see cache
     */
    private static $cacheKey = 'mc_dir';

    /**
     * 目录[id,name,level,path,parent_id,sort_order,image,is_show,created_by]
     * @var array
     */
    private static $dirs;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dir}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'parent_id', 'sort_order', 'is_del', 'is_public', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['path', 'image', 'des'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'level' => Yii::t('app', 'Level'),
            'path' => Yii::t('app', 'Path'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'image' => Yii::t('app', 'Image'),
            'is_del' => Yii::t('app', 'Is Del'),
            'is_public' => Yii::t('app', 'Is Public'),
            'des' => Yii::t('app', 'Des'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMedias()
    {
        return $this->hasMany(Media::class, ['dir_id' => 'id']);
    }
    
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
//            $file_name = md5(time());
//            //图片上传
//            $upload = UploadedFile::getInstance($this, 'image');
//            if ($upload !== null) {
//                $string = $upload->name;
//                $array = explode('.', $string);
//                //获取后缀名，默认名为.jpg
//                $ext = count($array) == 0 ? 'jpg' : $array[count($array) - 1];
//                $uploadpath = $this->fileExists(Yii::getAlias('@frontend/web/upload/course/category/'));
//                $upload->saveAs($uploadpath . $file_name . '.' . $ext);
//                $this->image = '/upload/course/category/' . $file_name . '.' . $ext . '?r=' . rand(1, 10000);
//            }
//            if (trim($this->image) == '') {
//                $this->image = $this->getOldAttribute('image');
//            }
            //设置等级
            if (empty($this->parent_id)) {
                $this->parent_id = 0;
            }
            
            $this->level = $this->parent_id == 0 ? 1 : self::getDirById($this->parent_id)->level + 1;
            return true;
        }
        return false;
    }
    
    /**
     * 检查目标路径是否存在，不存即创建目标
     * @param string $uploadpath    目录路径
     * @return string
     */
    private function fileExists($uploadpath) {

        if (!file_exists($uploadpath)) {
            mkdir($uploadpath);
        }
        return $uploadpath;
    }
    
    //==========================================================================
    //
    // Cache
    //
    //==========================================================================

    /* 初始缓存 */
    private static function initCache() {
        if (self::$cache == null) {
            self::$cache = Instance::ensure([
                        'class' => 'yii\caching\FileCache',
                        'cachePath' => Yii::getAlias('@backend') . '/runtime/cache'
                            ], Cache::class);
        }
        self::loadFromCache();
    }

    /**
     * 取消缓存
     */
    public static function invalidateCache() {
        self::initCache();
        if (self::$cache !== null) {
            self::$cache->delete(self::$cacheKey);
            self::$dirs = null;
        }
    }

    /**
     * 从缓存中获取数据
     */
    private static function loadFromCache() {
        if (self::$dirs !== null || !self::$cache instanceof Cache) {
            return;
        }
        $data = self::$cache->get(self::$cacheKey);
        if (is_array($data) && isset($data[0])) {
            //从缓存取出分类数据
            self::$dirs = ArrayHelper::index($data[0], 'id');
            return;
        }
        $dirDatas = self::find()->asArray()->all();
        //没有缓存则从数据库获取数据
        self::$dirs = ArrayHelper::index($dirDatas, 'id');
        
        self::$cache->set(self::$cacheKey, [$dirDatas]);
    }
    
    /**
     * 获取目录
     * @param integer $id
     * @return Dir
     */
    public static function getDirById($id) {
        self::initCache();
        if (isset(self::$dirs[$id])) {
            return new Dir(self::$dirs[$id]);
        }
        
        return null;
    }

    /**
     * 获取所有目录数据
     * @return array
     */
    public static function getDirs() {
        self::initCache();
        return self::$dirs;
    }
            
    /**
     * 更新父级继承路径
     */
    public function updateParentPath() {
        //设置继承路径
        $parent = self::getDirById($this->parent_id);
        $this->path = ($this->level == 1 ? "0" : "$parent->path") . ",$this->id";
        $this->update(false, ['path']);
    }

    /**
     * 父级
     * @return UserCategory
     */
    public function getParent() {
        self::initCache();
        return self::getDirById($this->parent_id);
    }

    /**
     * 获取所有父级
     * @param array $fields         只返回指定字段
     * @param bool $contain_root    是否包括根目录
     * @return type
     */
    public function getParents($fields = [], $contain_root = false) {
        self::initCache();
        $paths = explode(',', $this->path);
        if(!$contain_root){
            $paths = array_filter($paths);
        }
        $parentids = array_values($paths);
        $parents = [];
        foreach ($parentids as $index => $id) {
            /* @var $dir Dir */
            $dir = self::getDirById($id);
            $parents [] = count($fields) == 0 ? $dir : $dir->toArray($fields);
        }

        return $parents;
    }

    /**
     * 获取全路径
     */
    public function getFullPath() {
        self::initCache();
        $parentids = array_values(array_filter(explode(',', $this->path)));
        $path = '';
        foreach ($parentids as $index => $id) {
            $path .= ($index == 0 ? '' : ' > ') . self::getDirById($id)->name;
        }
        return $path;
    }
    
    //==========================================================================
    //
    // public method
    //
    //==========================================================================
    /**
     * 递归生成目录列表框架结构
     * @param array $ids
     * @param integer $parent_id    父级id
     * @return array
     */
    public static function getDirListFramework($ids = [], $parent_id = 0){
        self::initCache();
        
        ArrayHelper::multisort(self::$dirs, 'is_public', SORT_DESC);
        //组装目录结构
        $listFramework = [];
        foreach(self::$dirs as $id => $_data){
            // 如果id存在则过滤
            if(in_array($_data['id'], $ids)) continue;
            if($_data['parent_id'] == $parent_id){
                $item = [
                    'title'=> $_data['name'],
                    'key' => $_data['id'],
                    'level' => $_data['level'],
                    'is_del' => $_data['is_del'],
                    'is_public' => $_data['is_public'],
                    'sort_order' => $_data['sort_order'],
                    'folder' => true,
                ];
                $item['children'] = self::getDirListFramework($ids, $_data['id']);
                $listFramework[] = $item;
            }
        }
        
        return $listFramework;
    }
    
    /**
     * 获取目录的等级
     * @param intger $level         默认返回所有目录
     * @param string $created_by    用户ID
     * @param bool $key_to_value    返回键值对形式
     * @param bool $include_unshow  是否包括隐藏的分类
     * @param string $sort_order    排序
     * 
     * @return array(array|Array) 
     */
    public static function getDirsByLevel($level = 1, $created_by = null, $key_to_value = false, $include_unshow = false, $sort_order = 'is_public') {
        self::initCache();
        $dirs = self::$dirs;   //所有目录
        //不传created_by，默认使用当前用户的ID
        if (!isset($created_by) || empty($created_by)) {
            $created_by = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        }
       
        $leveDirs = [];
        ArrayHelper::multisort($dirs, $sort_order, SORT_DESC);
        foreach ($dirs as $id => $dir) {
            if($dir['level'] == $level && ($include_unshow || $dir['is_del'] == 1)){
                $leveDirs[] = $dir;
            }
        }
        
        return $key_to_value ? ArrayHelper::map($leveDirs, 'id', 'name') : $leveDirs;
    }
    
    /**
     * 获取目录的子级
     * @param integer $id               目录ID
     * @param string $created_by        用户ID
     * @param bool $key_to_value        返回键值对形式
     * @param bool $recursion           是否递归
     * @param bool $include_unshow      是否包括隐藏的分类
     * @param string $sort_order        排序
     * 
     * @return array [array|key=value]
     */
    public static function getDirsChildren($id, $created_by = null, $key_to_value = false, $recursion = false, $include_unshow = false, $sort_order = 'is_public') {
        self::initCache();
        $dirs = self::$dirs; //所有目录
        //不传created_by，默认使用当前用户的ID
        if (!isset($created_by) || empty($created_by)) {
            $created_by = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        }
       
        $childrens = [];
        ArrayHelper::multisort($dirs, $sort_order, SORT_DESC);
        foreach ($dirs as $dir) {
            if($dir['parent_id'] == $id && ($include_unshow || $dir['is_del'] == 1)){
                $childrens[] = $dir;
                if ($recursion) {
                    $childrens = array_merge($childrens, self::getDirsChildren($dir['id'], $created_by, false, $recursion, $include_unshow, $sort_order));
                }
            }
        }

        return $key_to_value ? ArrayHelper::map($childrens, 'id', 'name') : $childrens;
    }
    
    /**
     * 获取目录的子级ID
     * @param integer $id               目录ID
     * @param string $created_by        用户ID
     * @param bool $recursion           是否递归
     * @param bool $include_unshow      是否包括隐藏的分类
     * 
     * @return array [id,id...]
     */
    public static function getDirChildrenIds($id, $created_by = null, $recursion = false, $include_unshow = false) {
        self::initCache();
        //不传created_by，默认使用当前用户ID
        if (!isset($created_by) || empty($created_by)) {
            $created_by = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        }
        
        $childrens = [];
        foreach (self::$dirs as $dir) {
            if($dir['parent_id'] == $id && ($include_unshow || $dir['is_del'] == 1)){
                $childrens[] = $dir['id'];
                if ($recursion) {
                    $childrens = array_merge($childrens, self::getDirChildrenIds($dir['id'], $created_by, $recursion, $include_unshow));
                }
            }
        }
       
        return $childrens;
    }
    
    /**
     * 返回当前（包括父级）存储目录同级的所有目录
     * @param integer $id               分类ID
     * @param string $created_by        用户ID
     * @param bool $containerSelfLevel  是否包括该分类同级分类
     * @param bool $key_to_value        返回键值对形式
     * @param bool $recursion           是否递归（向上级递归）
     * @param bool $include_unshow      是否包括隐藏的分类
     * @param string $sort_order        排序
     * 
     * @return array [[level_1],[level_2],..]
     */
    public static function getDirsBySameLevel($id, $created_by = null, $containerSelfLevel = false, $key_to_value = false, $recursion = true, $include_unshow = false, $sort_order = 'is_public') {
        //不created_by，默认使用当前用户的ID
        if (!isset($created_by) || empty($created_by)) {
            $created_by = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        }
        
        $dir = self::getDirById($id);
        $dirs = [];
        if (($containerSelfLevel && $dir != null)) {
            //加上当前目录的子层级
            $childrens = self::getDirsChildren($id, $created_by, $key_to_value, false, $include_unshow, $sort_order);
            if (count($childrens) > 0) {
                $dirs [] = $childrens;
            }
        }
        /* 递归获取所有层级 */
        do {
            if ($dir == null) {
                //当前分类为空时返回顶级分类
                $dirs [] = self::getDirsByLevel(1, $created_by, $key_to_value);
                break;
            } else {
                array_unshift($dirs, self::getDirsChildren($dir->parent_id, $created_by, $key_to_value, false, $include_unshow, $sort_order));
                if (!$recursion) 
                    break;
            }
            if ($dir->parent_id == 0) 
                break;
            
        }while (($dir = self::getDirById($dir->parent_id)) != null);
        
        return array_filter($dirs);
    }
}
