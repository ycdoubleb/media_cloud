<?php

namespace common\models\media\searchs;

use common\models\AdminUser;
use common\models\media\Media;
use common\models\media\MediaApprove;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * MediaApproveSearh represents the model behind the search form of `common\models\media\MediaApprove`.
 */
class MediaApproveSearch extends MediaApprove
{
    /**
     * 媒体名称
     * @var string 
     */
    public $media_name;
    
    /**
     * 用户昵称
     * @var string 
     */
    public $nickname;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'media_id', 'type', 'status', 'result', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content', 'feedback', 'media_name', 'nickname'], 'safe'],
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
     * @return array
     */
    public function search($params)
    {
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);      
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);                           
        
        //所有用户
        $userResults = AdminUser::find()->select(['id', 'nickname'])->all();
        
        // 查询审核列表
        $query = self::find()->from(['Approve' => MediaApprove::tableName()]);
        
//        ->select(['Approve.*', 'COUNT(Approve.id) AS totalCount']);
        
        // 关联媒体表
        if(!empty($this->media_id) || !empty($this->media_name)){
            $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Approve.media_id');
            // 按媒体id查询
            $query->andFilterWhere(['Approve.media_id' => $this->media_id]);
            // 模糊查询
            $query->andFilterWhere(['like', 'Media.name', $this->media_name]);
        }
        
        // 必要要条件
        $query->andFilterWhere([
            'Approve.type' => $this->type,
            'Approve.status' => $this->status,
            'Approve.result' => $this->result,
            'Approve.handled_by' => $this->handled_by,
            'Approve.created_by' => $this->created_by,
        ]);
        $query->andFilterWhere(['!=', 'Approve.status', self::STATUS_CANCELED]);
       
        // 复制对象
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(Approve.id) AS totalCount'])
            ->asArray()->one();
        
        $query->select(['Approve.*']);
        
        // 显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);
        
        // 过滤重复
        $query->with('media', 'media.mediaType', 'handledBy', 'createdBy');
               
        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'users' => $userResults,
                'approves' => $query->all()
            ],
        ];
    }
}
