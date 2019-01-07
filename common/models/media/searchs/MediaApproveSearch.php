<?php

namespace common\models\media\searchs;

use common\models\AdminUser;
use common\models\media\Media;
use common\models\media\MediaApprove;
use yii\base\Model;

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
        $query = self::find()->from(['Approve' => MediaApprove::tableName()]);
        
        $this->load($params);

        // 关联用户表         
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], '(AdminUser.id = Approve.handled_by or AdminUser.id =Approve.created_by)');
        // 复制查询
        $queryCopy = clone $query;
        
        // 关联媒体表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Approve.media_id');
        
        // 必要要条件
        $query->andFilterWhere([
            'Approve.media_id' => $this->media_id,
            'Approve.type' => $this->type,
            'Approve.status' => $this->status,
            'Approve.result' => $this->result,
            'Approve.handled_by' => $this->handled_by,
            'Approve.created_by' => $this->created_by,
        ]);

        // 模糊查询
        $query->andFilterWhere(['like', 'Media.name', $this->media_name]);
        
        // 按审批id分组
        $query->groupBy(['Approve.id']);
        
        // 过滤重复
        $query->with('media', 'media.mediaType', 'handledBy', 'createdBy');

        // 用户查询结果
        $userResults = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy('AdminUser.id')->all();
            
        // 审批查询结果
        $approveResults = $query->all();
        
        return [
            'filter' => $params,
            'data' => [
                'users' => $userResults,
                'approves' => $approveResults
            ],
        ];
    }
}
