<?php

namespace common\models\media;

use Yii;

/**
 * This is the model class for table "{{%media_detail}}".
 *
 * @property string $id
 * @property string $media_id   媒体id，关联media表id字段
 * @property string $content    媒体描述/简介
 * @property string $mts_watermark_ids 转码水印id,多个使用,分隔，关联watermark表id字段
 */
class MediaDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'media_id'], 'required'],
            [['id', 'media_id'], 'integer'],
            [['content'], 'string'],
            [['mts_watermark_ids'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'content' => Yii::t('app', 'Content'),
            'mts_watermark_ids' => Yii::t('app', 'Mts Watermark Ids'),
        ];
    }
}
