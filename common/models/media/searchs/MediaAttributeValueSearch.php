<?php

namespace common\models\media\searchs;

use common\models\media\MediaAttributeValue;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MediaAttributeValueSearch represents the model behind the search form of `common\models\media\MediaAttributeValue`.
 */
class MediaAttributeValueSearch extends MediaAttributeValue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'attribute_id', 'is_del'], 'integer'],
            [['value'], 'safe'],
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
        $this->attribute_id = ArrayHelper::getValue($params, 'attribute_id');  //属性id
        
        $query = MediaAttributeValue::find();
        
        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'is_del' => $this->is_del,
        ]);

        $query->andFilterWhere(['like', 'value', $this->value]);

        return $query->all();
    }
}
