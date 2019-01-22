<?php

namespace common\models\media\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\media\MediaAttribute;

/**
 * MediaAttributeSearch represents the model behind the search form of `common\models\media\MediaAttribute`.
 */
class MediaAttributeSearch extends MediaAttribute
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'index_type', 'input_type', 'sort_order', 'is_del', 'is_required', 'value_length'], 'integer'],
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
        $query = MediaAttribute::find();

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'index_type' => $this->index_type,
            'input_type' => $this->input_type,
            'sort_order' => $this->sort_order,
            'is_del' => $this->is_del,
            'value_length' => $this->value_length,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $query->all();
    }
}
