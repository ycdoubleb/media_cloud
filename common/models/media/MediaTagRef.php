<?php

namespace common\models\media;

use common\models\api\ApiResponse;
use common\models\Tags;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%media_tag_ref}}".
 *
 * @property string $id
 * @property string $object_id  资源ID
 * @property string $tag_id     课程标签ID
 * @property int $is_del        是否删除：0否 1是
 *
 * @property Media $media
 * @property Tags $tags
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
//            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['object_id' => 'id']],
//            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Tags::className(), 'targetAttribute' => ['id' => 'id']],
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
    public function getMedia()
    {
        return $this->hasOne(Media::className(), ['id' => 'object_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTags()
    {
        return $this->hasOne(Tags::className(), ['id' => 'tag_id']);
    }
    
    /**
     * 保存媒体标签关联关系
     * @param int $media_id
     * @param Tags $tags    媒体标签
     * @return ApiResponse
     */
    public static function saveMediaTagRef($media_id, $tags)
    {
        try {
            // 如果标签为空则返回
            if($tags == null) return;
            
            //删除已存在的标签
            self::updateAll(['is_del' => 1], ['object_id' => $media_id]);
            //准备数据
            $mediaTags = [];
            foreach ($tags as $tag) {
                /* @var $tag Tags */
                $mediaTags[] = [$media_id, $tag->id];
            }
            //保存关联
            \Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['object_id', 'tag_id'], $mediaTags)->execute();
            //累加引用次数
            Tags::updateAllCounters(['ref_count' => 1], ['id' => ArrayHelper::getColumn($tags, 'id')]);
            
        }catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /**
     * 获取所有对象的标签
     * @param string|Query $objectId  对象id
     * @param boolen $key_to_value    默认返回键值对模式
     * @return array|Query
     */
    public static function getTagsByObjectId($objectId, $key_to_value = true)
    {
        //查询对象下的标签
        $tagRef = self::find()->select(['TagRef.object_id'])->from(['TagRef' => MediaTagRef::tableName()]);
        //关联查询
        $tagRef->leftJoin(['Tags' => Tags::tableName()], 'Tags.id = TagRef.tag_id');
        //必要条件查询
        $tagRef->where(['TagRef.is_del' => 0]);
        //条件查询
        $tagRef->andFilterWhere(['TagRef.object_id' => $objectId]);
        //以id排序
        $tagRef->orderBy('TagRef.id');
        /** 默认返回键值对模式 */
        if($key_to_value) {
            $tagRef->select(['TagRef.tag_id', 'Tags.name']);
            return ArrayHelper::map($tagRef->asArray()->all(), 'tag_id', 'name');
        }
        //以对象id为分组
        $tagRef->groupBy('TagRef.object_id');
        
        return $tagRef;
    }
}
