<?php

namespace backend\modules\system_admin\controllers;

use common\modules\webuploader\models\searchs\UploadfileChunkSearch;
use common\modules\webuploader\models\searchs\UploadfileSearch;
use common\modules\webuploader\models\Uploadfile;
use common\modules\webuploader\models\UploadfileChunk;
use Yii;
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
                    'delete' => ['POST'],
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
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 删除文件
     */
    public function actionDelFile()
    {
        $id = Yii::$app->request->post('ids');
        $file_ids = explode(',', $id);
        $models = Uploadfile::findAll(['id' => $file_ids]);

        
        
        foreach($models as $model){
            $filePath = '';
            if($model->path){
                $filePath = Yii::getAlias('@frontend/web/' . $model->path); //文件路径
            }
            //检测文件是否存在
            if(file_exists($filePath)){
                //删除实体文件
                if(@unlink($filePath)){
                    $model->is_del = 1;
                    $model->save();
                } else {
                    Yii::$app->session->setFlash('error', '删除失败！');
                }
            } else {
                $model->is_del = 1;
                $model->save();
            }
        }
    }
    
    /**
     * 删除文件分片
     */
    public function actionDelChunk()
    {
        $id = Yii::$app->request->post('ids');
        $chunk_ids = explode(',', $id);
        $models = UploadfileChunk::findAll(['chunk_id' => $chunk_ids]);

        foreach($models as $model){
            $model->is_del = 1;
            $model->save();
        }
    }
    
}