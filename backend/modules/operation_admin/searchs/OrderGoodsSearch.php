<?php

namespace backend\modules\operation_admin\searchs;

use common\models\AdminUser;
use common\models\media\Media;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * OrderGoodsSearch represents the model behind the search form of `common\models\order\OrderGoods`.
 */
class OrderGoodsSearch extends OrderGoods
{
    /**
     * 素材名称
     * @var array 
     */
    public $meida_name;
    
    /**
     * 素材编号
     * @var int 
     */
    public $meida_sn;
    
    /**
     * 上传者
     * @var int 
     */
    public $uploaded_by;
    
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
            [['id', 'order_id', 'goods_id', 'num', 'is_del', 'created_by', 'created_at', 'updated_at', 'meida_sn', 'uploaded_by'], 'integer'],
            [['order_sn', 'meida_name', 'nickname'], 'safe'],
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
        
        // 查询数据
        $query = self::find()->from(['Goods' => self::tableName()]);
        
        $this->load($params);  
        
        // 关联素材表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Goods.goods_id');
        $query->leftJoin(['Order' => Order::tableName()], 'Order.id = Goods.order_id');
        // 关联用户表
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.created_by');
        $query->leftJoin(['User' => User::tableName()], 'User.id = Goods.created_by');
        // 复制对象
        $queryCopy = clone $query;
        
        // 必要条件
        $query->andFilterWhere([
            'Media.id' => $this->meida_sn,
            'Media.created_by' => $this->uploaded_by,
            'Goods.created_by' => $this->created_by,
            'Goods.is_del' => 0,
        ]);
        // 订单的状态要是大于【待确定】
        $query->andFilterWhere(['>=', 'Order.order_status', Order::ORDER_STATUS_TO_BE_CONFIRMED,]);
        
        // 模糊查询
        $query->andFilterWhere(['like', 'Media.name', $this->meida_name]);
        
        // 按商品id分组
        $query->groupBy(['Goods.id']);
        
        // 计算总数
        $totalCount = $query->count('*');
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
       
        // 过滤重复
        $query->with('media', 'order', 'createdBy', 'media.createdBy');
        
        // 用户结果
        $uploadedByResult = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy(['AdminUser.id'])->all();
        $createdByResult = $queryCopy->select(['User.id', 'User.nickname'])
            ->groupBy(['User.id'])->all();
        
        // 查询结果
        $goodsResult = $query->all();
        
        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'uploadedBys' => $uploadedByResult,
                'createdBys' => $createdByResult,
                'goods' => $goodsResult
            ]
        ];
    }
}
