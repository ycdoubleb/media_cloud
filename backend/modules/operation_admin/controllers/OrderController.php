<?php

namespace backend\modules\operation_admin\controllers;

use backend\modules\operation_admin\searchs\OrderSearch;
use common\models\order\Order;
use common\models\order\OrderAction;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
     * 列出所有订单数据。 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'totalCount' => $results['total'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['orders'],
                'key' => 'id'
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname')
        ]);
    }

    /**
     * 显示单个订单模型。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
            'playDataProvider' => new ArrayDataProvider([
                'allModels' => $model->playApproves
            ]),
            'goodsDataProvider' => new ArrayDataProvider([
                'allModels' => $model->goods
            ]),
            'actionDataProvider' => new ArrayDataProvider([
                'allModels' => $model->orderAction
            ]),
                
        ]);
    }
   
    /**
     * 作废 订单操作
     * @param string $id
     * @return mixed
     */
    public function actionInvalid($ids)
    {
        // 所有id
        $ids = explode(',', $ids);
        // 设置选中的订单为作废
        Order::updateAll(['order_status' => Order::ORDER_STATUS_INVALID], ['id' => $ids]);
        
        return $this->redirect(['index']);
    }
    
    /**
     * 查看 素材操作
     * @param string $id
     * @return mixed
     */
    public function actionViewAction($id)
    {
       $model = OrderAction::findOne($id);        
        
        return $this->renderAjax('____view_action', [
            'model' => $model,
        ]);
    }
    
    /**
     * 根据其主键值查找订单模型。
     * 如果找不到模型，将引发404 HTTP异常。
     * @param string $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
