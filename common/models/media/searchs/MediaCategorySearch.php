<?php

namespace common\models\media\searchs;

use common\models\media\MediaCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MediaCategorySearch represents the model behind the search form of `common\models\media\MediaCategory`.
 */
class MediaCategorySearch extends MediaCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
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
        
        // 查询类目
        $query = MediaCategory::find();

        // 必要条件
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);

        return $query->all();
    }
}
