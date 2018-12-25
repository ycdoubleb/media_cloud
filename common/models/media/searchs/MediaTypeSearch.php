<?php

namespace common\models\media\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\MediaType;

/**
 * MediaTypeSearch represents the model behind the search form of `common\models\media\MediaType`.
 */
class MediaTypeSearch extends MediaType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[' id', 'is_del'], 'integer'],
            [['name'], 'safe'],
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
        $query = MediaType::find();

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
            ' id' => $this-> id,
            'is_del' => $this->is_del,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
