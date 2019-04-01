<?php

namespace backend\modules\operation_admin\searchs;

use common\models\media\Acl;
use common\models\media\Media;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * AclSearch represents the model behind the search form of `common\models\media\Acl`.
 */
class AclSearch extends Acl
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
            [['id', 'order_id', 'media_id', 'level', 'user_id', 'status', 'visit_count', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['name', 'order_sn', 'url', 'nickname'], 'safe'],
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
        // 分库id
        $category_id = ArrayHelper::getValue($params, 'category_id');   
        
        // 查询数据
        $query = self::find()->from(['Acl' => self::tableName()]);

        $this->load($params);       
        
        // 关联用户表
        $query->leftJoin(['User' => User::tableName()], 'User.id = Acl.user_id');
        // 关联素材表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Acl.media_id');
        
      
        // 必要条件
        $query->andFilterWhere([
            'Acl.id' => $this->id,
            'Acl.order_sn' => $this->order_sn,
            'Acl.media_id' => $this->media_id,
            'Acl.user_id' => $this->user_id,
            'Acl.status' => $this->status,
            'Media.category_id' => $category_id,
        ]);
        
        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        // 复制对象
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(Acl.id) AS totalCount'])
            ->asArray()->one();
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 关联
        $query->with('media', 'order', 'user');
        
        // 用户查询结果
        $userResults = $queryCopy->select(['User.id', 'User.nickname'])->groupBy('User.id')->all();

        // 查询结果
        $aclResults = $query->all();
        
        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'users' => $userResults,
                'acls' => $aclResults,
            ]
        ];
    }
}
