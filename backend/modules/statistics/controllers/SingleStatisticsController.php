<?php

namespace backend\modules\statistics\controllers;

use common\models\AdminUser;
use common\models\media\Acl;
use common\models\media\Media;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

/**
 * Default controller for the `statistic_admin` module
 */
class SingleStatisticsController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        /* @var $request Request */
        $request = Yii::$app->getRequest();
        $dateRange = $request->getQueryParam('dateRange');  //时间段
        $userId = $request->getQueryParam('nickname');      //购买人 or 运营人
        $mediaId = $request->getQueryParam('media_id');     //媒体编号
        $tabs = ArrayHelper::getValue(Yii::$app->request->queryParams, 'tabs', 'operator');    // 过滤条件tabs
        
        return $this->render('index', [
            'operator' => $this->getStatisticsByOperator($dateRange, $userId),
            'purchaser' => $this->getStatisticsByPurchaser($dateRange, $userId),
            'media' => $this->getStatisticsByMedia($dateRange, $mediaId),
            
            'tabs' => $tabs,
            'dateRange' => $dateRange,
            'userId' => $userId,
            'mediaId' => $mediaId,
            'nicknameData' => $this->getNickname($tabs),
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }
    
    /**
     * 统计运营人所拥有的媒体数量及其收入金额
     * @param string $dateRange
     * @param integer $userId
     * @return array
     */
    protected function getStatisticsByOperator($dateRange, $userId)
    {
        $query = (new Query())
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.owner_id' => $userId]);
        
        // 媒体数量
        $mediaQuery = clone $query;
        $mediaQuery->addSelect(['COUNT(Media.id) AS media_num'])
            ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED]);   //已发布的媒体
        $mediaNum = $mediaQuery->one();
        
        // 媒体总收入金额
        $orderQuery = clone $query;
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $orderQuery->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        $orderQuery->addSelect(['SUM(OrderGoods.amount) AS order_amount'])
                ->andFilterWhere(['Order.order_status' => Order::ORDER_STATUS_CONFIRMED])   //已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id');
        $orderAmount = $orderQuery->one();

        return array_merge($mediaNum,$orderAmount);
    }
    
    /**
     * 统计购买人所购买的媒体数量及其支出金额
     * @param string $dateRange
     * @param integer $userId
     * @return array
     */
    protected function getStatisticsByPurchaser($dateRange, $userId)
    {
        $query = (new Query())
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => Order::ORDER_STATUS_CONFIRMED])   //已确认的订单
                ->andFilterWhere(['Order.created_by' => $userId]);
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        // 购买媒体数量
        $mediaQuery = clone $query;
        $mediaQuery->addSelect(['COUNT(OrderGoods.id) AS media_num'])
            ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.order_id = Order.id');
        $mediaNum = $mediaQuery->one();

        // 总支出金额
        $orderQuery = clone $query;
        $orderQuery->addSelect(['SUM(Order.order_amount) AS order_amount']);
        $orderAmount = $orderQuery->one();

        return array_merge($mediaNum,$orderAmount);
    }
    
    /**
     * 统计媒体的引用次数/总收入金额/总点击量
     * @param string $dateRange
     * @param integer $mediaId
     * @return array
     */
    protected function getStatisticsByMedia($dateRange, $mediaId)
    {
        $query = (new Query())
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.id' => $mediaId])
//                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的媒体
                ->andFilterWhere(['Order.order_status' => Order::ORDER_STATUS_CONFIRMED])   //已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id');
                
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        // 媒体引用次数
        $quoteQuery = clone $query;
        $quoteQuery->addSelect(['COUNT(Order.id) AS quote_num']);
        $quoteNum = $quoteQuery->one();
        
        // 媒体总收入金额
        $orderQuery = clone $query;
        $orderQuery->addSelect(['SUM(OrderGoods.amount) AS order_amount']);
        $orderAmount = $orderQuery->one();
         
        // 媒体总点击量
        $clickQuery = clone $query;
        $clickQuery->addSelect(['SUM(Acl.visit_count) AS click_num'])
            ->leftJoin(['Acl' => Acl::tableName()], 'Acl.media_id = Media.id');   //已发布的媒体
        $clickNum = $clickQuery->one();        

        return array_merge($quoteNum,$orderAmount,$clickNum);
    }

    /**
     * 查找运营人 or 购买人
     * @param string $tabs  显示的内容
     * @return ARray
     */
    protected function getNickname($tabs)
    {
        if($tabs == 'operator'){
            $operator = (new Query())
                ->select(['AdminUser.id', 'AdminUser.nickname'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的媒体
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id')
                ->all();
            
            $nickname = ArrayHelper::map($operator, 'id', 'nickname');
        } else {
            $purchaser = (new Query())
                ->select(['User.id', 'User.nickname'])
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => Order::ORDER_STATUS_CONFIRMED])   //已确认的订单
                ->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by')
                ->all();
        
            $nickname = ArrayHelper::map($purchaser, 'id', 'nickname');
        }

        return $nickname;
    }
}
