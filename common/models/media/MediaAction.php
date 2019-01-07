<?php

namespace common\models\media;

use common\models\api\ApiResponse;
use common\models\AdminUser;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "{{%media_action}}".
 *
 * @property string $id
 * @property string $media_id   媒体id，关联media表id字段
 * @property string $title      操作标题/类型
 * @property string $content    操作内容
 * @property string $created_by 操作人id,关联mc_admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property Media $media
 * @property AdminUser $createdBy 
 */
class MediaAction extends ActiveRecord
{
    
    /**
     * 标题
     * @var array 
     */
    public static $titleMap = [
        'create' => '新增',
        'update' => '修改',
        'delete' => '删除',
        'move' => '移动',
        'create-catalog' => '新增',
        'update-catalog' => '修改',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_action}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['media_id'], 'required'],
            [['media_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'media_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'created_by']);
    }
    
    /**
     * 保存媒体操作日志
     * @param int $media_id  
     * @param string $content  内容（字符串）| 加载渲染的模板
     * @param string $title 标题
     * @return ApiResponse
     */
    public static function savaMediaAction($media_id, $content, $title = null)
    {
        $data = []; // 返回的数据
        /** 开启事务 */
        $trans = Yii::$app->db->beginTransaction();
        try
        {  
            $model = new MediaAction([
                'media_id' => $media_id,
                'title' => $title == null ? self::$titleMap[Yii::$app->controller->action->id] : $title,
                'content' => $content,
                'created_by' => Yii::$app->user->id,
            ]);
             
            if($model->save()){
                $trans->commit();  //提交事务
                $data = new ApiResponse(ApiResponse::CODE_COMMON_OK);
            }else{
                $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, null, $model->getErrorSummary(true));
            }
        }catch (Exception $ex) {
            $trans ->rollBack(); //回滚事务
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
        }
        
        return $data;
    }
}
