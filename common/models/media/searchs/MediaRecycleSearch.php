<?php

namespace common\models\media\searchs;

use common\models\AdminUser;
use common\models\media\Media;
use common\models\media\MediaRecycle;
use common\models\media\MediaTagRef;
use common\models\Tags;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * MediaRecycleSearh represents the model behind the search form of `common\models\media\MediaRecycle`.
 */
class MediaRecycleSearch extends MediaRecycle
{
    /**
     * 关键字
     * @var string 
     */
    public $keyword;
    
    /**
     * 媒体类型
     * @var int 
     */
    public $media_type;
    
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
            [['id', 'media_id', 'media_type', 'result', 'status', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['keyword', 'nickname'], 'safe'],
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
        $page = ArrayHelper::getValue($params, 'page', 1);                              //分页
        $limit = ArrayHelper::getValue($params, 'limit', 10);                           //显示数
        
        $query = self::find()->from(['Recycle' => MediaRecycle::tableName()]);

        $this->load($params);
        
        // 关联用户表         
        $query->leftJoin(['AdminUser' => AdminUser::tableName()], '(AdminUser.id = Recycle.handled_by or AdminUser.id =Recycle.created_by)');
        // 复制查询
        $queryCopy = clone $query;

        // 关联媒体表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Recycle.media_id');
        // 关联媒体标签关系表
        $query->leftJoin(['TagRef' => MediaTagRef::tableName()], '(TagRef.object_id = Media.id and TagRef.is_del = 0)');
        // 关联标签表
        $query->leftJoin(['Tags' => Tags::tableName()], 'Tags.id = TagRef.tag_id');
        
        // 必要要条件
        $query->andFilterWhere([
            'Media.type_id' => $this->media_type,
            'Recycle.result' => $this->result,
            'Recycle.status' => $this->status,
            'Recycle.handled_by' => $this->handled_by,
            'Recycle.created_by' => $this->created_by,
        ]);
        
        // 模糊查询
        $query->andFilterWhere(['or', 
            ['like', 'Media.name', $this->keyword],
            ['like', 'Tags.name', $this->keyword],
        ]);
        
        // 按审批id分组
        $query->groupBy(['Recycle.id']);
        
        // 计算总数
        $totalCount = $query->count('*');
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);

        // 过滤重复
        $query->with('media', 'media.mediaType', 'handledBy', 'createdBy');
        
        // 用户查询结果
        $userResults = $queryCopy->select(['AdminUser.id', 'AdminUser.nickname'])
            ->groupBy('AdminUser.id')->all();
            
        // 回收站查询结果
        $recycleResults = $query->all();
        
        return [
            'filter' => $params,
            'total' => $totalCount,
            'data' => [
                'users' => $userResults,
                'recycles' => $recycleResults
            ],
        ];
    }
}
