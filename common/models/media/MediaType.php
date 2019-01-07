<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%media_type}}".
 *
 * @property string $id
 * @property string $name 媒体类型名称
 * @property string $sign 类型标识：video,audio,image,docment,h5
 * @property int $is_del 是否已删除 0否 1是
 * 
 * @property MediaTypeDetail[] $typeDetails     获取媒体类型的后缀
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
            [['name'], 'required'],
            [['name', 'sign'], 'string', 'max' => 20],
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
            'sign' => Yii::t('app', 'Sign'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getTypeDetails()
    {
        return $this->hasMany(MediaTypeDetail::class, ['type_id' => 'id'])->where(['is_del' => 0]);
    }
    
    /**
     * 返回媒体类型信息
     * @param array $condition      查询条件
     * @param bool $key_to_value    返回键值对形式
     * @return array(array|Array) 
     */
    public static function getMediaByType($condition = [], $key_to_value = true) 
    {
        if(is_null($condition)){
            $condition = ['is_del' => 0];
        }else{
            $condition = array_merge($condition, ['is_del' => 0]);
        }
        
        $query = self::find();
        $query->andFilterWhere($condition);
        
        $mediaTypes = [];
        foreach ($query->all() as $id => $type) {
            $mediaTypes[] = $type;
        }
       
        return $key_to_value ? ArrayHelper::map($mediaTypes, 'id', 'name') : $mediaTypes;
    }
}
