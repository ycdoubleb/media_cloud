<?php

namespace frontend\modules\media_library\searchs;

use common\models\media\Media;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaTagRef;
use common\models\media\MediaType;
use common\models\Tags;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MediaSearch represents the model behind the search form of `common\models\media\Mediat`.
 */
class MediaSearch extends Media 
{
    /**
     * 属性值id
     * @var array 
     */
    public $attribute_value_id;
    /**
     * 关键字
     * @var string 
     */
    public $keyword;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 'status', 'mts_status', 'del_status', 'is_link', 'created_by',
                    'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'cover_url', 'url', 'attribute_value_id', 'keyword'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $page = ArrayHelper::getValue($params, 'page', 1);      //分页
        $limit = ArrayHelper::getValue($params, 'limit', 20);   //显示数
        
        $query = self::find()->select(['Media.id'])->from(['Media' => Media::tableName()]);
        $this->load($params);
        
        // 主要过滤条件
        $query->andFilterWhere([
            'Media.del_status' => 0,
            'Media.status' => Media::STATUS_PUBLISHED,  // 发布状态
            'Media.type_id' => $this->type_id
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
            ['like', 'Tags.name', $this->keyword],
        ]);
        
        // 关联查询媒体类型
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        // 关联媒体属性值关系表
        $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], '(AttrValueRef.media_id = Media.id and AttrValueRef.is_del = 0)');
        // 关联媒体标签关系表
        $query->leftJoin(['TagRef' => MediaTagRef::tableName()], '(TagRef.object_id = Media.id and TagRef.is_del = 0)');
        // 关联标签表
        $query->leftJoin(['Tags' => Tags::tableName()], 'Tags.id = TagRef.tag_id');
        
        // 复制媒体对象
        $copyMedia= clone $query;    
        // 查询媒体下的标签
        $tagRefQuery = MediaTagRef::getTagsByObjectId($copyMedia, false);
        $tagRefQuery->addSelect(["GROUP_CONCAT(Tags.`name` ORDER BY TagRef.id ASC SEPARATOR ',') AS tag_name"]);
        // 查询媒体数据
        $query->addSelect(['Media.id','cover_url', 'Media.name', 'dir_id', 'MediaType.name AS type_name', 
                'MediaType.sign AS type_sign', 'price', 'duration', 'size', 
        ]);
        
        //以媒体id为分组
        $query->groupBy(['Media.id']);
        //查询总数
        $totalCount = $query->count('id');
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        //查询课程结果
        $meidaResult = $query->asArray()->all();
        //查询标签结果
        $tagRefResult = $tagRefQuery->asArray()->all(); 
        //以media_id为索引
        $medias = ArrayHelper::index($meidaResult, 'id');
        $results = ArrayHelper::index($tagRefResult, 'object_id');

        //合并查询后的结果
        foreach ($medias as $id => $item) {
            if(isset($results[$id])){
                $medias[$id] += $results[$id];
            }
        }
        
        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'media' => $medias
            ],
        ];
    }
    
}