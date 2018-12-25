<?php

namespace common\models\media;

use common\models\media\Media;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%dir}}".
 *
 * @property string $id
 * @property string $name   分类名称
 * @property int $level     等级：0顶级 1~3
 * @property string $path   继承路径，多个逗号分隔
 * @property string $parent_id 父级id
 * @property int $sort_order    排序
 * @property string $image  图标路径
 * @property int $is_del    是否显示
 * @property int $is_public 是否公共目录： 1是，0否
 * @property string $des    描述
 * @property string $created_by 创建者ID，关联admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property Media $id0
 */
class Dir extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dir}}';
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
            [['level', 'parent_id', 'sort_order', 'is_del', 'is_public', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['path', 'image', 'des'], 'string', 'max' => 255],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Media::className(), 'targetAttribute' => ['id' => 'dir_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'level' => Yii::t('app', 'Level'),
            'path' => Yii::t('app', 'Path'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'image' => Yii::t('app', 'Image'),
            'is_del' => Yii::t('app', 'Is Del'),
            'is_public' => Yii::t('app', 'Is Public'),
            'des' => Yii::t('app', 'Des'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Media::className(), ['dir_id' => 'id']);
    }
}
