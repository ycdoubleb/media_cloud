<?php

namespace common\models\searchs;

use common\models\User;
use common\models\UserProfile;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    
    /**
     * 公司
     * @var string 
     */
    public $company;
    
    /**
     * 部门
     * @var string 
     */
    public $department;
    
    /**
     * 认证
     * @var int 
     */
    public $is_certificate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'sex', 'status', 'access_token_expire_time', 'created_at', 'updated_at', 'is_certificate'], 'integer'],
            [['username', 'nickname', 'password_hash', 'password_reset_token', 'phone', 'email', 'avatar', 'des', 'auth_key', 'from', 'access_token', 'company', 'department'], 'safe'],
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
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);               
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);     
        
        // 查询前端用户
        $query = self::find()->from(['User' => User::tableName()]);

        // 关联用户配置
        $query->leftJoin(['UserProfile' => UserProfile::tableName()], 'UserProfile.user_id = User.id');
        
        // 必要条件
        $query->andFilterWhere([
            'User.username' => $this->username,
            'User.status' => $this->status,
            'UserProfile.is_certificate' => $this->is_certificate
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'User.nickname', $this->nickname])
            ->andFilterWhere(['like', 'UserProfile.company', $this->company])
            ->andFilterWhere(['like', 'UserProfile.department', $this->department]);

        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('profile');
        
        return $query->all();
        return $dataProvider;
    }
}
