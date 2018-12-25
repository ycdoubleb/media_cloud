<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_type_detail}}".
 *
 * @property string $id
 * @property string $type_id    媒体类型id，关联media_type表id字段
 * @property string $name       媒体类型名称
 * @property string $ext        后缀名,eg:mp4
 * @property string $icon_url   图标路径
 * @property int $is_del        是否删除
 */
class MediaTypeDetail extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_type_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id'], 'required'],
            [['type_id', 'is_del'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['ext'], 'string', 'max' => 10],
            [['icon_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type_id' => Yii::t('app', 'Type ID'),
            'name' => Yii::t('app', 'Name'),
            'ext' => Yii::t('app', 'Ext'),
            'icon_url' => Yii::t('app', 'Icon Url'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
}
