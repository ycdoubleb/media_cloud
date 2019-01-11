<?php

namespace backend\modules\operation_admin\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\Acl;

/**
 * AclSearch represents the model behind the search form of `common\models\media\Acl`.
 */
class AclSearch extends Acl
{
    /**
     * 媒体编号
     * @var array 
     */
    public $meida_sn;
            
    /**
     * 订单编号
     * @var int 
     */
    public $order_sn;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'media_id', 'user_id', 'status', 'visit_count', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['name', 'order_sn'], 'safe'],
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
        $query = Acl::find();

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
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'media_id' => $this->media_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'visit_count' => $this->visit_count,
            'expire_at' => $this->expire_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'order_sn', $this->order_sn]);

        return $dataProvider;
    }
}
