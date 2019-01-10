<?php

namespace backend\modules\operation_admin\controllers;

use backend\modules\operation_admin\searchs\PlayApproveSearch;
use common\models\order\PlayApprove;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderApproveController implements the CRUD actions for PlayApprove model.
 */
class OrderApproveController extends Controller
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
                    'pass-approve' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有PlayApve模型。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlayApproveSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['approves']
            ]),
            'createdByMap' => ArrayHelper::map($results['data']['createdBys'], 'id', 'nickname'),
            'handledByMap' => ArrayHelper::map($results['data']['handledBys'], 'id', 'nickname')
        ]);
    }

    /**
     * 显示单个PlayApve模型。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * 通过申请
     * 如果成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionPassApprove($id)
    {
        $model = $this->findModel($id);
        
        if($model->status == PlayApprove::STATUS_TO_BE_AUDITED){
        
            $model->status = PlayApprove::STATUS_AUDITED;
            $model->result = PlayApprove::RESULT_PASS;
            $model->handled_by = \Yii::$app->user->id;
            $model->handled_at = time();
            $model->feedback = '审核通过';

            $model->save();

            Yii::$app->getSession()->setFlash('success','操作成功！');
        }else{
            Yii::$app->getSession()->setFlash('error','该订单审核已经处理过，请勿重复提交处理。');     
        }
        
        return $this->redirect(['view', 'id' => $model->id]);
    }
    
    /**
     * 不通过申请
     * 如果成功，浏览器将被重定向到“view”页面。
     * @param string $id
     * @return mixed
     */
    public function actionNotApprove($id)
    {
        $model = $this->findModel($id);
        
        if(\Yii::$app->request->isPost){
            if($model->status == PlayApprove::STATUS_TO_BE_AUDITED){
                // 反馈信息
                $feedback = ArrayHelper::getValue(Yii::$app->request->post(), 'PlayApprove.feedback'); 

                $model->status = PlayApprove::STATUS_AUDITED;
                $model->result = PlayApprove::RESULT_NOT_PASS;
                $model->handled_by = \Yii::$app->user->id;
                $model->handled_at = time();
                $model->feedback = $feedback;

                $model->save();

                Yii::$app->getSession()->setFlash('success','操作成功！');                
            }else{
                Yii::$app->getSession()->setFlash('error','该订单审核已经处理过，请勿重复提交处理。');     
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
                
        return $this->renderAjax('approve');
    }
    
    /**
     * 根据其主键值查找PlayApve模型。
     * 如果找不到模型，将引发404 HTTP异常。
     * @param string $id
     * @return PlayApprove the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlayApprove::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
