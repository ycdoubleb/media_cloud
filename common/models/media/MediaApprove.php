<?php

namespace common\models\media;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_approve}}".
 *
 * @property string $id
 * @property string $media_id   媒体ID，关联media表id字段
 * @property int $type          审核类型 1入库申请 2删除申请
 * @property string $content    申请说明
 * @property int $status        状态 0待审核 1已审核
 * @property int $result        审核结果 0不通过 1通过
 * @property string $feedback   审核反馈
 * @property string $handled_by 审核人ID，关联admin_user表id
 * @property string $handled_at 审核时间
 * @property string $created_by 申请人ID，关联admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property Media $media 
 * @property user $handledBy
 * @property user $createdBy
 */
class MediaApprove extends ActiveRecord
{
    /** 类型-入库申请 */
    const TYPE_INTODB_APPROVE = 1;
    
    /** 类型-删除申请 */
    const TYPE_DELETE_APPROVE = 2;
    
    /** 状态-待审核 */
    const STATUS_WAIT_APPROVE = 0;
    
    /** 状态-已审核 */
    const STATUS_ALREADY_APPROVE = 1;
    
    /** 结果-不通过 */
    const RESULT_PASS_NO = 0;
    
    /** 结果-通过 */
    const RESULT_PASS_YES = 1;

    /**
     * 审核类型
     * @var array 
     */
    public static $typeMap = [
      self::TYPE_INTODB_APPROVE =>  '入库申请',
      self::TYPE_DELETE_APPROVE =>  '删除申请',
    ];
    
    /**
     * 审核状态
     * @var array 
     */
    public static $statusMap = [
      self::STATUS_WAIT_APPROVE =>  '待审核',
      self::STATUS_ALREADY_APPROVE =>  '已审核',
    ];
    
    /**
     * 审核结果
     * @var array 
     */
    public static $resultMap = [
      self::RESULT_PASS_NO =>  '不通过',
      self::RESULT_PASS_YES =>  '已通过',
    ];
    
    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_approve}}';
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
            [['media_id', 'type', 'status', 'result', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content', 'feedback'], 'string', 'max' => 255],
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
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'result' => Yii::t('app', 'Result'),
            'feedback' => Yii::t('app', 'Feedback'),
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
        return $this->hasOne(User::className(), ['id' => 'handled_by']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
