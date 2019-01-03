<?php

namespace common\models\media;

use common\components\aliyuncs\Aliyun;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%media_type_detail}}".
 *
 * @property string $id
 * @property string $type_id    媒体类型id，关联media_type表id字段
 * @property string $name       媒体类型名称
 * @property string $ext        后缀名,eg:mp4
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
            //上传头像
            $upload = UploadedFile::getInstance($this, 'icon_url');
            if ($upload != null) {
                //获取后缀名，默认为 png 
                $ext = pathinfo($upload->name,PATHINFO_EXTENSION);
                $img_path = "upload/icons/{$this->id}.{$ext}";
                //上传到阿里云
                Aliyun::getOss()->multiuploadFile($img_path, $upload->tempName);
                $this->icon_url = $img_path . '?rand=' . rand(0, 9999);                
            }
            
            if (trim($this->icon_url) == ''){
                $this->icon_url = $this->getOldAttribute('icon_url');
            }
            
            $this->ext = '.' . $this->name;
            
            return true;
        }
        return false;
    }
    
    /*
     * 数据查找后
     */
    public function afterFind(){
        $this->icon_url = Aliyun::absolutePath($this->icon_url);
    }
}
