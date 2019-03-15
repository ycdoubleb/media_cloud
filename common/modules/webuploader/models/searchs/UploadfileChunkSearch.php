<?php

namespace common\modules\webuploader\models\searchs;

use common\modules\webuploader\models\UploadfileChunk;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * UploadfileChunkSearch represents the model behind the search form of `common\modules\webuploader\models\UploadfileChunk`.
 */
class UploadfileChunkSearch extends UploadfileChunk
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chunk_id', 'file_md5', 'chunk_path'], 'safe'],
            [['chunk_index', 'is_del', 'created_at', 'updated_at'], 'integer'],
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
        $query = (new Query())
            ->select(['chunk_id', 'file_md5', 'chunk_path', 'chunk_index', 'created_at'])
            ->from(['UploadfileChunk' => UploadfileChunk::tableName()]);

        $query->orderBy([
            'file_md5' => SORT_ASC,
            'chunk_index' => SORT_ASC,
        ]);
        
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
        $query->andWhere(['is_del' => 0]);

        return $dataProvider;
    }
}
