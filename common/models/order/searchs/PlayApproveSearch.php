<?php

namespace common\models\order\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\order\PlayApprove;

/**
 * PlayApproveSearch represents the model behind the search form of `common\models\order\PlayApprove`.
 */
class PlayApproveSearch extends PlayApprove
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'status', 'result', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['certificate_url', 'content', 'feedback'], 'safe'],
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
        $query = PlayApprove::find();

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
            'status' => $this->status,
            'result' => $this->result,
            'handled_by' => $this->handled_by,
            'handled_at' => $this->handled_at,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'certificate_url', $this->certificate_url])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'feedback', $this->feedback]);

        return $dataProvider;
    }
    
    /**
     * 单独搜索某个订单的审核数据
     * @param int $id
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchDetails($id, $params)
    {
        $query = PlayApprove::find();

        // add conditions that should always apply here
        
        // 过滤订单ID
        $query->where(['order_id' => $id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
