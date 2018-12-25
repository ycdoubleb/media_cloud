<?php

namespace common\models\media;

use Yii;

/**
 * This is the model class for table "{{%media_att_value_ref}}".
 *
 * @property string $id
 * @property string $media_id       媒体id，关联media表id字段
 * @property string $attribute_id   属性id，关联media_attribute表id字段
 * @property string $attribute_value_id 属性值id，关联media_attribute_value表id字段
 * @property int $is_del    是否已删除 0否 1是
 */
class MediaAttValueRef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_att_value_ref}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['media_id', 'attribute_id', 'attribute_value_id'], 'required'],
            [['media_id', 'attribute_id', 'attribute_value_id', 'is_del'], 'integer'],
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
            'attribute_id' => Yii::t('app', 'Attribute ID'),
            'attribute_value_id' => Yii::t('app', 'Attribute Value ID'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
}
