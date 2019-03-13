<?php

namespace common\models\searchs;

use common\models\Watermark;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
     * 创建应用搜索查询的数据提供程序实例
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
        
        // 查询水印
        $query = Watermark::find();

        // 必要条件
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

        // 模糊查询
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'oss_key', $this->oss_key])
            ->andFilterWhere(['like', 'refer_pos', $this->refer_pos]);
        
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
                'watermark' => $query->all()
            ],
        ];
    }
}
