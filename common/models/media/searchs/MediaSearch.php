<?php

namespace common\models\media\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\Media;

/**
 * MediaSearch represents the model behind the search form of `common\models\media\Media`.
 */
class MediaSearch extends Media
{
    public $keyword;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 'status', 'mts_status', 'del_status', 'is_link', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['name', 'cover_url', 'url', 'keyword'], 'safe'],
            [['price', 'duration'], 'number'],
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
        $query = Media::find();

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
            'category_id' => $this->category_id,
            'type_id' => $this->type_id,
            'owner_id' => $this->owner_id,
            'dir_id' => $this->dir_id,
            'file_id' => $this->file_id,
            'price' => $this->price,
            'duration' => $this->duration,
            'size' => $this->size,
            'status' => $this->status,
            'mts_status' => $this->mts_status,
            'del_status' => 0,
            'is_link' => $this->is_link,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->keyword]);

        return $dataProvider;
    }
}
