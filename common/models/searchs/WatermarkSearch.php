<?php

namespace common\models\searchs;

use common\models\Watermark;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WatermarkSearch represents the model behind the search form of `common\models\media\Watermark`.
 */
class WatermarkSearch extends Watermark
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'is_del', 'is_selected', 'created_at', 'updated_at'], 'integer'],
            [['name', 'url', 'oss_key', 'refer_pos'], 'safe'],
            [['width', 'height', 'dx', 'dy'], 'number'],
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
        $query = Watermark::find();

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
            'type' => $this->type,
            'width' => $this->width,
            'height' => $this->height,
            'dx' => $this->dx,
            'dy' => $this->dy,
            'is_del' => $this->is_del,
            'is_selected' => $this->is_selected,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'oss_key', $this->oss_key])
            ->andFilterWhere(['like', 'refer_pos', $this->refer_pos]);

        return $dataProvider;
    }
}
