<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\Aliyun;
use common\models\api\ApiResponse;
use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaAction;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaDetail;
use common\models\media\MediaTagRef;
use common\models\media\MediaType;
use common\models\media\MediaTypeDetail;
use common\models\Tags;
use common\utils\DateUtil;
use common\utils\StringUtil;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\UploadedFile;


/**
 * 上传外链素材
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
//                    'create' => ['POST'],
                ],
            ],
        ];
    }
    
    
    /**
     * 进入批量导入界面
     */
    public function actionIndex(){
        return $this->render('index', [
            'category_id' => ArrayHelper::getValue(Yii::$app->request->queryParams, 'category_id'),   // 分库id
        ]);
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
        $bodyParams = ArrayHelper::merge(Yii::$app->request->queryParams, Yii::$app->request->post());
        $category_id = ArrayHelper::getValue($bodyParams, 'category_id');
        
        if($model->load($bodyParams)){
            Yii::$app->response->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {   
                $is_submit = false;
                // 分库id
                $model->category_id = $category_id; 
                
                // 类型详细
                $typeDetail = MediaTypeDetail::findOne(['name' => $model->ext, 'is_del' => 0]);
                if($typeDetail == null){
                    return new ApiResponse(ApiResponse::CODE_COMMON_DATA_INVALID, Yii::t('app', 'Uploaded file suffix does not exist.'));
                }
                // 保存素材类型
                $model->type_id = $typeDetail->type_id;
                // 大小
                $model->size = StringUtil::StringSizeToBytes($model->size);
                // 时长
                $model->duration = DateUtil::timeToInt($model->duration);
                // 属性值
                $media_attrs = ArrayHelper::getValue($bodyParams, 'Media.attribute_value');
                // 标签
                $tags = ArrayHelper::getValue($bodyParams, 'Media.tags', '');
                $media_tags = ArrayHelper::getValue($bodyParams, 'Media.media_tags');
                $model->tags = str_replace(['，','、'], ',', $tags.','.$media_tags);
                $tags = Tags::saveTags($model->tags);
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
                    // 保存素材详情
                    MediaDetail::savaMediaDetail($model->id, ['mts_need' => 1]);
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
            'category_id' => $category_id,
            'dirDataProvider' => $this->getAgainInstallDirsBySameLevel($category_id),
            'medias' => $this->getSpreadsheet('importfile'),     //excel表的素材信息
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId($category_id),
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
                        $ext = substr($sheetdata[$row]['B'], strrpos($sheetdata[$row]['B'], '.')+1);
                        $sheetColumns[$value] = trim($sheetdata[$row][$key]);
                        $sheetColumns['ext'] = $ext;
                    }
                }
                //判断每一行是否存在空值，若存在则过滤
                if(!empty(array_filter($sheetdata[$row]))){
                    $dataProvider[] = $sheetColumns;
                }
            }
        }
        
        $exts = ArrayHelper::getColumn($dataProvider, 'ext');
        $iconMap = $this->getMediaTypeDetailByName($exts);
        foreach ($dataProvider as &$data){
            $rand = rand(0, 9999);
            if(isset($iconMap[$data['ext']])){
                switch ($iconMap[$data['ext']]['sign']){
                    case MediaType::SIGN_VIDEO:
                        $data['thumb_url'] = str_replace($data['ext'], 'jpg', $data['url'])."?rand={$rand}";
                        break;
                    case MediaType::SIGN_IMAGE:
                        $data['thumb_url'] = str_replace($data['ext'], 'jpg', $data['url'])."?rand={$rand}";
                        break;
                    case MediaType::SIGN_AUDIO:
                        $data['thumb_url'] = $iconMap[$data['ext']]['icon_url']."?rand={$rand}";
                        break;
                    case MediaType::SIGN_DOCUMENT:
                        $data['thumb_url'] = $iconMap[$data['ext']]['icon_url']."?rand={$rand}";
                        break;
                    default:
                        $data['thumb_url'] = Aliyun::absolutePath('static/imgs/notfound.png')."?rand={$rand}";
                        break;
                }
            } else {
                $data['thumb_url'] = Aliyun::absolutePath('static/imgs/notfound.png')."?rand={$rand}";
            }
        }
        
        return $dataProvider;
    }
    
    
    /**
     * 获取素材类型拓展信息
     * 如果是转字符串，则默认返回类型拓展的mime_type
     * @param string $ext
     * @return array ArrayMap
     */
    private static function getMediaTypeDetailByName($ext)
    {
        $query = (new Query())->from(['TypeDetail' => MediaTypeDetail::tableName()]);
        // 关联属性值表
        $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = TypeDetail.type_id');
        // 查询的字段
        $query->select(['MediaType.sign', 'TypeDetail.name', 'TypeDetail.icon_url']);
        // 必要条件
        $query->andFilterWhere([
            'TypeDetail.is_del' => 0,
            'MediaType.is_del' => 0,
            'TypeDetail.name' => $ext,
        ]);
        
        $results = [];
        foreach($query->all() as $item){
            $results[$item['name']] = [
                'sign' => $item['sign'],
                'icon_url' => $item['icon_url'],
            ];
        }
        
        return $results;
    }
    
    /**
     * 重组存储目录同级的所有目录
     * @return array
     */
    protected function getAgainInstallDirsBySameLevel($category_id)
    {
        $dirDataProvider = [];
        $dirBySameLevels = Dir::getDirsBySameLevel(null, Yii::$app->user->id, $category_id, true);
        foreach ($dirBySameLevels as $dirLists) {
            foreach ($dirLists as $dir) {
                $dir['isParent'] = true;
                $dirDataProvider[] = $dir;
            }    
        }
        
        return $dirDataProvider;
    }
}
