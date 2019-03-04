<?php

namespace frontend\modules\order_admin\controllers;

use common\models\api\ApiResponse;
use common\models\order\Cart;
use common\models\order\Order;
use common\models\order\OrderAction;
use common\models\order\OrderGoods;
use common\models\order\PlayApprove;
use common\models\order\searchs\CartSearch;
use common\models\UserProfile;
use frontend\modules\order_admin\utils\ExportUtils;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CartController implements the CRUD actions for Cart model.
 */
class CartController extends Controller
{
    /**
     * {@inheritdoc}
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Cart models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CartSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);
        
        $sel_num = 0;
        $total_price = 0;
        // 计算选中素材的总数和价格
        foreach ($dataProvider->models as $data){
            if($data['is_selected'] == 1){
                $sel_num += $data['num'];
                $total_price += $data['price'] * $data['num'];
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalCount' => count($dataProvider->models),   // 总数
            'sel_num' => $sel_num,      // 选中数量
            'total_price' => $total_price,  // 选中的素材总价
        ]);
    }
    
    /**
     * 把选中的素材移出购物车
     * @return mixed
     */
    public function actionDelMedia()
    {
        try{
            $models = Cart::findAll(['is_selected' => 1, 'is_del' => 0, 'created_by' => Yii::$app->user->id]);
            foreach($models as $model){
                $model->is_del = 1;
                $model->save();
            }
            Yii::$app->getSession()->setFlash('success', '移出购物车成功！');
        } catch (Exception $e) {
            Yii::$app->getSession()->setFlash('error', '移出购物车失败！失败原因：'.$e->getMessage());
        }
        return $this->redirect('index');
    }

    /**
     * 全选or全不选
     * @return mixed
     */
    public function actionChangeAll()
    {
        Yii::$app->getResponse()->format = 'json';
        
        $checked = Yii::$app->request->post('checked');
        $models = Cart::findAll(['is_del' => 0, 'created_by' => Yii::$app->user->id]);

        if($checked == 'true'){
            foreach($models as $model){
                $model->is_selected = 1;
                $model->save();
            }
            $is_select = true;
        } else {
            foreach($models as $model){
                $model->is_selected = 0;
                $model->save();
            }
            $is_select = false;
        }
        
        return new ApiResponse(ApiResponse::CODE_COMMON_OK, null, $is_select);
        
    }
    
    /**
     * 修改单个素材选中状态
     * @return mixed
     */
    public function actionChangeOne()
    {   
        Yii::$app->getResponse()->format = 'json';
    
        $id = Yii::$app->request->post('id');
        $model = Cart::findOne(['goods_id' => $id, 'is_del' => 0, 'created_by' => Yii::$app->user->id]);
        if($model->is_selected == 1){
            $model->is_selected = 0;
        } else {
            $model->is_selected = 1;
        }
        
        if($model->save()){
            //所有素材
            $cats = Cart::findAll(['is_del' => 0, 'created_by' => Yii::$app->user->id]);
            //选中的素材
            $cats_sels = Cart::findAll(['is_selected' => 1, 'is_del' => 0, 'created_by' => Yii::$app->user->id]);

            return new ApiResponse(ApiResponse::CODE_COMMON_OK, null, count($cats) == count($cats_sels));
        }
    }

    /**
     * 核对订单 / 提交订单后跳转到下单成功页面
     * @return mixed
     */
    public function actionCheckingOrder()
    {
        // 使用主布局main的布局样式
        $this->layout = '@app/views/layouts/main';
        
        $order_sn = date('YmdHis',time()) . rand(1000, 9999);
        $searchModel = new CartSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        if(empty($dataProvider->models)){            
            return $this->redirect('index');
        }
        $sel_num = 0;
        $total_price = 0;
        // 计算选中素材的总数和价格
        foreach ($dataProvider->models as $data){
            if($data['is_selected'] == 1){
                $sel_num ++;
                $total_price += $data['price'] * $data['num'];
            }
        }
        
        $model = new Order();
        $model->order_sn = $order_sn;           //订单编号
        $model->goods_num = $sel_num;           //订单中素材的数量
        $model->goods_amount = $total_price;    //价格
        $model->order_amount = $total_price;    //应付价格
        $model->created_by = Yii::$app->user->id; //创建用户
        
        $userProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]); // 当前用户附加属性
        if($userProfile->is_certificate == 0){
            // 保存订单
            if($model->load(Yii::$app->request->post()) && $model->save()){
                try {
                    // 下单成功后在购物车里删除它
                    $carts = Cart::findAll(['is_selected' => 1]);
                    foreach ($carts as $cart){
                        $cart->is_del = 1;
                        $cart->save();
                    }
                    // 保存订单操作记录
                    $order = Order::findOne(['order_sn' => $order_sn]);
                    OrderAction::savaOrderAction($order->id, '提交订单', '提交订单', $order->order_status, $order->play_status, Yii::$app->user->id);
                    // 保存订单素材表
                    $data = [];
                    foreach ($dataProvider->models as $value) {
                        $data[] = [
                            $order->id, $order_sn, $value['media_id'], $value['price'],
                            $value['price'], Yii::$app->user->id, time(), time()
                        ];
                    }
                    Yii::$app->db->createCommand()->batchInsert(OrderGoods::tableName(),
                        ['order_id', 'order_sn', 'goods_id', 'price', 'amount', 'created_by', 'created_at', 'updated_at'], $data)->execute();
                    // 跳转到下单成功页
                    return $this->redirect(['place-order',
                        'id' => $order->id,
                    ]);
                } catch (Exception $ex) {
                    Yii::$app->getSession()->setFlash('error', '失败原因：'.$ex->getMessage());
                }
            }
        } else {
            return $this->render('error');
        }
        
        return $this->render('checking-order', [
            'model' => $model,    // 订单模型
            'dataProvider' => $dataProvider,
            'sel_num' => $sel_num,      // 选中数量
            'total_price' => $total_price,  // 选中的素材总价
        ]);
    }

    /**
     * 下单成功
     * @return mixed
     */
    public function actionPlaceOrder($id)
    {
        // 使用主布局main的布局样式
        $this->layout = '@app/views/layouts/main';
      
        $model = Order::findOne($id);
        
        return $this->render('place-order',[
            'model' => $model,
        ]);
    }

    /**
     * 弹出选择支付方式的模态框
     * @return mixed
     */
    public function actionPaymentMethod($id)
    {
        return $this->renderAjax('payment-method', ['id' => $id]);
    }
    
    /**
     * 线下支付提交审批
     * @param string $id
     * @return mixed
     */
    public function actionPlayApprove()
    {
        // 使用主布局main的布局样式
        $this->layout = '@app/views/layouts/main';

        $id = ArrayHelper::getValue(Yii::$app->request->queryParams, 'id');
        // 如果ID为空 跳转到订单列表页
        if($id == null){
            return $this->redirect(['order/index']);
        }
        // 判断是否已经存在
        $model = new PlayApprove(['order_id' => $id, 'created_by' => Yii::$app->user->id]);    // 支付核对模型
            
        if($model->load(Yii::$app->request->post()) && $model->save()){
            //更改订单信息
            $order = Order::findOne(['id' => $model->order_id]);
            $order->order_status = Order::ORDER_STATUS_TO_BE_AUDITED; //支付提交后把订单状态改为待审核
            $order->play_code = 'eeplay';   //支付方式
            $order->play_at = time();       //支付时间
            $order->play_status = Order::PLAY_STATUS_UNPAID;  //支付状态任为未付款
            if($order->save()){
                OrderAction::savaOrderAction($order->id, '支付', '提交审核', $order->order_status, $order->play_status, Yii::$app->user->id);
            }
            
            return $this->redirect(['order/view', 'id' => $model->order_id]);
        }
       
        return $this->render('play-approve', [
            'model' => $model,
        ]);
    }

    /**
     * 下载支付审批申请模板
     * @param integer $id 订单ID
     */
    public function actionDownload($id)
    {
        ExportUtils::getInstance()->downloadTable($id);
    }


    /**
     * 导出素材清单
     * @param integer $id 订单ID
     */
    public function actionExportList($id)
    {
        ExportUtils::getInstance()->exportMediaLists($id);
    }

    /**
     * Deletes an existing Cart model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cart model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Cart the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cart::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
