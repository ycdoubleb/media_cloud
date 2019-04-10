<?php

namespace common\modules\webuploader\models\searchs;

use common\models\AdminUser;
use common\modules\webuploader\models\Uploadfile;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * UploadfileSearch represents the model behind the search form of `common\modules\webuploader\models\Uploadfile`.
 */
class UploadfileSearch extends Uploadfile {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'size', 'oss_upload_status', 'is_del', 'created_at', 'updated_at'], 'integer'],
            [['name', 'md5', 'path', 'thumb_url', 'ext', 'metadata', 'oss_key', 'created_by'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
                ->select(['Uploadfile.id', 'Uploadfile.name', 'Uploadfile.path', 'Uploadfile.size', 'Uploadfile.oss_upload_status',
                    'Uploadfile.md5', 'AdminUser.nickname AS created_by', 'Uploadfile.created_at'])
                ->from(['Uploadfile' => Uploadfile::tableName()]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'id'
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //关联查询创建者
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Uploadfile.created_by');

        // grid filtering conditions
        $query->andWhere(['is_del' => 0]);

        $query->andFilterWhere(['md5' => $this->md5]);
        $query->andFilterWhere(['like', 'Uploadfile.name', $this->name]);
        
        return $dataProvider;
    }
    
}
