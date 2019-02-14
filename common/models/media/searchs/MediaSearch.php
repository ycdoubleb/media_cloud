<?php

namespace common\models\media\searchs;

use common\models\AdminUser;
use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaTagRef;
use common\models\Tags;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * MediaSearch represents the model behind the search form of `common\models\media\Media`.
 */
class MediaSearch extends Media
{
    /**
     * 关键字
     * @var string 
     */
    public $keyword;
    
    /**
     * 属性值id
     * @var array 
     */
    public $attribute_value_id;
    
    /**
     * 用户昵称
     * @var string 
     */
    public $nickname;
    
    /**
     * 标签
     * @var string 
     */
//    public $tags;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 'status', 'mts_status', 'del_status', 'is_link', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'cover_url', 'url', 'keyword', 'attribute_value_id', 'nickname', 'ext', 'tags'], 'safe'],
            [['price', 'duration'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * 创建应用搜索查询的数据提供程序实例
     *
     * @param array $params
     *
     * @return array
     */
    public function search($params)
    {        
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);                             
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);     
        //属性值
        $attrValIds = array_filter($this->attribute_value_id ? $this->attribute_value_id : []);
        
        //所有用户
        $userResults = AdminUser::find()->select(['id', 'nickname'])->all();

        // 查询媒体数据
        $query = self::find()->from(['Media' => self::tableName()]);
        
        // 属性关联
        if(count($attrValIds) > 0){
            foreach ($attrValIds as $index => $id){
                $query->leftJoin(["AttValRef_$index" => MediaAttValueRef::tableName()], "(AttValRef_$index.media_id = Media.id and AttValRef_$index.is_del = 0)");
                $query->andFilterWhere(["AttValRef_$index.attribute_value_id" => $id]);
            }
        }
        
        // 目录过滤
        if(!empty($this->dir_id)){
            $dirChildrenIds = Dir::getDirChildrenIds($this->dir_id, Yii::$app->user->id, true);
            $query->andFilterWhere(['Media.dir_id' => ArrayHelper::merge($dirChildrenIds, [$this->dir_id])]);
        }
        
        // 必要条件
        $query->andFilterWhere([
            'Media.type_id' => $this->type_id,
            'Media.owner_id' => $this->owner_id,
            'Media.status' => $this->status,
            'Media.created_by' => $this->created_by,
            'Media.del_status' => 0,
        ]);
        
        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.name', $this->keyword],
            ['like', 'Media.tags', $this->keyword],
        ]);
        
        // 复制对象
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(Media.id) AS totalCount'])
            ->asArray()->one();
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 按媒体id分组
        $query->select(['Media.*'])->groupBy('Media.id');
        
        // 过滤重复
        $query->with('dir', 'mediaType', 'mediaTagRefs', 'owner', 'createdBy');
        
        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'users' => $userResults,
                'medias' => $query->all()
            ],
        ];
    }
}
