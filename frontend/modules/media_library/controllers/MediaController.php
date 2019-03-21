<?php

namespace frontend\modules\media_library\controllers;

use common\components\aliyuncs\Aliyun;
use common\models\api\ApiResponse;
use common\models\media\Dir;
use common\models\media\Media;
use common\models\media\MediaAttribute;
use common\models\media\MediaAttValueRef;
use common\models\media\MediaIssue;
use common\models\order\Cart;
use common\models\order\Favorites;
use common\models\order\Order;
use common\models\order\OrderAction;
use common\models\order\OrderGoods;
use common\models\UserProfile;
use common\utils\DateUtil;
use frontend\modules\media_library\searchs\MediaSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Media controller for the `modules` module
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
                    [
                        'actions' => ['view'],   //素材详情
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ]
        ];
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $results = $searchModel->searchMediaData(array_merge(Yii::$app->request->queryParams, ['limit' => 10]), true);
        
        return $this->render('index',[
            'searchModel' => $searchModel,      //搜索模型
            'filters' => $results['filter'],    //查询过滤的属性
            'totalCount' => $results['total'],  //总数量
            'attrMap' => MediaAttribute::getMediaAttributeByCategoryId(),
        ]);
    }
    
    /**
     * Index（素材库）页素材列表数据
     * @return ApiResponse
     */
    public function actionMediaData()
    {
        $searchModel = new MediaSearch();
        $results = $searchModel->searchMediaData(array_merge(Yii::$app->request->queryParams, ['limit' => 10]), false);
        $medias = array_values($results['data']['media']);                  //素材数据
        
        $icons = [
            'video' => 'glyphicon glyphicon-facetime-video',
            'image' => 'glyphicon glyphicon-picture',
            'audio' => 'glyphicon glyphicon-music',
            'document' => 'glyphicon glyphicon-file',
        ];
        $row = 0;
        //重设素材数据里面的元素值
        foreach ($medias as &$item) {
            $item['row'] = $row++;
            $item['cover_img'] = Aliyun::absolutePath(!empty($item['cover_url']) ? $item['cover_url'] : 'static/imgs/notfound.png');
            $item['dir_id'] = Dir::getDirById($item['dir_id'])->getFullPath();
            $item['duration'] = $item['duration'] > 0 ? DateUtil::intToTime($item['duration'], ':', true) : '';
            $item['size'] = Yii::$app->formatter->asShortSize($item['size']);
            $item['tags'] = isset($item['tag_name']) ? $item['tag_name'] : 'null';
            $item['icon'] = isset($icons[$item['type_sign']]) ? $icons[$item['type_sign']] : '';
        }
            
        //如果是ajax请求，返回json
        if(\Yii::$app->request->isAjax){
            Yii::$app->getResponse()->format = 'json';
            try
            { 
                $data = [
                        'result' => $medias, 
                        'page' => $results['filter']['page']
                    ];
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '请求成功！', $data);
            }catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '请求失败::' . $ex->getMessage());
            }
        }
        
        return $this->render('media-data');
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
        
        $attributeInfo = MediaAttValueRef::getMediaAttValueRefByMediaId($model->id, false); // 素材属性
        $tagsInfo = ArrayHelper::getColumn($model->mediaTagRefs, 'tags.name');      // 标签信息

        // 组装素材属性
        $attr = [];
        foreach ($attributeInfo as $value) {
            $attr[] = ['label' => $value['attr_name'], 'value' => $value['attr_value']];
        }
        
        // 素材基础数据
        $datas = [
            ['label' => '素材编号', 'value' => $model->id],
            ['label' => '素材名称', 'value' => $model->name],
            ['label' => '素材类型', 'value' => $model->mediaType->name],
            ['label' => '价格', 'value' => \Yii::$app->formatter->asCurrency($model->price)],
            ['label' => '时长', 'value' => $model->duration > 0 ? DateUtil::intToTime($model->duration, ':', true) : ''],
            ['label' => '大小', 'value' => Yii::$app->formatter->asShortSize($model->size)]
        ];
        
        // 如果合并后数据为奇数 则添加一个数组
        $result = array_merge($datas, $attr);
        if(count($result)%2 == 1){
            $result[] = ['label' => '', 'value' => ''];
        }

        return $this->render('view', [
            'filters' => Yii::$app->request->queryParams,
            'model' => $model,
            'datas' => $result,
            'tagsInfo' => implode('，', $tagsInfo),
            'hasFavorite' => !empty($hasFavorite),
        ]);
        
    }
    
    /**
     * 把素材批量加入购物车
     * @return minxd
     */
    public function actionAddCarts()
    {
        Yii::$app->getResponse()->format = 'json';
        
        try{
            $id = Yii::$app->request->post('ids');
            $media_ids = explode(',', $id);
            foreach($media_ids as $media_id){
                $model = Cart::findOne(['goods_id' => $media_id, 'created_by' => \Yii::$app->user->id]);
                if($model == null){
                    $model = new Cart(['goods_id' => $media_id, 'created_by' => \Yii::$app->user->id]);
                } else {
                    $model->is_del = 0;
                }
                $model->save();
            }
            Yii::$app->getSession()->setFlash('success','加入购物车成功！');
            return new ApiResponse(ApiResponse::CODE_COMMON_OK);
        } catch (Exception $ex) {
            Yii::$app->getSession()->setFlash('error','加入购物车失败！失败原因::'.$ex->getMessage());
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '加入购物车失败！失败原因：'.$ex->getMessage());
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
        
        $id = ArrayHelper::getValue(Yii::$app->request->queryParams, 'id');
        $media_ids = explode(',', $id);
        $medias = Media::findAll($media_ids);

        $total_price = 0;
        // 计算选中素材的总数和价格
        foreach ($medias as $media){
            $total_price += $media->price;
        }

        $model = new Order();
        $model->order_sn = $order_sn;               //订单编号
        $model->goods_num = count($media_ids);      //订单中素材的数量
        $model->goods_amount = $total_price;        //价格
        $model->order_amount = $total_price;        //应付价格
        $model->created_by = Yii::$app->user->id;   //创建用户

        $userProfile = UserProfile::findOne(['user_id' => Yii::$app->user->id]); // 当前用户附加属性
        if($userProfile->is_certificate == 1){
            // 保存订单
            if($model->load(Yii::$app->request->post()) && $model->save()){
                try {
                    // 保存订单操作记录
                    OrderAction::savaOrderAction($model->id, '提交订单', '提交订单', $model->order_status, $model->play_status, Yii::$app->user->id);

                    // 保存订单素材表
                    $data = [];
                    foreach ($medias as $value) {
                        $data[] = [
                            $model->id, $order_sn, $value->id, $value->price,
                            $value->price, Yii::$app->user->id, time(), time()
                        ];
                    }
                    Yii::$app->db->createCommand()->batchInsert(OrderGoods::tableName(),
                        ['order_id', 'order_sn', 'goods_id', 'price', 'amount', 'created_by', 'created_at', 'updated_at'], $data)->execute();
                    // 跳转到下单成功页
                    return $this->redirect(['/order_admin/cart/place-order',
                        'id' => $model->id,
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
            'dataProvider' =>  new ArrayDataProvider([
                'allModels' => array_values($medias),
                'key' => 'id',
            ]),
            'sel_num' => count($media_ids),      // 选中数量
            'total_price' => $total_price,  // 选中的素材总价
        ]);
    }
    
    /**
     * 收藏 or 取消收藏 素材资源
     * @param int $id   素材ID
     * @return ApiResponse
     */
    public function actionChangeFavorite($id)
    {
        Yii::$app->getResponse()->format = 'json';
        
        $model = Favorites::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        if($model != null){
            if($model->is_del == 0){
                $model->is_del = 1;
                $is_favorite = false;
            } else {
                $model->is_del = 0;
                $is_favorite = true;
            }
        } else {
            $model = new Favorites(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
            $model->is_del = 0;
            $is_favorite = true;
        }

        if ($model->save()) {
            return new ApiResponse(ApiResponse::CODE_COMMON_OK, null, $is_favorite);
        } 
        else {
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN);
        }
    }
    
    /**
     * 打开反馈问题的模态框 / 添加反馈问题操作
     * @param int $id   素材ID
     * @return type
     */
    public function actionFeedback($id)
    {
        $model = new MediaIssue(['media_id' => $id]);
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->getResponse()->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {  
                $results = $this->saveFeedback(Yii::$app->request->post());
                if($results <= 0){
                    throw new Exception($model->getErrors());
                }
                $trans->commit();  //提交事务
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '操作成功！');
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '操作失败::'.$ex->getMessage());
            }
        } else {
            return $this->renderAjax('feedback', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 加入购物车
     * @param int $id   素材ID
     * @return mixed
     */
    public function actionAddCart($id)
    {
        $model = Cart::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
        if($model != null){
            $model->is_del = 0;
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
     * 获取 目录的子级
     * @param string $id
     * @param string $target_id
     */
    public function actionSearchChildren($id, $category_id = 1, $target_id = null){
        $dirsChildren = Dir::getDirsChildren($id, \Yii::$app->user->id, $category_id); 
        $childrens = [];
        foreach ($dirsChildren as $index => $item) {
            if($target_id != null){
                if($target_id == $item['id']){
                    unset($item[$index]);
                    break;
                }
            }
            $childrens[] = $item;
        }
        
        Yii::$app->getResponse()->format = 'json';
        
        return new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $childrens);
    }
    
    /**
     * 保存反馈问题
     * @param type $post
     * @return array
     */
    public function saveFeedback($post)
    {
        $media_id = ArrayHelper::getValue($post, 'MediaIssue.media_id');    //素材ID
        $type = ArrayHelper::getValue($post, 'MediaIssue.type');            //问题类型
        $content = ArrayHelper::getValue($post, 'MediaIssue.content');      //问题描述

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
        
        return $num;
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
        if (($model = Media::findOne(['id' => $id, 'del_status' => 0, 'status' => Media::STATUS_PUBLISHED])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
