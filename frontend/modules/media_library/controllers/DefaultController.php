<?php

namespace frontend\modules\media_library\controllers;

use common\components\aliyuncs\Aliyun;
use common\models\media\Media;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaIssue;
use common\models\order\Cart;
use common\models\order\Favorites;
use common\models\order\Order;
use common\models\order\OrderAction;
use common\models\order\OrderGoods;
use common\utils\DateUtil;
use frontend\modules\media_library\searchs\MediaSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `modules` module
 */
class DefaultController extends Controller
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
        ];
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $results = $searchModel->search(array_merge(Yii::$app->request->queryParams, ['limit' => 10]));
        $medias = array_values($results['data']['media']);                  //媒体数据
        $mediaIds = ArrayHelper::getColumn($medias, 'id');
        
        $icons = [
            'video' => 'glyphicon glyphicon-facetime-video',
            'image' => 'glyphicon glyphicon-picture',
            'audio' => 'glyphicon glyphicon-music',
            'document' => 'glyphicon glyphicon-file',
        ];
        $row = 0;
        //重设媒体数据里面的元素值
        foreach ($medias as &$item) {
            $item['row'] = $row++;
            $item['cover_img'] = Aliyun::absolutePath(!empty($item['cover_url']) ? $item['cover_url'] : 'static/imgs/notfound.png');
            $item['dir_id'] = $item['duration'];
            $item['duration'] = DateUtil::intToTime($item['duration'], ':', true);
            $item['size'] = Yii::$app->formatter->asShortSize($item['size']);
            $item['tags'] = isset($item['tag_name']) ? $item['tag_name'] : 'null';
            $item['icon'] = isset($icons[$item['type_sign']]) ? $icons[$item['type_sign']] : '';
        }

        //如果是ajax请求，返回json
        if(\Yii::$app->request->isAjax){
            Yii::$app->getResponse()->format = 'json';
            try
            { 
                return [
                    'code'=> 200,
                    'data' => [
                        'result' => $medias, 
                        'page' => $results['filter']['page']
                    ],
                    'message' => '请求成功！',
                ];
            }catch (Exception $ex) {
                return [
                    'code'=> 404,
                    'data' => [],
                    'message' => '请求失败::' . $ex->getMessage(),
                ];
            }
        }
        
        return $this->render('index',[
            'searchModel' => $searchModel,      //搜索模型
            'filters' => $results['filter'],    //查询过滤的属性
            'totalCount' => $results['total'],  //总数量
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId($mediaIds),
        ]);
    }
    
    /**
     * Displays a single Media model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        // 是否已被收藏
        $hasFavorite = Favorites::findOne([
            'goods_id' => $id,
            'created_by' => \Yii::$app->user->id,
            'is_del' => 0
        ]);

        return $this->render('view', [
            'filters' => Yii::$app->request->queryParams,
            'model' => $model,
            'attrDataProvider' => MediaAttValueRef::getMediaAttValueRefByMediaId($model->id),
            'tagsDataProvider' => ArrayHelper::getColumn($model->mediaTagRefs, 'tags.name'),
            'hasFavorite' => !empty($hasFavorite),
        ]);
    }
    
    /**
     * 把媒体加入购物车
     * @return minxd
     */
    public function actionAddCarts()
    {
        try{
            $id = Yii::$app->request->post('ids');
            $media_ids = explode(',', $id);
            foreach($media_ids as $media_id){
                $model = Cart::findOne(['goods_id' => $media_id, 'is_del' => 0, 'created_by' => \Yii::$app->user->id]);
                if($model == null){
                    $model = new Cart(['goods_id' => $media_id, 'created_by' => \Yii::$app->user->id]);
                    $model->save();
                }
            }
            Yii::$app->getSession()->setFlash('success', '成功加入购物车！');
        } catch (Exception $ex) {
            Yii::$app->getSession()->setFlash('error', '加入购物车失败！失败原因：'.$ex->getMessage());
        }
        return $this->redirect('index');
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
        
        $id = ArrayHelper::getValue(Yii::$app->request->queryParams, 'id');
        $media_ids = explode(',', $id);
        $medias = Media::findAll($media_ids);

        $total_price = 0;
        // 计算选中媒体的总数和价格
        foreach ($medias as $media){
            $total_price += $media->price;
        }
//       var_dump($medias);exit;
        $model = new Order();
        $model->order_sn = $order_sn;               //订单编号
        $model->goods_num = count($media_ids);      //订单中媒体的数量
        $model->goods_amount = $total_price;        //价格
        $model->order_amount = $total_price;        //应付价格
        $model->created_by = Yii::$app->user->id;   //创建用户

        // 保存订单
        if($model->load(Yii::$app->request->post()) && $model->save()){
            try {
                // 保存订单操作记录
                $order = Order::findOne(['order_sn' => $order_sn]);
                OrderAction::savaOrderAction($order->id, '提交订单', '提交订单', $order->order_status, $order->play_status);
                
                // 保存订单媒体表
                $data = [];
                foreach ($medias as $value) {
                    $data[] = [
                        $order->id, $order_sn, $value->id, $value->price,
                        $value->price, Yii::$app->user->id
                    ];
                }
                Yii::$app->db->createCommand()->batchInsert(OrderGoods::tableName(),
                    ['order_id', 'order_sn', 'goods_id', 'price', 'amount', 'created_by'], $data)->execute();
                
                return $this->redirect(['/order_admin/cart/place-order',
                    'id' => $order->id,
                ]);
            } catch (Exception $ex) {
                Yii::$app->getSession()->setFlash('error', '失败原因：'.$ex->getMessage());
            }
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($medias),
            'key' => 'id',
        ]);
//        var_dump($dataProvider);exit;
        return $this->render('checking-order', [
            'model' => $model,    // 订单模型
            'dataProvider' => $dataProvider,
            'sel_num' => count($media_ids),      // 选中数量
            'total_price' => $total_price,  // 选中的媒体总价
        ]);
    }
    
    /**
     * 收藏媒体资源
     * @param int $id   媒体ID
     * @return mixed
     */
    public function actionAddFavorite($id)
    {
        $model = Favorites::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        if($model != null){
            $model->is_del = 0;
        } else {
            $model = new Favorites(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
            $model->is_del = 0;
        }
             
        if ($model->save()) {
            Yii::$app->getSession()->setFlash('success','收藏成功！');
        } else {
            Yii::$app->getSession()->setFlash('error','收藏失败！');
        }
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * 取消收藏媒体资源
     * @param int $id   媒体ID
     * @return mixed
     */
    public function actionDelFavorite($id)
    {
        $model = Favorites::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        $model->is_del = 1;
        
        if ($model->save()) {
            Yii::$app->getSession()->setFlash('success','取消收藏成功！');
        } else {
            Yii::$app->getSession()->setFlash('error','取消收藏失败！');
        }
        return $this->redirect(['view', 'id' => $id]);
    }
    
    /**
     * 打开反馈问题的模态框
     * @param int $id   媒体ID
     * @return type
     */
    public function actionFeedback($id)
    {
        $model = new MediaIssue(['media_id' => $id]);
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->getResponse()->format = 'json';
            $result = $this->Feedback($model, Yii::$app->request->post());
            return [
                'code' => $result ? 200 : 404,
                'message' => ''
            ];

        } else {
            return $this->renderAjax('feedback', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 加入购物车
     * @param int $id   媒体ID
     * @return mixed
     */
    public function actionAddCart($id)
    {
        $model = Cart::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        if($model != null){
            $model->num += 1;
        } else {
            $model = new Cart(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        }
        
        if ($model->save()) {
            Yii::$app->getSession()->setFlash('success','成功加入购物车！');
        } else {
            Yii::$app->getSession()->setFlash('error','加入购物车失败！');
        }
        return $this->redirect(['view', 'id' => $id]);
    }


    /**
     * 添加反馈问题操作
     * @param MediaIssue $model
     * @param type $post
     * @return array
     * @throws Exception
     */
    public function Feedback($model, $post)
    {
        /** 开启事务 */
        $trans = Yii::$app->db->beginTransaction();
        try
        {  
            $results = $this->saveFeedback($post);
            if($results['code'] == 400){
                throw new Exception($model->getErrors());
            }
            
            $trans->commit();  //提交事务
            return true;
            Yii::$app->getSession()->setFlash('success','操作成功！');
        }catch (Exception $ex) {
            $trans ->rollBack(); //回滚事务
            return false;
            Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
        }
    }

     /**
     * 保存反馈问题
     * @param type $post
     * @return array
     */
    public function saveFeedback($post)
    {
        $media_id = ArrayHelper::getValue($post, 'MediaIssue.media_id');    //媒体ID
        $type = ArrayHelper::getValue($post, 'MediaIssue.type');            //问题类型
        $content = ArrayHelper::getValue($post, 'MediaIssue.content');      //问题描述
        
        if($type == null){
            return false;
        } else {
            $values = [
                'media_id' => $media_id,
                'type' => $type,
                'content' => $content,
                'created_by' => \Yii::$app->user->id,
                'created_at' => time(),
                'updated_at' => time(),
            ];
            /** 添加$values数组到表里 */
            $num = Yii::$app->db->createCommand()->insert(MediaIssue::tableName(),$values)->execute();
            if($num > 0){
                return ['code' => 200];
            } else {
                return ['code' => 400];
            }
        }
    }
    
    /**
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
