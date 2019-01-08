<?php

namespace frontend\modules\media_library\searchs;

use common\models\media\Media;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaTagRef;
use common\models\media\MediaType;
use common\models\Tags;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * MediaSearch represents the model behind the search form of `common\models\media\Mediat`.
 */
class MediaSearch extends Media 
{
    /**
     * 声明静态属性
     * @var Query 
     */
    private static $query;
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
        $keyword = ArrayHelper::getValue($params, 'MediaSearch.keyword');   //关键字
        
        // 查询媒体数据
        $query = self::find()->from(['Media' => self::tableName()]);
        $this->load($params);
        
        // 关联查询媒体类型
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        // 关联媒体属性值关系表
        $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], '(AttrValueRef.media_id = Media.id and AttrValueRef.is_del = 0)');
        // 关联媒体标签关系表
        $query->leftJoin(['TagRef' => MediaTagRef::tableName()], '(TagRef.object_id = Media.id and TagRef.is_del = 0)');
        // 关联标签表
        $query->leftJoin(['Tags' => Tags::tableName()], 'Tags.id = TagRef.tag_id');
        
        $query->addSelect(['Media.id','cover_url', 'Media.name', 'dir_id', 'MediaType.name AS type_name', 
                'MediaType.sign AS type_sign', 'price', 'duration', 'size', 
            "GROUP_CONCAT(Tags.`name` ORDER BY TagRef.id ASC SEPARATOR ',') AS tag_name"
        ]);
            
        // 媒体类型过滤
        $query->andFilterWhere(['Media.type_id' => $this->type_id]);
        // 属性值条件
        if(!empty($this->attribute_value_id)){
            foreach ($this->attribute_value_id as $value) {
                $query->andFilterWhere (['AttrValueRef.attribute_value_id' => $value]);
            }
        }
        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.name', $keyword],
            ['like', 'Tags.name', $keyword],
        ]);
        
        //以媒体id为分组
        $query->groupBy(['Media.id']);
        // 过滤重复
//        $query->with('dir', 'mediaType', 'mediaTagRefs');
        //查询总数
        $totalCount = $query->count('id');
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        //查询课程结果
        $meidaResult = $query->asArray()->all();
 

        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'media' => $meidaResult
            ],
        ];
    }
}