<?php

namespace common\models\media;

use Yii;
use yii\behaviors\TimestampBehavior;
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
 */
class MediaRecycle extends ActiveRecord
{
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
}
