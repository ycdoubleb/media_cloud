<?php

namespace backend\modules\operation_admin\searchs;

use common\models\AdminUser;
use common\models\order\Order;
use common\models\order\PlayApprove;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * PlayApproveSearch represents the model behind the search form of `common\models\order\PlayApprove`.
 */
class PlayApproveSearch extends PlayApprove
{
    /**
     * 订单名
     * @var string
     */
    public $order_name;
    
    /**
     * 订单号
     * @var string
     */
    public $order_sn;
    
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
            [['id', 'order_id', 'status', 'result', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['certificate_url', 'content', 'feedback', 'order_name', 'order_sn', 'nickname'], 'safe'],
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
        $page = ArrayHelper::getValue($params, 'page', 1);                              //分页
        $limit = ArrayHelper::getValue($params, 'limit', 10);                           //显示数
        
        $query = self::find()->from(['Approve' => PlayApprove::tableName()]);
        
        $this->load($params);

        // 关联用户表         
        $query->leftJoin(['User' => User::tableName()], '(User.id =Approve.created_by)');
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], '(AdminUser.id = Approve.handled_by)');
        // 复制查询
        $queryCopy = clone $query;
        
        // 关联订单表
        $query->leftJoin(['Order' => Order::tableName()], 'Order.id = Approve.order_id');

        // 必要条件
        $query->andFilterWhere([
            'Order.order_sn' => $this->order_sn,
            'Approve.status' => $this->status,
            'Approve.handled_by' => $this->handled_by,
            'Approve.created_by' => $this->created_by,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'Order.order_name', $this->order_name]);
        
        // 按审批id分组
        $query->groupBy(['Approve.id']);
        
        // 计算总数
        $totalCount = $query->count('*');
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('order', 'handledBy', 'createdBy');

        // 用户查询结果
        $createdByResults = $queryCopy->select(['User.id', 'User.nickname'])
            ->groupBy(['User.id'])->all();
        $handledByResults = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy(['AdminUser.id'])->asArray()->all();
        
        // 审批查询结果
        $approveResults = $query->all();

        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'createdBys' => $createdByResults,
                'handledBys' => $handledByResults,
                'approves' => $approveResults
            ],
        ];
    }
}
