<?php

namespace common\models\media;

use common\models\AdminUser;
use common\modules\webuploader\models\Uploadfile;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "{{%media}}".
 *
 * @property string $id
 * @property string $category_id    媒体的所属类目ID，关联media_category表id字段
 * @property string $type_id        媒体类型，关联media_type表id字段
 * @property string $owner_id       当前所有者id，关联admin_user表id字段
 * @property string $dir_id         存储目录id，关联media_dir表id字段
 * @property string $file_id        上传文件id，关联uploadfile表id字段
 * @property string $name           媒体名称
 * @property string $cover_url      封面路径
 * @property string $url            原始路径
 * @property string $price          价格
 * @property string $duration       时长
 * @property string $size           大小(字节 b)
 * @property string $ext            拓展名/后缀名
 * @property int $status            普通状态 1待入库 2已入库 3已发布
 * @property int $mts_status        转码状态 0无转码 1未转码 2转码中 3已转码 4转码失败
 * @property int $del_status        删除状态 0正常 1申请删除 2逻辑删除 3物理删除
 * @property int $is_link           是否联接地址 0否 1是
 * @property string $created_by 创建人id，关联admin_user表id字段
 * @property string $updated_by 最后最新人id，关联admin_user表id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property AliyunMtsService[] $aliyunMtsServices
 * @property Dir $dir
 * @property MediaType $mediaType
 * @property MediaDetail $detail
 * @property MediaTagRef[] $mediaTagRefs
 * @property Uploadfile $uploadfile
 * @property AdminUser $owner
 * @property AdminUser $createdBy
 * @property VideoUrl[] $videoUrls
 * @property MediaAction[] $mediaAction
 */
class Media extends ActiveRecord
{
    
    /** 待入库 */
    const STATUS_WAIT_INTO_DB = 1;

    /** 已入库 */
    const STATUS_ALREADY_INTO_DB = 2;

    /** 已发布 */
    const STATUS_ALREADY_PUBLISH = 3;
    
    /** 无转码 */
    const MTS_STATUS_NONE = 0;
    /** 无转码 */
    const MTS_STATUS_NO = 1;
    /** 转码中 */
    const MTS_STATUS_DOING = 2;
    /** 已转码 */
    const MTS_STATUS_YES = 3;
    /** 转码失败 */
    const MTS_STATUS_FAIL = 4;
    
    /** 删除状态-申请 */
    const DEL_STATUS_APPROVE = 1;

    /** 删除状态-逻辑 */
    const DEL_STATUS_LOGIC = 2;

    /** 删除状态-物理 */
    const DEL_STATUS_TRUE = 3;
    
    /**
     * 状态名
     * @var array 
     */
    public static $statusName = [
        self::STATUS_WAIT_INTO_DB => '待入库',
        self::STATUS_ALREADY_INTO_DB => '已入库',
        self::STATUS_ALREADY_PUBLISH => '已发布',
    ];
    
    /**
     * 转码状态名
     * @var array 
     */
    public static $mtsStatusName = [
        self::MTS_STATUS_NONE => '无转码',
        self::MTS_STATUS_NO => '未转码',
        self::MTS_STATUS_DOING => '转码中',
        self::MTS_STATUS_YES => '已转码',
        self::MTS_STATUS_FAIL => '转码失败',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media}}';
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
            [['category_id', 'type_id', 'owner_id', 'dir_id', 'file_id', 'size', 'status', 'mts_status', 'del_status','is_link', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
//            [['type_id'], 'required'],
            [['price', 'duration'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['ext'], 'string', 'max' => 10],
            [['cover_url', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'type_id' => Yii::t('app', 'Type ID'),
            'owner_id' => Yii::t('app', 'Owner ID'),
            'dir_id' => Yii::t('app', 'Dir ID'),
            'file_id' => Yii::t('app', 'File ID'),
            'name' => Yii::t('app', 'Name'),
            'cover_url' => Yii::t('app', 'Cover Url'),
            'url' => Yii::t('app', 'Url'),
            'price' => Yii::t('app', 'Price'),
            'duration' => Yii::t('app', 'Duration'),
            'size' => Yii::t('app', 'Size'),
            'status' => Yii::t('app', 'Status'),
            'mts_status' => Yii::t('app', 'Mts Status'),
            'del_status' => Yii::t('app', 'Del Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAliyunMtsServices()
    {
        return $this->hasMany(AliyunMtsService::className(), ['media_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDir()
    {
        return $this->hasOne(Dir::className(), ['id' => 'dir_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMediaType()
    {
        return $this->hasOne(MediaType::className(), ['id' => 'type_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getDetail()
    {
        return $this->hasOne(MediaDetail::className(), ['media_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMediaTagRefs()
    {
        return $this->hasMany(MediaTagRef::className(), ['object_id' => 'id'])
            ->where(['is_del' => 0])->with('tags');
    }

    /**
     * @return ActiveQuery
     */
    public function getUploadfile()
    {
        return $this->hasOne(Uploadfile::className(), ['id' => 'file_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'owner_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getVideoUrls()
    {
        return $this->hasMany(VideoUrl::className(), ['media_id' => 'id'])
           ->where(['media_id' => $this->id]);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMediaAction()
    {
        return $this->hasMany(MediaAction::className(), ['media_id' => 'id'])
           ->where(['media_id' => $this->id]);
    }
}
