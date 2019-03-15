<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\MediaAliyunAction;
use common\models\api\ApiResponse;
use common\models\media\Acl;
use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaAction;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttributeValue;
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
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends GridViewChangeSelfController
{
    public $enableCsrfValidation = false;
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['tran-complete'],
                'rules' => [
                    [
                        'actions' => ['tran-complete'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'tran-complete' => ['POST'],
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
            'filters' => $results['filter'],     //查询过滤
            'totalCount' => $results['total'],     //计算总数量
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $medias,
                'key' => 'id',
            ]),
            'dirDataProvider' => $this->getAgainInstallDirsBySameLevel(),
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
        ]);
    }
    
    /**
     * 显示属性标签。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionTagsadmin($id)
    {
        $model = $this->findModel($id);
        
        return $this->renderAjax('____tags_admin', [
            'model' => $model,
            'attrDataProvider' => MediaAttValueRef::getMediaAttValueRefByMediaId($model->id, false),
        ]);
    }
    
    /**
     * 显示属性标签。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        
        return $this->renderAjax('____media_ preview', [
            'model' => $model,
            'videoDataProvider' => new ArrayDataProvider([
                'allModels' => $model->videoUrls,
            ]),
        ]);
    }
    
    /**
     * 显示操作记录。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionOperatelogList($id)
    {
        $model = $this->findModel($id);
        
        return $this->renderAjax('____list_operate_log', [
            'model' => $model,
            'actionDataProvider' => new ArrayDataProvider([
                'allModels' => $model->mediaAction,
            ]),
        ]);
    }
    
    /**
     * 查看 素材操作
     * @param string $id
     * @return mixed
     */
    public function actionOperatelogView($id)
    {
       $model = MediaAction::findOne($id);        
        
        return $this->renderAjax('____view_operate_log', [
            'model' => $model,
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
            Yii::$app->response->format = 'json';
            
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
                $model->tags = ArrayHelper::getValue($post, 'Media.tags');
                $tags = Tags::saveTags($model->tags);
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
            'dirDataProvider' => $this->getAgainInstallDirsBySameLevel(),
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
            Yii::$app->response->format = 'json';
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
                    '存储目录' => isset($newAttributes['dir_id']) ? Dir::getDirById($oldAttributes['dir_id'])->getFullPath() : null,
                    '素材名称' => isset($newAttributes['name']) ? $oldAttributes['name']: null,
                    '价格' => isset($newAttributes['price']) ? Yii::$app->formatter->asCurrency($oldAttributes['price']) : null,
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
            Yii::$app->response->format = 'json';
            try
            {
                // 旧属性值
                $oldAttValues = '';
                // 属性值
                $media_attrs = ArrayHelper::getValue($post, 'Media.attribute_value');
                // 标签
                $model->tags = ArrayHelper::getValue($post, 'Media.tags');
                // 保存标签
                $tags = Tags::saveTags($model->tags);
                //获取所有新属性值
                $newAttributes = $model->getDirtyAttributes();
                //获取所有旧属性值
                $oldAttributes = $model->getOldAttributes();  
                
                // 获取旧属性值
                $oldMediaAttVals = $this->getOldMediaAttributeValue($model->id, array_keys($media_attrs));
                
                // 把素材属性转换成字符串
                foreach ($oldMediaAttVals as $att_name => $att_values){
                    $att_value = implode('、', $att_values);
                    $oldAttValues .= "{$att_name}:{$att_value}；";
                }
                
                // 若发生修改则返回修改后的属性
                $dataProvider = [
                    '素材属性' => !empty($oldAttValues) ? $oldAttValues : null,
                    '素材标签' => isset($newAttributes['tags']) ? $oldAttributes['tags'] : null,
                ];
                
                if($model->save()){
                    // 保存属性关联
                    MediaAttValueRef::saveMediaAttValueRef($model->id, $media_attrs);
                    // 保存标签关联
                    MediaTagRef::saveMediaTagRef($model->id, $tags);
                    // 保存操作记录
                    if(!empty(array_filter($dataProvider))){
                        MediaAction::savaMediaAction($model->id,  $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                    }
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
                $need_tran = false;
                $is_submit = false;
                $model->is_link = 0;    // 都设置为非外链
                //获取所有新属性值
                $newAttributes = $model->getDirtyAttributes();
                //获取所有旧属性值
                $oldAttributes = $model->getOldAttributes();  

                // 若发生修改则返回修改后的属性
                $dataProvider = [
                    '素材名称' => isset($newAttributes['name']) ? $oldAttributes['name'] : null,
                ];
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                    // 设置素材类型是视频并且是自动转码时，才调用转码需求
                    if($model->mediaType->sign == MediaType::SIGN_VIDEO){
                        // 如果视频转码需求是自动则转码
                        if($model->detail->mts_need && $model->status == Media::STATUS_PUBLISHED){
                            $need_tran = true;
                        }
                    }
                    // 保存操作记录
                    if(!empty(array_filter($dataProvider))){
                        MediaAction::savaMediaAction($model->id, $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                    }
                    // 更新Acl
                    Acl::updateAcl($model->id, 0);
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    if($need_tran){
                        MediaAliyunAction::addVideoTranscode($model->id, true, '/media_admin/media/tran-complete');   // 转码
                    }
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
        
        if (Yii::$app->request->isPost && $model->status == Media::STATUS_PUBLISHED) {
            try
            {
                // 水印id
                $wate_ids = implode(',', ArrayHelper::getValue(Yii::$app->request->post(), 'Media.mts_watermark_ids', []));
                // 保存素材详细
                MediaDetail::savaMediaDetail($model->id, ['mts_watermark_ids' => $wate_ids]);
                // 转码
                MediaAliyunAction::addVideoTranscode($model->id, true, '/media_admin/media/tran-complete');   
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
     * 转码完成
     * @param type $id
     * @return $post [
        * code : 0 成功，其它失败
        * data : 成功时为{media_id:xx}，失败时为失败详情{error:xxx}
        * msg  : 提示信息
     * ]
     */
    public function actionTranComplete(){
        $post = array_merge([],Yii::$app->getRequest()->post(), json_decode(Yii::$app->getRequest()->getRawBody(),true));
        
        if(isset($post['code']) && $post['code'] == 0){
            $model = $this->findModel($post['data']['media_id']);
            $model->status = Media::STATUS_PUBLISHED;
            if($model->save(true, ['status'])){
                Acl::updateAcl($model->id);  // 更新Acl
            }
        }
        exit;
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
            //获取所有新属性值
            $newAttributes = $model->getDirtyAttributes();
            //获取所有旧属性值
            $oldAttributes = $model->getOldAttributes();  

            // 若发生修改则返回修改后的属性
            $dataProvider = [
                '素材标签' => isset($newAttributes['tags']) ? $oldAttributes['tags'] : null,
            ];
            
            if($model->validate(false) && $model->save()){
                // 标签
                $tags = Tags::saveTags($value);
                // 保存关联的标签
                if(!empty($tags)){
                    MediaTagRef::saveMediaTagRef($id, $tags);
                }
                // 保存操作记录
                if(!empty(array_filter($dataProvider))){
                    MediaAction::savaMediaAction($model->id,  $this->renderPartial("____media_update_dom", ['dataProvider' => array_filter($dataProvider)]), '修改');
                }
            }
        } catch (Exception $ex) {
            return ['result' => 0,'message' => sprintf("%s $fieldName = $value %s！%s", Yii::t('app', 'Update'),  Yii::t('app', 'Fail'), $ex->getMessage())];
        }
        
        return ['result' => 1,'message' => sprintf('%s%s', Yii::t('app', 'Update'),  Yii::t('app', 'Success'))];
    }
    
    /**
     * 检查素材转码情况
     * @param string $id
     * @return mixed
     */
    public function actionCheckTranscode($id)
    {
        Yii::$app->getResponse()->format = 'json';
        
        try
        {
            $model = $this->findModel($id);
            if($model->mts_status == Media::MTS_STATUS_YES){
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '转码成功', $model->toArray());
            }else if($model->mts_status == Media::MTS_STATUS_FAIL){
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '转码失败', $model->toArray());
            }
        } catch (Exception $ex) {
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, $ex->getMessage(), $ex->getTraceAsString());
        }
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
    
    /**
     * 获取素材属性旧值
     * @param int $media_id
     * @param int $att_ids
     * @return array
     */
    protected function getOldMediaAttributeValue($media_id, $att_ids)
    {
        $query = (new Query())->select(['Attribute.name', 'AttributeValue.value'])
            ->from(['AttValueRef' => MediaAttValueRef::tableName()]);
        $query->leftJoin(['Attribute' => MediaAttribute::tableName()], 'Attribute.id = AttValueRef.attribute_id');
        $query->leftJoin(['AttributeValue' => MediaAttributeValue::tableName()], 'AttributeValue.id = AttValueRef.attribute_value_id');
        $query->where(['AttValueRef.media_id' => $media_id, 'AttValueRef.attribute_id' => $att_ids, 'AttValueRef.is_del' => 0]);
        $results = '';
        foreach ($query->all() as $item){
            $results[$item['name']][] = $item['value'];
        }
        
        return $results;
    }
    
    /**
     * 重组存储目录同级的所有目录
     * @return array
     */
    protected function getAgainInstallDirsBySameLevel()
    {
        $dirDataProvider = [];
        $dirBySameLevels = Dir::getDirsBySameLevel(null, Yii::$app->user->id, true);
        foreach ($dirBySameLevels as $dirLists) {
            foreach ($dirLists as $index => $dir) {
                $dir['isParent'] = true;
                $dirDataProvider[] = $dir;
            }    
        }
        
        return $dirDataProvider;
    }
}
