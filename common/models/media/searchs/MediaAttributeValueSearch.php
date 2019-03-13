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
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);               
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);     
        
        // 查询属性值
        $query = MediaAttributeValue::find();
        
        // 必要条件
        $query->andFilterWhere([
            'attribute_id' => $this->attribute_id,
            'is_del' => $this->is_del,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'value', $this->value]);
        
        // 复制对象
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(id) AS totalCount'])
            ->asArray()->one();
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);

        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'attVal' => $query->all()
            ],
        ];
    }
}
