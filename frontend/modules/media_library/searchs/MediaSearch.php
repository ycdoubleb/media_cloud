<?php

namespace frontend\modules\media_library\searchs;

use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaType;
use Yii;
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
     * 查询素材数据
     * @param array $params     参数
     * @param boolean $isTrue   是否是查询总数
     * @return ActiveDataProvider
     */
    public function searchMediaData($params, $isTrue)
    {
        $this->load($params);
        $page = ArrayHelper::getValue($params, 'page', 1);      //分页
        $limit = ArrayHelper::getValue($params, 'limit', 20);   //显示数
        //分库id
        $this->category_id = ArrayHelper::getValue($params, 'category_id', 1);
        
        //需要查找的属性
        $attrValIds = array_filter($this->attribute_value_id ? $this->attribute_value_id : []);  
        
        // 查询素材数据
        $query = self::find()->from(['Media' => Media::tableName()]);
        
        // 主要过滤条件
        $query->andFilterWhere([
            'Media.category_id' => $this->category_id,
            'Media.del_status' => 0,
            'Media.status' => Media::STATUS_PUBLISHED,  // 发布状态
            'Media.type_id' => $this->type_id
        ]); 
        // 目录过滤
        if(!empty($this->dir_id)){
            $dirChildrenIds = Dir::getDirChildrenIds($this->dir_id, null, $this->category_id, true);
            $query->andFilterWhere(['Media.dir_id' => ArrayHelper::merge($dirChildrenIds, [$this->dir_id])]);
        }
        // 属性值过滤条件
        if(count($attrValIds) > 0){
            foreach ($attrValIds as $index => $id){
                // 关联素材属性值关系表
                $query->leftJoin(["AttValRef_$index" => MediaAttValueRef::tableName()], "(AttValRef_$index.media_id = Media.id and AttValRef_$index.is_del = 0)");
                $query->andFilterWhere(["AttValRef_$index.attribute_value_id" => $id]);
            }
        }
        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.name', $this->keyword],
            ['like', 'Media.tags', $this->keyword],
        ]);
        
        $totalCount = 0; $mediaResult = [];
        if($isTrue){
            //查询总数
            $totalCount = $query->addSelect(['COUNT(Media.id) AS totalCount'])->asArray()->one();
        } else {
            $query->addSelect(['Media.id', 'cover_url', 'Media.name', 'dir_id', 'MediaType.name AS type_name', 
                'MediaType.sign AS type_sign', 'price', 'duration', 'size', 'Media.tags AS tag_name'
            ]);
            // 关联查询素材类型
            $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
            
            // 显示数量
            $query->offset(($page - 1) * $limit)->limit($limit);
            // 查询素材结果
            $mediaResult = $query->asArray()->all();
        }
        
        return [
            'filter' => $params,
            'total' => $totalCount['totalCount'],
            'data' => [
                'media' => $mediaResult
            ],
        ];
    }
    
}