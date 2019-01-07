<?php

namespace common\models\media;

use common\models\AdminUser;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_recycle}}".
 *
 * @property string $id
 * @property string $media_id   媒体ID，关联media表id字段
 * @property int $result        处理结果
 * @property int $status        处理状态 0未处理 1已处理
 * @property string $handled_by 处理人ID,关联admin_user表id字段
 * @property string $handled_at 处理时间
 * @property string $created_by 删除人ID（放入回收站的人），关联admin_user表id
 * @property string $created_at 创建时间（放入回收站时间）
 * @property string $updated_at 更新时间
 * 
 * @property Media $media 
 * @property AdminUser $handledBy
 * @property AdminUser $createdBy
 */
class MediaRecycle extends ActiveRecord
{
    /** 状态-未处理 */
    const STATUS_NOT_HANDLE = 0;
    
    /** 状态-已处理 */
    const STATUS_ALREADY_HANDLE = 1;
    
    /** 结果-已还原 */
    const RESULT_ALREADY_RECOVERY = 0;
    
    /** 结果-已删除 */
    const RESULT_ALREADY_DELETE = 1;
    
    /**
     * 审核状态
     * @var array 
     */
    public static $statusMap = [
      self::STATUS_NOT_HANDLE =>  '未处理',
      self::STATUS_ALREADY_HANDLE =>  '已处理',
    ];
    
    /**
     * 审核结果
     * @var array 
     */
    public static $resultMap = [
      self::RESULT_ALREADY_RECOVERY =>  '已还原',
      self::RESULT_ALREADY_DELETE =>  '已删除',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_recycle}}';
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
            [['media_id', 'result', 'status', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
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
            'result' => Yii::t('app', 'Result'),
            'status' => Yii::t('app', 'Status'),
            'handled_by' => Yii::t('app', 'Handled By'),
            'handled_at' => Yii::t('app', 'Handled At'),
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
    public function getHandledBy()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'handled_by']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'created_by']);
    }
}
