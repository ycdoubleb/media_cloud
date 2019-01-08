<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%video_url}}".
 *
 * @property string $id 文件ID
 * @property string $media_id 媒体ID,关联media表id字段
 * @property string $job_id 阿里任务ID，可查询转码任务详细信息
 * @property string $name 格式名，原始，流畅，标清，高清，超清
 * @property string $url 路径
 * @property string $oss_key oss名称
 * @property int $level 视频质量：0流畅 1标清 2高清 3超清
 * @property string $size 大小B
 * @property string $width 宽度
 * @property string $height 高度
 * @property string $duration 时长
 * @property string $bitrate 码率
 * @property int $is_original 是否为原始格式 0否 1是
 * @property int $is_del 是否已经删除标记：0未删除，1已删除
 * @property string $created_by 上传人ID，关联admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class VideoUrl extends ActiveRecord
{
    public static $videoLevelName = ['原始', '流畅', '标清', '高清', '超清',];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%video_url}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['media_id'], 'required'],
            [['media_id', 'level', 'size', 'width', 'height', 'bitrate', 'is_original', 'is_del', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['duration'], 'number'],
            [['job_id', 'name'], 'string', 'max' => 50],
            [['url', 'oss_key'], 'string', 'max' => 255],
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
            'job_id' => Yii::t('app', 'Job ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'oss_key' => Yii::t('app', 'Oss Key'),
            'level' => Yii::t('app', 'Level'),
            'size' => Yii::t('app', 'Size'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'duration' => Yii::t('app', 'Duration'),
            'bitrate' => Yii::t('app', 'Bitrate'),
            'is_original' => Yii::t('app', 'Is Original'),
            'is_del' => Yii::t('app', 'Is Del'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
