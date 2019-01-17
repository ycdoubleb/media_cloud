<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\MediaAliyunAction;
use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaAction;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaDetail;
use common\models\media\MediaTagRef;
use common\models\media\MediaType;
use common\models\media\MediaTypeDetail;
use common\models\media\searchs\MediaSearch;
use common\models\Tags;
use common\models\Watermark;
use common\utils\DateUtil;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有媒体数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);
        $medias = $results['data']['medias']; //所有媒体数据
        $mediaTypeIds = ArrayHelper::getColumn($medias, 'type_id');        
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $medias,
                'key' => 'id'
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'iconMap' => ArrayHelper::map(MediaTypeDetail::getMediaTypeDetailByTypeId($mediaTypeIds, false), 'name', 'icon_url'),
        ]);
    }

    /**
     * 显示单个媒体模型。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
            'iconMap' => ArrayHelper::map(MediaTypeDetail::getMediaTypeDetailByTypeId($model->type_id, false), 'name', 'icon_url'),
            'attrDataProvider' => MediaAttValueRef::getMediaAttValueRefByMediaId($model->id),
            'tagsDataProvider' => ArrayHelper::getColumn($model->mediaTagRefs, 'tags.name'),
            'videoDataProvider' => new ArrayDataProvider([
                'allModels' => $model->videoUrls,
            ]),
            'actionDataProvider' => new ArrayDataProvider([
                'allModels' => $model->mediaAction,
            ]),
            
        ]);
    }

    /**
     * 创建 一个新的媒体模型。
     * 如果创建成功，返回保存的json信息。
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Media([
            'owner_id' => Yii::$app->user->id,
            'created_by' => Yii::$app->user->id
        ]);
        $model->loadDefaultValues();
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            // 类型详细
            $typeDetail = MediaTypeDetail::findOne(['name' => $model->ext, 'is_del' => 0]);
            // 保存媒体类型
            $model->type_id = $typeDetail->type_id;
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {   
                $is_submit = false;
                $data = []; // 返回的数据
                // 属性值
                $media_attrs = ArrayHelper::getValue($post, 'Media.attribute_value');
                // 标签
                $media_tags = ArrayHelper::getValue($post, 'Media.tag_ids');
                $tags = Tags::saveTags($media_tags);
                // 转码需求
                $mts_need = ArrayHelper::getValue($post, 'Media.mts_need');
                // 水印id
                $wate_ids = implode(',', ArrayHelper::getValue($post, 'Media.mts_watermark_ids' , []));
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存关联的属性值
                    MediaAttValueRef::saveMediaAttValueRef($model->id, $media_attrs);
                    // 保存关联的标签
                    MediaTagRef::saveMediaTagRef($model->id, $tags);
                    // 保存操作记录
                    MediaAction::savaMediaAction($model->id, $model->name);
                    /** 保存水印图： 1媒体类型是视频,2自动转码 */
                    if($model->mediaType->sign == MediaType::SIGN_VIDEO && $mts_need){
                        // 保存媒体详情
                        MediaDetail::savaMediaDetail($model->id, ['mts_need' => $mts_need, 'mts_watermark_ids' => $wate_ids]);
                    }
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
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'mimeTypes' => MediaTypeDetail::getMediaTypeDetailByTypeId(),
            'wateFiles' => Watermark::getEnabledWatermarks(),
            'wateSelected' => [],
        ]);
    }

    /**
     * 编辑 媒体基本信息
     * 如果更新成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionEditBasic($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                $is_submit = false;
                //获取所有新属性值
                $newAttributes = $model->getDirtyAttributes();
                //获取所有旧属性值
                $oldAttributes = $model->getOldAttributes();  
                // 媒体内容
                $content = ArrayHelper::getValue($post, 'Media.content');

                // 若发生修改则返回修改后的属性
                $dataProvider = [
                    '存储目录' => isset($newAttributes['dir_id']) && $newAttributes['dir_id'] != $oldAttributes['dir_id'] ? $model->dir->getFullPath() : null,
                    '媒体名称' => isset($newAttributes['name']) && $newAttributes['name'] != $oldAttributes['name'] ? $model->name : null,
                    '价格' => isset($newAttributes['price']) && $newAttributes['price'] != $oldAttributes['price'] ? Yii::$app->formatter->asCurrency($model->price) : null,
                    '内容' => !empty($model->detail) && $model->detail->content != $content ? $content : null
                ];
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存媒体详情
                    MediaDetail::savaMediaDetail($model->id, ['content' => $content]);
                    // 保存操作记录
                    MediaAction::savaMediaAction($model->id,  empty(array_filter($dataProvider)) ? '无' :  
                        $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    Yii::$app->getSession()->setFlash('success','操作成功！');
                }
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->renderAjax('____edit_basic', [
            'model' => $model
        ]);
    }
    
    /**
     * 批量编辑 媒体属性标签
     * @param string $id
     * @return mixed
     */
    public function actionBatchEditAttribute()
    {
        return $this->renderAjax('____edit_attribute', [
            'ids' => explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id')),    // 所有媒体id
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'attrSelected' => [],
            'tagsSelected' => [],
        ]);
    }
    
    /**
     * 编辑 媒体属性标签
     * 如果更新成功，浏览器将被重定向到“当前页”页面。
     * @param string $id
     * @return mixed
     */
    public function actionEditAttribute($id)
    {       
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            try
            {
                // 属性值
                $media_attrs = ArrayHelper::getValue($post, 'Media.attribute_value');
                // 标签
                $media_tags = ArrayHelper::getValue($post, 'Media.tag_ids');
                // 保存标签
                $tags = Tags::saveTags($media_tags);
                
                // 保存属性关联
                MediaAttValueRef::saveMediaAttValueRef($model->id, $media_attrs);
                // 保存标签关联
                MediaTagRef::saveMediaTagRef($model->id, $tags);

                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____edit_attribute', [
            'ids' => explode(',', $id),
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'attrSelected' => ArrayHelper::map(MediaAttValueRef::getMediaAttValueRefByMediaId($model->id), 'attr_id', 'attr_value_id'),
            'tagsSelected' => ArrayHelper::getColumn($model->mediaTagRefs, 'tags.name'),
        ]);
    }
    
    /**
     * 重新 上传媒体文件
     * 如果更新成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionAnewUpload($id)
    {
        $model = $this->findModel($id);       
        
        if ($model->load(Yii::$app->request->post())) {
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                $is_submit = false;
                //获取所有新属性值
                $newAttributes = $model->getDirtyAttributes();
                //获取所有旧属性值
                $oldAttributes = $model->getOldAttributes();  

                // 若发生修改则返回修改后的属性
                $dataProvider = [
                    '媒体类型' => isset($newAttributes['type_id']) && $newAttributes['type_id'] != $oldAttributes['type_id'] ? $model->mediaType->name : null,
                    '存储目录' => isset($newAttributes['dir_id']) && $newAttributes['dir_id'] != $oldAttributes['dir_id'] ? $model->dir->getFullPath() : null,
                    '媒体文件' => isset($newAttributes['file_id']) && $newAttributes['file_id'] != $oldAttributes['file_id'] ? $model->uploadfile->name : null,
                    '媒体名称' => isset($newAttributes['name']) && $newAttributes['name'] != $oldAttributes['name'] ? $model->name : null,
                    '封面路径' => isset($newAttributes['cover_url']) && $newAttributes['cover_url'] != $oldAttributes['cover_url'] ? $model->cover_url : null,
                    '原始路径' => isset($newAttributes['url']) && $newAttributes['url'] != $oldAttributes['url'] ? $model->url : null,
                    '时长' => isset($newAttributes['duration']) && $newAttributes['duration'] != $oldAttributes['duration'] ? DateUtil::timeToInt($model->duration) : null,
                    '大小' => isset($newAttributes['size']) && $newAttributes['size'] != $oldAttributes['size'] ? Yii::$app->formatter->asShortSize($model->size) : null,
                ];
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存操作记录
                    MediaAction::savaMediaAction($model->id,  empty(array_filter($dataProvider)) ? '无' :  
                        $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    Yii::$app->getSession()->setFlash('success','操作成功！');
                }
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->renderAjax('____anew_upload', [
            'model' => $model,
//            'mediaFiles' => $model->uploadfile->toArray(),
            'mimeTypes' => MediaTypeDetail::getMediaTypeDetailByTypeId($model->type_id),
        ]);
    }
    
    /**
     * 重新 转码视频文件
     * 如果更新成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionAnewTranscoding($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            try
            {
                // 水印id
                $wate_ids = implode(',', ArrayHelper::getValue($post, 'Media.mts_watermark_ids'));
                // 保存媒体详细
                MediaDetail::savaMediaDetail($model->id, ['mts_watermark_ids' => $wate_ids]);
                // 转码
                MediaAliyunAction::addVideoTranscode($model->id);   
                // 保存操作记录
                MediaAction::savaMediaAction($model->id,  '重新转码', '修改');
                
                Yii::$app->getSession()->setFlash('success','操作成功！');
                
            } catch (Exception $ex) {
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->renderAjax('____anew_transcoding', [
            'model' => $model,
            'wateFiles' => Watermark::getEnabledWatermarks(),
            'wateSelected' => explode(',', $model->detail->mts_watermark_ids)
        ]);
    }
    
    /**
     * 查看 媒体操作
     * @param string $id
     * @return mixed
     */
    public function actionViewAction($id)
    {
       $model = MediaAction::findOne($id);        
        
        return $this->renderAjax('____view_action', [
            'model' => $model,
        ]);
    }

    /**
     * 根据其主键值查找媒体模型。
     * 如果找不到模型，将引发404 HTTP异常。
     * @param string $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne(['id' => $id, 'del_status' => 0])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
