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
        $this->load($params);
        
        //分页
        $page = ArrayHelper::getValue($params, 'page', 1);  
        //显示数
        $limit = ArrayHelper::getValue($params, 'limit', 10);                           
        //所有用户
        $userResults = AdminUser::find()->select(['id', 'nickname'])->all();
        
        // 查询回收站数据
        $query = self::find()->from(['Recycle' => MediaRecycle::tableName()]);

        // 关联媒体表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Recycle.media_id');
        
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
            ['like', 'Media.tags', $this->keyword],
        ]);
        
        // 复制查询
        $queryCopy = clone $query;
        // 查询计算总数量
        $totalResults = $queryCopy->select(['COUNT(Recycle.id) AS totalCount'])
            ->asArray()->one();
        
        //显示数量
        $query->offset(($page - 1) * $limit)->limit($limit);

        // 过滤重复
        $query->with('media', 'media.mediaType', 'handledBy', 'createdBy');
        
        return [
            'filter' => $params,
            'total' => $totalResults['totalCount'],
            'data' => [
                'users' => $userResults,
                'recycles' => $query->all()
            ],
        ];
    }
}
