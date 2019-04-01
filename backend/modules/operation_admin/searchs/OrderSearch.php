<?php

namespace backend\modules\operation_admin\searchs;

use common\models\media\Media;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * OrderSearch represents the model behind the search form of `common\models\order\Order`.
 */
class OrderSearch extends Order
{
    /**
     * 用户昵称
     * @var string 
     */
    public $nickname;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goods_num', 'order_status', 'play_status', 'play_at', 'confirm_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'order_name', 'user_note', 'play_code', 'play_sn', 'nickname'], 'safe'],
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
     * 创建应用搜索查询的数据提供程序实例
     *
     * @param array $params
     *
     * @return array
     */
    public function search($params)
    {
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);   
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);   
        // 分库id
        $category_id = ArrayHelper::getValue($params, 'category_id');   
        
        //查询订单
        $query = self::find()->from(['Order' => self::tableName()]);

        // 关联用户表
        $query->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by');
        // 关联商品表
        $query->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.order_id = Order.id');
        // 关联素材表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = OrderGoods.goods_id');
       
        // 条件查询
        $query->andFilterWhere([
            'order_sn' => $this->order_sn,
            'order_status' => $this->order_status,
            'created_by' => $this->created_by,
            'category_id' => $category_id,
        ]);

        // 过滤已作废的订单
        $query->andFilterWhere(['!=', 'order_status', self::ORDER_STATUS_INVALID]);
        
        // 模糊查询
        $query->andFilterWhere(['like', 'order_name', $this->order_name]);
        
        // 复制对象
        $queryCopy = clone $query;
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 关联
        $query->with('createdBy');
        
        // 查询结果
        $orderResults = $query->all();
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(Order.id) AS totalCount'])
            ->asArray()->one();
        // 用户查询结果
        $userResults = $queryCopy->select(['User.id', 'User.nickname'])
            ->groupBy('User.id')->all();
        
        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'users' => $userResults,
                'orders' => $orderResults,
            ]
        ];
    }
}
