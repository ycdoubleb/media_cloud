<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_type}}".
 *
 * @property string $ id
 * @property string $name   媒体类型名称
 * @property int $is_del    是否已删除 0否 1是
 */
class MediaType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_del'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            ' id' => Yii::t('app', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
}
