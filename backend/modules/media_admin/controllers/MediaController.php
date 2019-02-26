<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\MediaAliyunAction;
use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaAction;
use common\models\media\MediaApprove;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaDetail;
use common\models\media\MediaTagRef;
use common\models\media\MediaType;
use common\models\media\MediaTypeDetail;
use common\models\media\searchs\MediaSearch;
use common\models\Tags;
use common\models\Watermark;
use common\widgets\grid\GridViewChangeSelfController;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends GridViewChangeSelfController
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
     * 列出所有素材数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch(['type_id' => array_keys(MediaType::getMediaByType())]);
        $results = $searchModel->search(Yii::$app->request->queryParams);
        $medias = $results['data']['medias']; //所有素材数据
        $mediaTypeIds = ArrayHelper::getColumn($medias, 'type_id');        
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'totalCount' => $results['total'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $medias,
                'key' => 'id',
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'iconMap' => ArrayHelper::map(MediaTypeDetail::getMediaTypeDetailByTypeId($mediaTypeIds, false), 'name', 'icon_url'),
        ]);
    }
    
    /**
     * 列出所有素材数据。
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new MediaSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);
        $medias = $results['data']['medias']; //所有素材数据
        $mediaTypeIds = ArrayHelper::getColumn($medias, 'type_id');      
        
        return $this->renderAjax('____media_table_dom', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $medias,
                'key' => 'id',
            ]),
            'iconMap' => ArrayHelper::map(MediaTypeDetail::getMediaTypeDetailByTypeId($mediaTypeIds, false), 'name', 'icon_url'),
        ]);
    }

    /**
     * 显示单个素材模型。
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
            'attrDataProvider' => MediaAttValueRef::getMediaAttValueRefByMediaId($model->id, false),
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
     * 创建 一个新的素材模型。
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
        $model->scenario = Media::SCENARIO_CREATE;
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {   
                $is_submit = false;
                
                // 类型详细
                $typeDetail = MediaTypeDetail::findOne(['name' => $model->ext, 'is_del' => 0]);
                if($typeDetail == null){
                    return new ApiResponse(ApiResponse::CODE_COMMON_DATA_INVALID, '上传的文件后缀不存在');
                }
                // 保存素材类型
                $model->type_id = $typeDetail->type_id;
                // 属性值
                $media_attrs = ArrayHelper::getValue($post, 'Media.attribute_value');
                // 标签
                $media_tags = ArrayHelper::getValue($post, 'Media.tags');
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
                    if(!empty($tags)){
                        MediaTagRef::saveMediaTagRef($model->id, $tags);
                    }
                    // 保存操作记录
                    MediaAction::savaMediaAction($model->id, $model->name);
                    // 保存素材详情
                    MediaDetail::savaMediaDetail($model->id, ['mts_need' => $mts_need, 'mts_watermark_ids' => $wate_ids]);
                    // 保存素材审核
                    MediaApprove::savaMediaApprove($model->id, '系统自动创建入库申请', MediaApprove::TYPE_INTODB_APPROVE);
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
            'isTagRequired' => false,     // 判断标签是否需要必须
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'mimeTypes' => MediaTypeDetail::getMediaTypeDetailByTypeId(),
            'wateFiles' => Watermark::getEnabledWatermarks(),
            'wateSelected' => [],
        ]);
    }
    
    /**
     * 批量编辑 素材价格
     * @param string $id
     * @return mixed
     */
    public function actionBatchEditPrice()
    {
        return $this->renderAjax('____edit_price', [
            'ids' => json_encode(explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'))),    // 所有素材id
        ]);
    }
    
    /**
     * 编辑 素材基本信息
     * 如果更新成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionEditBasic($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->scenario = Media::SCENARIO_UPDATE;
        
        if ($model->load($post)) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                $is_submit = false;
                //获取所有新属性值
                $newAttributes = $model->getDirtyAttributes();
                //获取所有旧属性值
                $oldAttributes = $model->getOldAttributes();  
                // 素材内容
                $content = ArrayHelper::getValue($post, 'Media.content');

                // 若发生修改则返回修改后的属性
                $dataProvider = [
                    '存储目录' => isset($newAttributes['dir_id']) && $newAttributes['dir_id'] != $oldAttributes['dir_id'] ? $model->dir->getFullPath() : null,
                    '素材名称' => isset($newAttributes['name']) && $newAttributes['name'] != $oldAttributes['name'] ? $model->name : null,
                    '价格' => isset($newAttributes['price']) && $newAttributes['price'] != $oldAttributes['price'] ? Yii::$app->formatter->asCurrency($model->price) : null,
                ];
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存素材详情
                    MediaDetail::savaMediaDetail($model->id, ['content' => $content]);
                    // 保存操作记录
                    if(!empty(array_filter($dataProvider))){
                        MediaAction::savaMediaAction($model->id,  $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                    }
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    return new ApiResponse(ApiResponse::CODE_COMMON_OK, '操作成功');
                }
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }            
        }
        
        return $this->renderAjax('____edit_basic', [
            'model' => $model,
            'ids' => json_encode(explode(',', $id)),
        ]);
    }
    
    /**
     * 批量编辑 素材属性标签
     * @param string $id
     * @return mixed
     */
    public function actionBatchEditAttribute()
    {
        return $this->renderAjax('____edit_attribute', [
            'ids' => json_encode(explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'))),    // 所有素材id
            'isTagRequired' => false,  // 判断标签是否需要必须
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
        ]);
    }
    
    /**
     * 编辑 素材属性标签
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
                $model->tags = ArrayHelper::getValue($post, 'Media.tags');
                // 保存标签
                $tags = Tags::saveTags($model->tags);
                
                if($model->save()){
                    // 保存属性关联
                    MediaAttValueRef::saveMediaAttValueRef($model->id, $media_attrs);
                    // 保存标签关联
                    MediaTagRef::saveMediaTagRef($model->id, $tags);
                }
                
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '操作成功');
                
            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____edit_attribute', [
            'ids' => json_encode(explode(',', $id)),
            'isTagRequired' => true,     // 判断标签是否需要必须
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
            'attrSelected' => MediaAttValueRef::getMediaAttValueRefByMediaId($model->id),
            'tagsSelected' => $model->tags,
        ]);
    }
    
    /**
     * 重新 上传素材文件
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
                    '素材名称' => isset($newAttributes['name']) && $newAttributes['name'] != $oldAttributes['name'] ? $model->name : null,
                ];
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 保存操作记录
                    if(!empty(array_filter($dataProvider))){
                        MediaAction::savaMediaAction($model->id, $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                    }
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
        
        if (Yii::$app->request->isPost) {
            try
            {
                // 水印id
                $wate_ids = implode(',', ArrayHelper::getValue($post, 'Media.mts_watermark_ids', []));
                // 保存素材详细
                MediaDetail::savaMediaDetail($model->id, ['mts_watermark_ids' => $wate_ids]);
                // 转码
                MediaAliyunAction::addVideoTranscode($model->id, true);   
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
     * 更新标签
     * @param type $id          id
     * @param type $fieldName   字段名
     * @param type $value       新值
     */
    public function actionChangeTags($id,$fieldName,$value){
        Yii::$app->getResponse()->format = 'json';
        try
        {
            $model = $this->findModel($id);
            $model[$fieldName] = $value;
            if($model->validate(false) && $model->save()){
                // 标签
                $tags = Tags::saveTags($value);
                // 保存关联的标签
                if(!empty($tags)){
                    MediaTagRef::saveMediaTagRef($id, $tags);
                }
            }
        } catch (Exception $ex) {
            return ['result' => 0,'message' => sprintf("%s $fieldName = $value %s！%s", Yii::t('app', 'Update'),  Yii::t('app', 'Fail'), $ex->getMessage())];
        }
        
        return ['result' => 1,'message' => sprintf('%s%s', Yii::t('app', 'Update'),  Yii::t('app', 'Success'))];
    }
    
    /**
     * 查看 素材操作
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
     * 根据其主键值查找素材模型。
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
