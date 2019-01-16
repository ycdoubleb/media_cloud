<?php

namespace common\models\media;

use common\models\AdminUser;
use common\models\media\Acl;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;


/**
 * This is the model class for table "{{%acl_action}}".
 *
 * @property string $id
 * @property string $acl_id 关联acl表id字段
 * @property string $title 操作标题/类型
 * @property string $content 操作内容
 * @property string $created_by 操作人id,关联mc_admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property Acl $acl
 * @property AdminUser $createdBy
 */
class AclAction extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%acl_action}}';
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
            [['acl_id'], 'required'],
            [['acl_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 20],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'acl_id' => Yii::t('app', 'Acl ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getAcl()
    {
        return $this->hasOne(Acl::className(), ['id' => 'acl_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'created_by']);
    }
    
    /**
     * 保存Acl操作日志
     * @param array $acl_ids
     * @param string $title 标题
     * @param string $content   内容（字符串）| 加载渲染的模板
     * @throws Exception
     */
    public static function savaAclAction($acl_ids, $title, $content)
    {
        try
        {  
            // 准备数据
            $rows = [];
            foreach ($acl_ids as $id) {
                $rows[] = ['acl_id' => $id, 'title' => $title, 'content' => $content, 
                    'created_by' => Yii::$app->user->id, 'created_at' => time(), 
                    'updated_at' => time()
                ];
            }
            // 批量插入
            Yii::$app->db->createCommand()->batchInsert(self::tableName(), array_keys($rows[0]), array_values($rows))->execute();
            
        }catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
