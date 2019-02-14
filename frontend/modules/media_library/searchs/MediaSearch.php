<?php

namespace frontend\modules\media_library\searchs;

use common\models\media\Media;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaType;
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
            [['id', 'category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 'status', 'mts_status',
                'del_status', 'is_link', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
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
     * 查询媒体总数
     * @param array $params
     * @return array
     */
    public function search($params)
    {
        $this->load($params);
        $att_value_ids = array_filter($this->attribute_value_id ? $this->attribute_value_id : []);  //需要查找的属性

        $query = self::find()->select(['Media.id','Media.name'])->from(['Media' => Media::tableName()]);
        
        // 主要过滤条件
        $query->andFilterWhere([
            'Media.del_status' => 0,
//            'Media.status' => Media::STATUS_PUBLISHED,  // 发布状态
            'Media.type_id' => $this->type_id
        ]);        
        // 属性值过滤条件
        if (count($att_value_ids) > 0) {
            // 关联媒体属性值关系表
            $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], '(AttrValueRef.media_id = Media.id AND AttrValueRef.is_del = 0)');
            $query->andFilterWhere (['AttrValueRef.attribute_value_id' => $att_value_ids]);
        }        
        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.name', $this->keyword],
            ['like', 'Media.tags', $this->keyword],
        ]);
        
        //以媒体id为分组
        $query->groupBy(['Media.id']);
        //查询总数
        $totalCount = $query->count();
        
        return [
            'filter' => $params,
            'total' => $totalCount,
        ];
    }
    
    /**
     * 查询媒体数据（ajax请求时调用）
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchMediaData($params)
    {
        $this->load($params);
        $page = ArrayHelper::getValue($params, 'page', 1);      //分页
        $limit = ArrayHelper::getValue($params, 'limit', 20);   //显示数
        $att_value_ids = array_filter($this->attribute_value_id ? $this->attribute_value_id : []);  //需要查找的属性
        
        // 查询媒体数据
        $query = self::find()->select(['Media.id', 'Media.id','cover_url', 'Media.name', 'dir_id', 'MediaType.name AS type_name', 
                'MediaType.sign AS type_sign', 'price', 'duration', 'size', 'Media.tags AS tag_name'
            ])->from(['Media' => Media::tableName()]);
        
        // 主要过滤条件
        $query->andFilterWhere([
            'Media.del_status' => 0,
//            'Media.status' => Media::STATUS_PUBLISHED,  // 发布状态
            'Media.type_id' => $this->type_id
        ]);        
        // 属性值过滤条件
        if (count($att_value_ids) > 0) {
            // 关联媒体属性值关系表
            $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], '(AttrValueRef.media_id = Media.id AND AttrValueRef.is_del = 0)');
            $query->andFilterWhere (['AttrValueRef.attribute_value_id' => $att_value_ids]);
        }
        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.name', $this->keyword],
            ['like', 'Media.tags', $this->keyword],
        ]);
        
        // 关联查询媒体类型
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        //以媒体id为分组
        $query->groupBy(['Media.id']);
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        // 查询媒体结果
        $mediaResult = $query->asArray()->all();
       
        return [
            'filter' => $params,
            'data' => [
                'media' => $mediaResult
            ],
        ];
    }
    
}