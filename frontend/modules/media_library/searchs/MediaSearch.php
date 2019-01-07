<?php

namespace frontend\modules\media_library\searchs;

use common\models\media\Media;
use common\models\media\MediaType;
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
     * 关键字
     * @var string 
     */
    public $keyword;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 
                'status', 'mts_status', 'del_status', 'is_link', 'created_by',
                    'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'cover_url', 'url', 'keyword'], 'safe'],
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
        $page = ArrayHelper::getValue($params, 'page', 1); //分页
        $limit = ArrayHelper::getValue($params, 'limit', 20); //显示数
        
        self::getInstance();
        $this->load($params);
        
        self::$query->addSelect(['cover_url', 'Media.name', 'dir_id', 'MediaType.name AS type_name', 
                    'price', 'duration', 'size',
//            'tags'
            ]);
            
        // 媒体类型
        self::$query->andFilterWhere(['type_id' => $this->type_id,]);
        
        // 模糊查询
        self::$query->andFilterWhere(['like', 'Media.name', $this->name]);
        
        // 关联查询媒体类型
        self::$query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        
        //复制媒体对象
//        $copyMedia= clone self::$query;    
//        //查询媒体下的标签
//        $tagRefQuery = MediaTagRef::getTagsByObjectId($copyMedia, 1, false);
//        $tagRefQuery->addSelect(["GROUP_CONCAT(Tags.`name` ORDER BY TagRef.id ASC SEPARATOR ',') AS tags"]);
        //以媒体id为分组
        self::$query->groupBy(['Media.id']);
        //查询总数
        $totalCount = self::$query->count('id');
        //显示数量
        self::$query->offset(($page - 1) * $limit)->limit($limit);
        
        //查询标签结果
//        $tagRefResult = $tagRefQuery->asArray()->all(); 
        //查询课程结果
        $meidaResult = self::$query->asArray()->all();
        //以media_id为索引
        $medias = ArrayHelper::index($meidaResult, 'id');
//        $results = ArrayHelper::index($tagRefResult, 'object_id');

        //合并查询后的结果
//        foreach ($medias as $id => $item) {
//            if(isset($results[$id])){
//                $medias[$id] += $results[$id];
//            }
//        }

        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'media' => $medias
            ],
        ];
    }
    
    /**
     * 
     * @return Query
     */
    protected static function getInstance() {
        if (self::$query == null) {
            self::$query = self::findMedia();
        }
        return self::$query;
    }
    
    /**
     * 查询媒体
     * @return Query
     */
    public static function findMedia() 
    {
        $query = self::find()->select(['Media.id'])
            ->from(['Media' => self::tableName()]);
        
        return $query;
    }
}