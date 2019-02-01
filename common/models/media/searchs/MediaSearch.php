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
        $page = ArrayHelper::getValue($params, 'page', 1);                              //分页
        $limit = ArrayHelper::getValue($params, 'limit', 10);                           //显示数
        
        // 查询媒体数据
        $query = self::find()->from(['Media' => self::tableName()]);

        $this->load($params);
        
        // 关联用户表         
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], '(AdminUser.id = Media.owner_id or AdminUser.id =Media.created_by)');
        // 复制查询
        $queryCopy = clone $query;
        
        $totalCount = $queryCopy->count('Media.id');
        
        // 关联媒体属性值关系表
        $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], '(AttrValueRef.media_id = Media.id and AttrValueRef.is_del = 0)');
        // 关联媒体标签关系表
        $query->leftJoin(['TagRef' => MediaTagRef::tableName()], '(TagRef.object_id = Media.id and TagRef.is_del = 0)');
        // 关联标签表
        $query->leftJoin(['Tags' => Tags::tableName()], 'Tags.id = TagRef.tag_id');
        
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
        
        // 属性值条件
        if(!empty($this->attribute_value_id)){
            foreach ($this->attribute_value_id as $value) {
                $query->andFilterWhere (['AttrValueRef.attribute_value_id' => $value]);
            }
        }
        // 模糊查询
        $query->andFilterWhere(['or', 
            ['like', 'Media.name', $this->keyword], 
            ['like', 'Tags.name', $this->keyword]
        ]);
        
        // 按媒体id分组
        $query->groupBy(['Media.id']);
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('dir', 'mediaType', 'mediaTagRefs', 'owner', 'createdBy');
        
        // 用户查询结果
        $userResults = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy('AdminUser.id')->all();
            
        // 媒体查询结果
        $mediaResults = $query->all();
        
        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'users' => $userResults,
                'medias' => $mediaResults
            ],
        ];
    }
    
    /**
     * 获取标签
     * @return string
     */
    public function getTags(){
        return implode(',', ArrayHelper::getColumn($this->mediaTagRefs, 'tags.name'));
    }
}
