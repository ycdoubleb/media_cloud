<?php

namespace common\models\media;

use Yii;
use yii\behaviors\TimestampBehavior;
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
 */
class MediaApprove extends ActiveRecord
{
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
}
