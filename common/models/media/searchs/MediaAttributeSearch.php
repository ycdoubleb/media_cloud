<?php

namespace common\models\media\searchs;

use common\models\media\MediaAttribute;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);               
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);     
        
        // 查询属性
        $query = MediaAttribute::find();

        // 必要条件
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'index_type' => $this->index_type,
            'input_type' => $this->input_type,
            'sort_order' => $this->sort_order,
            'is_del' => $this->is_del,
            'value_length' => $this->value_length,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('category');

        return $query->all();
    }
}
