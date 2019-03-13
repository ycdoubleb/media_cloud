<?php

namespace common\models\media\searchs;

use common\models\media\MediaType;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
            [['id', 'is_del'], 'integer'],
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
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);               
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);     
        
        // 查询类型
        $query = MediaType::find();

        // 必要条件
        $query->andFilterWhere([
            'id' => $this->id,
            'is_del' => 0,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        // 复制对象
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(id) AS totalCount'])
            ->asArray()->one();
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('typeDetails');

        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'type' => $query->all()
            ],
        ];
    }
}
