<?php

namespace common\models\media\searchs;

use common\models\media\MediaTypeDetail;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MediaTypeDetailSearch represents the model behind the search form of `common\models\media\MediaTypeDetail`.
 */
class MediaTypeDetailSearch extends MediaTypeDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'is_del'], 'integer'],
            [['name', 'ext', 'icon_url'], 'safe'],
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
        
       // 查询属性配置
        $query = MediaTypeDetail::find();

        // 必要条件
        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
            'is_del' => $this->is_del,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'ext', $this->ext]);

        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        return $query->all();
    }
}
