<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%media_category}}".
 *
 * @property string $id
 * @property string $name 类目名
 */
class MediaCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
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
        ];
    }
    
    /**
     * 返回媒体类目信息
     * @param array $condition      查询条件
     * @param bool $key_to_value    返回键值对形式
     * @return array(array|Array) 
     */
    public static function getMediaCategory($condition = [], $key_to_value = true) 
    {
        $query = self::find();
        $query->andFilterWhere($condition);
        
        $categorys = [];
        foreach ($query->all() as $id => $category) {
            $categorys[] = $category;
        }
       
        return $key_to_value ? ArrayHelper::map($categorys, 'id', 'name') : $categorys;
    }
}
