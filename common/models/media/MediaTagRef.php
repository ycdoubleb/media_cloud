<?php

namespace common\models\media;

use common\models\Tags;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_tag_ref}}".
 *
 * @property string $id
 * @property string $object_id  资源ID
 * @property string $tag_id     课程标签ID
 * @property int $is_del        是否删除：0否 1是
 *
 * @property Media $object
 * @property Tags $id0
 */
class MediaTagRef extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_tag_ref}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id'], 'required'],
            [['object_id', 'tag_id', 'is_del'], 'integer'],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['object_id' => 'id']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Tags::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'object_id' => Yii::t('app', 'Object ID'),
            'tag_id' => Yii::t('app', 'Tag ID'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Media::className(), ['id' => 'object_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Tags::className(), ['id' => 'id']);
    }
    
    /**
     * 保存标签
     * @param string        $video_id      素材ID
     * @param array<Tags>   $tags         标签
     */
    public static function saveVideoTags($video_id,$tags){
        //准备数据
        $rows = [];
        /* @var $tag Tags */
        foreach ($tags as $tag) {
            $rows[] = [$video_id, $tag->id, 2];
        }
        //保存关联
        \Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['object_id', 'tag_id', 'type'], $rows)->execute();
        //累加引用次数
        Tags::updateAllCounters(['ref_count' => 1], ['id' => ArrayHelper::getColumn($tags, 'id')]);
    }
}
