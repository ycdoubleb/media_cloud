<?php

namespace backend\modules\system_admin\controllers;

use common\models\api\ApiResponse;
use common\modules\webuploader\models\searchs\UploadfileChunkSearch;
use common\modules\webuploader\models\searchs\UploadfileSearch;
use common\modules\webuploader\models\Uploadfile;
use common\modules\webuploader\models\UploadfileChunk;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;


/**
 * UploadfileController implements the CRUD actions for Uploadfile model.
 */
class UploadfileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'del-file' => ['POST'],
                    'del-chunk' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Lists all Uploadfile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;  //过滤条件
        $tabs = ArrayHelper::getValue($params, 'tabs', 'file');
        
        if($tabs == 'file'){
            $searchModel = new UploadfileSearch();
            $dataProvider = $searchModel->search($params);
        } else {
            $searchModel = new UploadfileChunkSearch();
            $dataProvider = $searchModel->search($params);
        }
        

        return $this->render('index', [
            'tabs' => $tabs,
            'filters' => $params,
            'createdBy' => $this->getUserByUploadfile(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 删除文件
     */
    public function actionDelFile()
    {
        if(Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            try {

                $file_ids = ArrayHelper::getValue(Yii::$app->request->post(), 'ids');

                if(count($file_ids) > 0){
                    Uploadfile::updateAll(['is_del' => 1, 'updated_at' => time()], ['id' => $file_ids]);
                }

                return new ApiResponse(ApiResponse::CODE_COMMON_OK);

            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
    }
    
    /**
     * 删除文件分片
     */
    public function actionDelChunk()
    {
        if(Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            try {

                $chunk_ids = ArrayHelper::getValue(Yii::$app->request->post(), 'ids');

                if(count($chunk_ids) > 0){
                    UploadfileChunk::updateAll(['is_del' => 1, 'updated_at' => time()], ['chunk_id' => $chunk_ids]);
                }

                return new ApiResponse(ApiResponse::CODE_COMMON_OK);

            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
    }
    
    protected function getUserByUploadfile()
    {
        $query = (new \yii\db\Query())
                ->select(['User.id', 'User.nickname'])
                ->from(['Uploadfile' => Uploadfile::tableName()])
                ->leftJoin(['User' => \common\models\AdminUser::tableName()], 'User.id = Uploadfile.created_by')
                ->all();
        
        return ArrayHelper::map($query, 'id', 'nickname');
    }
}