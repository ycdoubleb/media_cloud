<?php

namespace common\models\order\searchs;

use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\OrderGoods;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * OrderGoodsSearch represents the model behind the search form of `common\models\order\OrderGoods`.
 */
class OrderGoodsSearch extends OrderGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'goods_id', 'num', 'is_del', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['order_sn'], 'safe'],
            [['price', 'amount'], 'number'],
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
        $query = OrderGoods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'goods_id' => $this->goods_id,
            'num' => $this->num,
            'price' => $this->price,
            'amount' => $this->amount,
            'is_del' => $this->is_del,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'order_sn', $this->order_sn]);

        return $dataProvider;
    }
    
    /**
     * 订单核查页
     * 
     * @param int $id   订单ID
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMedia($id, $params)
    {
        $query = (new Query())->select(['OrderGoods.id'])
                ->from(['OrderGoods' => OrderGoods::tableName()]);
        
        $query->addSelect(['Media.cover_url', 'Media.id AS media_id', 'Media.name AS media_name',
            'MediaType.name AS type_name', 'Media.price', 'Media.duration', 'Media.size',
            ]);
        
        //过滤条件
        $query->andFilterWhere(['OrderGoods.order_id' => $id]);
        
        // 查询媒体
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = OrderGoods.goods_id');
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
        
        return $dataProvider;
    }
}
