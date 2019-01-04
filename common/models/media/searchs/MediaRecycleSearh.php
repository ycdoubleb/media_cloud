<?php

namespace common\models\media\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\MediaRecycle;

/**
 * MediaRecycleSearh represents the model behind the search form of `common\models\media\MediaRecycle`.
 */
class MediaRecycleSearh extends MediaRecycle
{
    public $keyword;
    public $type_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'media_id', 'type_id', 'result', 'status', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['keyword'], 'safe'],
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
        $query = MediaRecycle::find();

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
            'media_id' => $this->media_id,
            'result' => $this->result,
            'status' => $this->status,
            'handled_by' => $this->handled_by,
            'handled_at' => $this->handled_at,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
