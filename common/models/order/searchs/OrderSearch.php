<?php

namespace common\models\order\searchs;

use common\models\media\Acl;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Order;
use common\models\order\OrderGoods;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
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
            'key' => 'id',
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

        // 关键字查询
        $query->andFilterWhere(['or',
            ['Order.order_sn' => $keyword],         //订单编号
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
        $query = self::find()->select(['OrderGoods.goods_id'])
                ->from(['Order' => Order::tableName()]);
        // 关联查询媒体
        $query->leftJoin(['OrderGoods' => OrderGoods::tableName()], '(OrderGoods.order_id = Order.id AND OrderGoods.order_sn = Order.order_sn)');

        // 复制媒体对象
        $copyMedia= clone $query;
        
        $query->addSelect(['Media.cover_url', 'Media.id AS media_id', 'Media.name AS media_name',
            'Order.order_sn', 'Order.order_name', 'MediaType.name AS type_name', 'Media.price', 
            'Media.duration', 'Media.size', 'Order.created_at', ]);
        // add conditions that should always apply here

        // 必要过滤条件
        $query->andFilterWhere([
            'Order.order_status' => Order::ORDER_STATUS_TO_BE_CONFIRMED,
            'Order.order_status' => Order::ORDER_STATUS_CONFIRMED, 'Order.play_status' => 1, 
            'Order.created_by' => Yii::$app->user->id
        ]);
        // 关联查询媒体
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = OrderGoods.goods_id');
        // 关联查询媒体类型
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
        

        $this->load($params);

        // 关键字查询
        $query->andFilterWhere(['or',
            ['Media.id' => $this->keyword],           //媒体编号
            ['like', 'Media.name', $this->keyword],   //媒体名称
            ['Order.order_sn' => $this->keyword],         //订单编号
            ['like', 'Order.order_name', $this->keyword], //订单名称
        ]);

        // 查找Acl列表
        $aclResults = $this->findAclByMediaId($copyMedia);
        // 媒体数据
        $mediaResults = $query->asArray()->all();
        
        //合并查询后的结果
        foreach ($mediaResults as &$item) {
            $item['acl'] = [];
            foreach ($aclResults as $value) {
                if($item['goods_id'] == $value['media_id']){
                    $item['acl'] += [
                        $value['level'] => $value['id']
                    ] ;
                }
            }
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($mediaResults),
        ]);
        
        return $dataProvider;
    }
    
    /**
     * 根据媒体ID查找数据
     * @param array $id
     * @return array
     */
    protected function findAclByMediaId($id)
    {
        $videoUrl = Acl::find()
                ->select(['id', 'media_id', 'level'])
                ->where(['media_id' => $id])
                ->asArray()->all();

        return $videoUrl;
    }
}
