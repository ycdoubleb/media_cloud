<?php

namespace backend\modules\media_admin\controllers;

use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaAction;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaDetail;
use common\models\media\MediaTagRef;
use common\models\media\MediaTypeDetail;
use common\models\Tags;
use common\utils\DateUtil;
use common\utils\StringUtil;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\UploadedFile;


/**
 * 上传外链媒体
 * @author Administrator
 */
class MediaImportController extends Controller{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }
    
    
    /**
     * 进入批量导入界面
     */
    public function actionIndex(){
        return $this->render('index');
    }
    
    /**
     * 添加素材
     */
    public function actionCreate(){
        
        $model = new Media([
            'created_by' => \Yii::$app->user->id,
            'owner_id' => \Yii::$app->user->id,
            'is_link' => 1
        ]);
        $model->loadDefaultValues();
        $model->scenario = Media::SCENARIO_CREATE;
        $post = Yii::$app->request->post();
        
        if($model->load($post)){
            Yii::$app->response->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {   
                $is_submit = false;
                
                // 后缀名
                $model->ext = substr($model->url, strrpos($model->url, '.')+1);
                // 类型详细
                $typeDetail = MediaTypeDetail::findOne(['name' => $model->ext, 'is_del' => 0]);
                // 保存媒体类型
                $model->type_id = $typeDetail->type_id;
                // 大小
                $model->size = StringUtil::StringSizeToBytes($model->size);
                // 时长
                $model->duration = DateUtil::timeToInt($model->duration);
                // 封面图
                $model->cover_url = str_replace($model->ext, 'jpg', $model->url);
                // 属性值
                $media_attrs = ArrayHelper::getValue($post, 'Media.attribute_value');
                // 标签
                $tags = ArrayHelper::getValue($post, 'Media.tags', '');
                $media_tags = ArrayHelper::getValue($post, 'Media.media_tags');
                $tags = Tags::saveTags($tags.','.$media_tags);
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存关联的属性值
                    MediaAttValueRef::saveMediaAttValueRef($model->id, $media_attrs);
                    // 保存关联的标签
                    if(!empty($tags)){
                        MediaTagRef::saveMediaTagRef($model->id, $tags);
                    }
                    // 保存操作记录
                    MediaAction::savaMediaAction($model->id, $model->name);
                    // 保存媒体详情
                    MediaDetail::savaMediaDetail($model->id, ['content' => null]);
                }else{
                   return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, null, $model->getErrorSummary(true));
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    return new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $model->toArray());
                }
                
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->render('create', [
            'model' => $model,
            'medias' => $this->getSpreadsheet('importfile'),     //excel表的素材信息
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
        ]);
    }
   
    /**
     * 获取素材信息
     * @param type $name    filename
     */
    private function getSpreadsheet($name){
        $dataProvider = [];
        $upload = UploadedFile::getInstanceByName($name);
        if($upload != null){
            $spreadsheet = IOFactory::load($upload->tempName); // 载入excel文件
            $sheet = $spreadsheet->getActiveSheet();    // 读取第一個工作表 
            $sheetdata = $sheet->toArray(null, true, true, true);   //转换为数组
            $sheetColumns = [];
            //获取组装的工作表数据
            for ($row = 3; $row <= count($sheetdata); $row++) {
                //组装对应数组值
                foreach ($sheetdata[2] as $key => $value) {
                    if(!empty($value)){ //值非空
                        $sheetColumns[$value] = trim($sheetdata[$row][$key]);
                    }
                }
                //判断每一行是否存在空值，若存在则过滤
                if(!empty(array_filter($sheetdata[$row]))){
                    $dataProvider[] = $sheetColumns;
                }
            }
        }
        return $dataProvider;
    }
}
