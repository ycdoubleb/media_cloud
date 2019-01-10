<?php

namespace backend\modules\operation_admin\searchs;

use common\models\order\Order;
use common\models\User;
use yii\base\Model;

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
        $query = self::find()->from(['Order' => self::tableName()]);

        $this->load($params);

        // 关联用户表
        $query->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by');
        // 复制对象
        $queryCopy = clone $query;

        // 条件查询
        $query->andFilterWhere([
            'order_sn' => $this->order_sn,
            'order_status' => $this->order_status,
            'created_by' => $this->created_by,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'order_name', $this->order_name]);
        
        // 按订单id分组
        $query->groupBy(['Order.id']);
        
        // 关联
        $query->with('createdBy');
        
        // 查询结果
        $orderResults = $query->all();
        // 用户查询结果
        $userResults = $queryCopy->select(['User.id', 'User.nickname'])
            ->groupBy('User.id')->all();
        
        return [
            'filter' => $params,
            'data' => [
                'users' => $userResults,
                'orders' => $orderResults,
            ]
        ];
    }
}
