<?php

namespace common\models\media;

use common\utils\MIMEUtil;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%media_type_detail}}".
 *
 * @property string $id
 * @property string $type_id    媒体类型id，关联media_type表id字段
 * @property string $name       媒体类型名称
 * @property string $ext        后缀名,eg:mp4
 * @property string $mime_type  媒体类型eg:image/jpg
 * @property string $icon_url   图标路径
 * @property int $is_del        是否删除
 * 
 * @property MediaType $mediaType   获取媒体类型
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
//            [['name'], 'checkExtensions'],
            [['ext'], 'string', 'max' => 10],
            [['icon_url', 'mime_type'], 'string', 'max' => 255],
        ];
    }

    
    /**
     * 检验扩展名是否支持
     * @param string $attribute     name
     * @param string $params
     */
    public function checkExtensions($attribute, $params)
    {
        $name = $this->getAttribute($attribute);  
        $ext = strtolower($name);   // 小写的扩展
        // 获取mime的key
        $mime_keys = array_keys(MIMEUtil::$mime);
        if(!in_array($ext, $mime_keys)){
            $this->addError($attribute, "该后缀平台暂不支持，请手动输入对应的MIME类型。");  
            return false;  
        }
        return true; 
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
            'mime_type' => Yii::t('app', 'Mime Type'),
            'icon_url' => Yii::t('app', 'Icon Url'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMediaType()
    {
        return $this->hasOne(MediaType::class, ['id' => 'type_id']);
    }
    
    /**
     * 保存前
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->ext = '.' . $this->name;
            $this->mime_type = MIMEUtil::$mime[strtolower($this->name)];
            return true;
        }
        return false;
    }
    
    /**
     * 获取媒体类型拓展信息
     * 如果是转字符串，则默认返回类型拓展的mime_type
     * @param int $type_id
     * @param bool $asString    默认是 true
     * @return array
     */
    public static function getMediaTypeDetailByTypeId($type_id = null, $asString = true)
    {
        $query = self::find()->from(['TypeDetail' => self::tableName()]);
        // 查询的字段
        $query->select(['TypeDetail.*', 'MediaType.name AS type_name']);
        // 必要条件
        $query->andFilterWhere([
            'TypeDetail.is_del' => 0,
            'MediaType.is_del' => 0,
        ]);
        // 按media_id条件查询
        $query->andFilterWhere(['type_id' => $type_id]);
        // 关联属性值表
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = type_id');

        // 查询结果
        $detailResult = $query->asArray()->all();
        
        return $asString ? implode(',', ArrayHelper::getColumn($detailResult, 'mime_type')) : $detailResult;
    }
}
