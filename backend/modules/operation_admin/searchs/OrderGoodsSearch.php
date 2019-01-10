<?php

namespace backend\modules\operation_admin\searchs;

use common\models\AdminUser;
use common\models\media\Media;
use common\models\order\OrderGoods;
use common\models\User;
use yii\base\Model;

/**
 * OrderGoodsSearch represents the model behind the search form of `common\models\order\OrderGoods`.
 */
class OrderGoodsSearch extends OrderGoods
{
    /**
     * 媒体名称
     * @var array 
     */
    public $meida_name;
    
    /**
     * 媒体编号
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
        // 查询数据
        $query = self::find()->from(['Goods' => self::tableName()]);
        
        // 关联媒体表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Goods.goods_id');
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
        
        // 模糊查询
        $query->andFilterWhere(['like', 'Media.name', $this->meida_name]);
        
        // 过滤重复
        $query->with('media', 'order', 'createdBy');
        // 用户结果
        $uploadedByResult = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy(['AdminUser.id'])->all();
        $createdByResult = $queryCopy->select(['User.id', 'User.nickname'])
            ->groupBy(['User.id'])->all();
        
        // 查询结果
        $goodsResult = $query->all();
        
        return [
            'filter' => $params,
            'data' => [
                'uploadedBys' => $uploadedByResult,
                'createdBys' => $createdByResult,
                'goods' => $goodsResult
            ]
        ];
    }
}
