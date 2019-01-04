<?php

namespace common\models\order\searchs;

use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Favorites;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * FavoritesSearch represents the model behind the search form of `common\models\order\Favorites`.
 */
class FavoritesSearch extends Favorites
{
    /**
     * 关键字
     * @var string 
     */
    public $keyword;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'is_del', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['group_name', 'keyword'], 'safe'],
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
        $keyword = ArrayHelper::getValue($params, 'FavoritesSearch.keyword');
        
        $query =  (new Query())->select(['Favorites.id'])
                ->from(['Favorites' => Favorites::tableName()]);

        $query->addSelect(['Media.cover_url', 'Media.id AS media_id', 'Media.name AS media_name',
            'MediaType.name AS type_name', 'Media.price', 'Media.duration', 'Media.size', 'Favorites.created_at']);
        // add conditions that should always apply here
         
        //过滤条件
        $query->andFilterWhere(['Favorites.is_del' => 0, 'Favorites.created_by' => \Yii::$app->user->id]);
        // 查询媒体
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Favorites.goods_id');
        // 查询媒体类型
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Media.id', $keyword],
            ['like', 'Media.name', $keyword]
        ]);

        return $dataProvider;
    }
}
