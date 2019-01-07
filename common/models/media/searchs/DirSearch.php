<?php

namespace common\models\media\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\Dir;

/**
 * DirSearh represents the model behind the search form of `common\models\media\Dir`.
 */
class DirSearch extends Dir
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'level', 'parent_id', 'sort_order', 'is_del', 'is_public', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'path', 'image', 'des'], 'safe'],
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
        $query = Dir::find();

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
            'level' => $this->level,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_del' => $this->is_del,
            'is_public' => $this->is_public,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'des', $this->des]);

        return $dataProvider;
    }
}
