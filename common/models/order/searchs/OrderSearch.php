<?php

namespace common\models\order\searchs;

use common\models\media\Acl;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Order;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * OrderSearch represents the model behind the search form of `common\models\order\Order`.
 */
class OrderSearch extends Order
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
            [['id', 'goods_num', 'order_status', 'play_status', 'play_at', 'confirm_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'order_name', 'user_note', 'play_code', 'play_sn', 'keyword'], 'safe'],
            [['goods_amount', 'order_amount'], 'number'],
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
     * 我的订单
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchOrder($params)
    {
        $keyword = ArrayHelper::getValue($params, 'OrderSearch.keyword');   // 关键字
        $order_status = ArrayHelper::getValue($params, 'order_status');     // 订单状态
        
        $query = (new Query())->from(['Order' => Order::tableName()]);

        //过滤条件
        $query->andFilterWhere([
            'Order.created_by' => Yii::$app->user->id
        ]);
        
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
        $query->andFilterWhere(['order_status' => $order_status,]);

        // 模糊查询
        $query->andFilterWhere(['or',
            ['like', 'Order.order_sn', $keyword],   //订单编号
            ['like', 'Order.order_name', $keyword], //订单名称
        ]);

        return $dataProvider;
    }
    

    /**
     * 我的资源
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchResources($params)
    {
        $keyword = ArrayHelper::getValue($params, 'OrderSearch.keyword');
        
        $query = (new Query())->select(['Order.id'])
                ->from(['Order' => Order::tableName()]);

        $query->addSelect(['Media.cover_url', 'Media.id AS media_id', 'Media.name AS media_name',
            'Order.order_sn', 'Order.order_name', 'MediaType.name AS type_name', 'Media.price', 
            'Media.duration', 'Media.size', 'Order.created_at', ]);
        // add conditions that should always apply here

        //过滤条件
        $query->andFilterWhere([
            'Order.order_status' => 11, 'Order.play_status' => 1, 
            'Order.created_by' => Yii::$app->user->id
        ]);
        // 查询媒体
        $query->leftJoin(['Acl' => Acl::tableName()], '(Acl.order_id = Order.id AND Acl.order_sn = Order.order_sn)');
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Acl.media_id');
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
            ['like', 'Media.id', $keyword], //媒体编号
            ['like', 'Media.name', $keyword],   //媒体名称
            ['like', 'Order.order_sn', $keyword],   //订单编号
            ['like', 'Order.order_name', $keyword], //订单名称
        ]);

        return $dataProvider;
    }
}
