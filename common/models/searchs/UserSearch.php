<?php

namespace common\models\searchs;

use common\models\User;
use common\models\UserProfile;
use yii\base\Model;
use yii\data\ActiveDataProvider;

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
        $query = self::find()->from(['User' => User::tableName()]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'id',
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // 关联用户配置
        $query->leftJoin(['UserProfile' => UserProfile::tableName()], 'UserProfile.user_id = User.id');
        
        // 必要条件
        $query->andFilterWhere([
            'User.username' => $this->username,
            'User.status' => $this->status,
            'UserProfile.is_certificate' => $this->is_certificate
        ]);

        $query->andFilterWhere(['like', 'User.nickname', $this->nickname])
            ->andFilterWhere(['like', 'UserProfile.company', $this->company])
            ->andFilterWhere(['like', 'UserProfile.department', $this->department]);

        // 过滤重复
        $query->with('profile');
        
        return $dataProvider;
    }
}
