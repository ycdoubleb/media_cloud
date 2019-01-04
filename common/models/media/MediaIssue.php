<?php

namespace common\models\media;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%media_issue}}".
 *
 * @property string $id
 * @property string $media_id   媒体ID，关联media表id字段
 * @property int $result        处理结果 0未解决 1已解决
 * @property int $status        处理状态 0未处理 1已处理
 * @property string $feedback   处理反馈
 * @property int $type          问题类型 1版权 2内容 3标签属性 4访问 5其它
 * @property string $content    问题内容
 * @property string $handled_by 处理人ID，关联admin_user表id字段
 * @property string $handled_at 处理时间
 * @property string $created_by 创建人（提交人），关联user表id字段
 * @property string $created_at 创建时间（提交时间）
 * @property int $updated_at
 * 
 * @property Media $media
 */
class MediaIssue extends ActiveRecord
{
    /** 版权问题 */
    const ISSUE_COPYRIGHT = 1;
    /** 内容问题 */
    const ISSUE_CONTENT = 2;
    /** 标签属性问题 */
    const ISSUE_ATTRIBUTE = 3;
    /** 访问问题 */
    const ISSUE_VISIT = 4;
    /** 其它问题 */
    const ISSUE_OTHER = 5;
    
    /**
     * 问题类型
     * @var array
     */
    public static $issueName = [
        self::ISSUE_COPYRIGHT => '版权',
        self::ISSUE_CONTENT => '内容',
        self::ISSUE_ATTRIBUTE => '标签属性',
        self::ISSUE_VISIT => '访问',
        self::ISSUE_OTHER => '其它',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_issue}}';
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
            [['media_id', 'result', 'status', 'type', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['feedback', 'content'], 'string'],
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
            'feedback' => Yii::t('app', 'Feedback'),
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
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
        return $this->hasOne(Media::class, ['id' => 'media_id']);
    }
}
