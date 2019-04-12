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
 * @property string $category_id 分库id
 * @property string $name   目录名称
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
            [['category_id', 'level', 'parent_id', 'sort_order', 'is_del', 'is_public', 'created_by', 'created_at', 'updated_at'], 'integer'],
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
            'category_id' => Yii::t('app', 'Category ID'),
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
        return $this->hasMany(Media::class, ['dir_id' => 'id'])
            ->where(['<=', 'del_status', Media::DEL_STATUS_APPLY]);
    }
    
    
    /**
     * 保存目录
     * @param string $name  名称
     * @param integer $category_id  分库id
     * @param integer $parent_id   上一级id
     * @return integer $id  目录id
     */
    public static function saveDir($name, $category_id, $parent_id = 0)
    {
        $dirModel = self::findOne([
            'name' => $name, 'category_id' => $category_id, 'parent_id' => $parent_id
        ]);
        
        if($dirModel == null ){
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {  
                $dirModel = new Dir([
                    'name' => trim($name), 'category_id' => $category_id,
                    'parent_id' => $parent_id, 'created_by' => \Yii::$app->user->id
                ]);
                //如果parent_id == 0，则level = 1，否则level就是父级的level + 1
                if($dirModel->parent_id == 0){
                    $dirModel->level = 1;
                }else{
                    $dirModel->level = self::getDirById($dirModel->parent_id)->level + 1;
                }
                //如果保存成功则更新路径和提交事务
                if($dirModel->save()){
                    $dirModel->updateParentPath();      //更新路径
                    $trans->commit();  //提交事务
                }
                self::invalidateCache();    //清除缓存    
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
            }
        }
        
        return $dirModel->id;
    }

    /**
     * 检查目录是否存在
     * @param string $dir_path    目录结构
     * @param string $category_id 分库id
     * @param string $dir_id      目录id
     * @return
     */
    public static function checkIsTheDirExists($dir_path, $category_id, $dir_id = 0)
    {
        if(is_string($dir_path) && !empty($dir_path)){
            //把全角"/" "\"替换为">"
            $dir_path = str_replace(["/", "\\"], ">", $dir_path);
            //目录结构
            $dir_path = explode('>', $dir_path);    
        }else{
            return;
        }
        //计算上传的目录个数
        $dirCount = count($dir_path);   
        //过滤数组中值两端的空格
        array_walk_recursive($dir_path, function(&$val){$val = trim($val);});   
        
        //查询已存在的目录
        $existDirs = [];   //已存在目录
        $dirQuery = self::find()->select(['id', 'path'])
            ->where(['category_id' => $category_id, 'name' => $dir_path]);
        $dirDataProvider = $dirQuery->all();
        //获取需要的已存在目录
        foreach ($dirDataProvider as $dir) {
            //获取已存在的目录的全路径
            $full_path = $dir->getFullPath();
            //上传目录的路径和存在的目录路径相同，则返回id
            if($dir_path == $full_path){
                $existDirPath = explode(',', $dir->path);
                foreach ($existDirPath as $id) {
                    if($id > 0) $existDirs[] = $id;
                }
            }
        }
        
        //计算已存在的目录个数
        $dirsCount = count($existDirs);    
        //如果已存在的目录大于0，则目录id为最后一个已存在的目录id， 否则新建目录
        if($dirsCount > 0){
            $dir_id = end($existDirs);
        }else{
            $dir_id = self::saveDir($dir_path[0], $category_id, $dir_id);
            for($i = 1; $i < $dirCount; $i++){
                if($dir_id == null) break;
                $dir_id = self::saveDir($dir_path[$i], $category_id, $dir_id);
            }
        }
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
     * @return Dir
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
     * @param integer $category_id  分库id
     * @param array $ids
     * @param integer $parent_id    父级id
     * @return array
     */
    public static function getDirListFramework($category_id, $ids = [], $parent_id = '0', $sort_order = 'is_public'){
        self::initCache();
        
        ArrayHelper::multisort(self::$dirs, $sort_order, SORT_DESC);
        
        //组装目录结构
        $listFramework = [];
        foreach(self::$dirs as $id => $_data){
            // 如果id存在则过滤
            if(in_array($_data['id'], $ids)) continue;
            if($_data['category_id'] != $category_id) continue;
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
                $item['children'] = self::getDirListFramework($category_id, $ids, $_data['id'], $sort_order);
                $listFramework[] = $item;
            }
        }

        return $listFramework;
    }
    
    /**
     * 获取目录的等级
     * @param string $created_by    用户ID，一般是当前用户ID
     * @param string $category_id   分库id
     * @param intger $level         默认返回所有目录
     * @param bool $key_to_value    返回键值对形式
     * @param bool $include_unshow  是否包括隐藏的分类
     * @param string $sort_order    排序
     * 
     * @return array(array|Array) 
     */
    public static function getDirsByLevel($created_by, $category_id, $level = 1, $key_to_value = false, $include_unshow = false, $sort_order = 'is_public') {
        self::initCache();
        $dirs = self::$dirs;   //所有目录
        
        ArrayHelper::multisort($dirs, $sort_order, SORT_DESC);
        
        $leveDirs = [];
        foreach ($dirs as $id => $dir) {
            if(!empty($created_by) && $dir['created_by'] != $created_by) continue;
            if(!empty($category_id) && $dir['category_id'] != $category_id) continue;
            if($dir['level'] == $level && ($include_unshow || $dir['is_del'] == 0)){
                $leveDirs[] = $dir;
            }
        }
        
        return $key_to_value ? ArrayHelper::map($leveDirs, 'id', 'name') : $leveDirs;
    }
    
    /**
     * 获取目录的子级
     * @param integer $id               目录ID
     * @param string $created_by        用户ID，一般是当前用户ID
     * @param string $category_id       分库id
     * @param bool $key_to_value        返回键值对形式
     * @param bool $recursion           是否递归
     * @param bool $include_unshow      是否包括隐藏的分类
     * @param string $sort_order        排序
     * 
     * @return array [array|key=value]
     */
    public static function getDirsChildren($id, $created_by, $category_id, $key_to_value = false, $recursion = false, $include_unshow = false, $sort_order = 'is_public') {
        self::initCache();
        $dirs = self::$dirs; //所有目录
        
        $childrens = [];
        ArrayHelper::multisort($dirs, $sort_order, SORT_DESC);
        foreach ($dirs as $dir) {
            if(!empty($created_by) && $dir['created_by'] != $created_by) continue;
            if(!empty($category_id) && $dir['category_id'] != $category_id) continue;
            if($dir['parent_id'] == $id && ($include_unshow || $dir['is_del'] == 0)){
                $childrens[] = $dir;
                if ($recursion) {
                    $childrens = array_merge($childrens, self::getDirsChildren($dir['id'], $created_by, $category_id, false, $recursion, $include_unshow, $sort_order));
                }
            }
        }

        return $key_to_value ? ArrayHelper::map($childrens, 'id', 'name') : $childrens;
    }
    
    /**
     * 获取目录的子级ID
     * @param integer $id               目录ID
     * @param string $created_by        用户ID，一般是当前用户ID
     * @param string $category_id       分库id
     * @param bool $recursion           是否递归
     * @param bool $include_unshow      是否包括隐藏的分类
     * 
     * @return array [id,id...]
     */
    public static function getDirChildrenIds($id, $created_by, $category_id, $recursion = false, $include_unshow = false) {
        self::initCache();
        
        $childrens = [];
        foreach (self::$dirs as $dir) {
            if(!empty($created_by) && $dir['created_by'] != $created_by) continue;
            if(!empty($category_id) && $dir['category_id'] != $category_id) continue;   
            if($dir['parent_id'] == $id && ($include_unshow || $dir['is_del'] == 0)){
                $childrens[] = $dir['id'];
                if ($recursion) {
                    $childrens = array_merge($childrens, self::getDirChildrenIds($dir['id'], $created_by, $category_id, $recursion, $include_unshow));
                }
            }
        }
       
        return $childrens;
    }
    
    /**
     * 返回当前（包括父级）存储目录同级的所有目录
     * @param integer $id               分类ID
     * @param string $created_by        用户ID，一般是当前用户ID
     * @param string $category_id       分库id
     * @param bool $containerSelfLevel  是否包括该分类同级分类
     * @param bool $key_to_value        返回键值对形式
     * @param bool $recursion           是否递归（向上级递归）
     * @param bool $include_unshow      是否包括隐藏的分类
     * @param string $sort_order        排序
     * 
     * @return array [[level_1],[level_2],..]
     */
    public static function getDirsBySameLevel($id, $created_by, $category_id, $containerSelfLevel = false, $key_to_value = false, $recursion = true, $include_unshow = false, $sort_order = 'is_public') 
    {
        $dir = self::getDirById($id);
        $dirs = [];
        if (($containerSelfLevel && $dir != null)) {
            //加上当前目录的子层级
            $childrens = self::getDirsChildren($id, $created_by, $category_id, $key_to_value, false, $include_unshow, $sort_order);
            if (count($childrens) > 0) {
                $dirs [] = $childrens;
            }
        }
        /* 递归获取所有层级 */
        do {
            if ($dir == null) {
                //当前分类为空时返回顶级分类
                $dirs [] = self::getDirsByLevel($created_by, $category_id, 1, $key_to_value);
                break;
            } else {
                array_unshift($dirs, self::getDirsChildren($dir->parent_id, $created_by, $category_id, $key_to_value, false, $include_unshow, $sort_order));
                if (!$recursion) 
                    break;
            }
            if ($dir->parent_id == 0) 
                break;
            
        }while (($dir = self::getDirById($dir->parent_id)) != null);
        
        return array_filter($dirs);
    }
}
