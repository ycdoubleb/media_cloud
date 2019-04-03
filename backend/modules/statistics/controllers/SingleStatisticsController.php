<?php

namespace backend\modules\statistics\controllers;

use common\models\AdminUser;
use common\models\log\MediaVisitLog;
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
        $year = $request->getQueryParam('year') == null ? '' : $request->getQueryParam('year');
        $month = $request->getQueryParam('month') == null ? '' : $request->getQueryParam('month');
        $userId = $request->getQueryParam('nickname');      //购买人 or 运营人
        $mediaId = $request->getQueryParam('media_id');     //素材编号
        $tabs = ArrayHelper::getValue(Yii::$app->request->queryParams, 'tabs', 'operator');    // 过滤条件tabs
        
        return $this->render('index', [
            'operator' => $this->getStatisticsByOperator($year, $month, $userId),   //运营人
            'purchaser' => $this->getStatisticsByPurchaser($year, $month, $userId), //购买人
            'media' => $this->getStatisticsByMedia($year, $month, $mediaId),        //素材
            
            'tabs' => $tabs,
            'year' => $year,
            'month' => $month,
            'years' => $this->getYears(),
            'months' => $this->getMonths(),
            'userId' => $userId,
            'mediaId' => $mediaId,
            'nicknameData' => $this->getNickname($tabs),
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }
    
    /**
     * 统计运营人所拥有的素材数量及其收入金额
     * @param array $year   年份
     * @param array $month  月份
     * @param integer $userId   运营人ID
     * @return array
     */
    protected function getStatisticsByOperator($year, $month, $userId)
    {
        $query = (new Query())
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.owner_id' => $userId]);
        
        // 素材数量
        $mediaQuery = clone $query;
        $mediaQuery->addSelect(['COUNT(Media.id) AS media_num'])
            ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED]);   //已发布的素材
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $mediaQuery->andFilterWhere(["FROM_UNIXTIME(Media.created_at, '%Y')" => $year]);
            $mediaQuery->andFilterWhere(["FROM_UNIXTIME(Media.created_at, '%m')" => $month]);
        }
        $mediaNum = $mediaQuery->one();
        
        // 素材总收入金额
        $orderQuery = clone $query;
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $orderQuery->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $orderQuery->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        $orderQuery->addSelect(['SUM(OrderGoods.amount) AS order_amount'])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id');
        $orderAmount = $orderQuery->one();

        return array_merge($mediaNum,$orderAmount);
    }
    
    /**
     * 统计购买人所购买的素材数量及其支出金额
     * @param array $year   年份
     * @param array $month  月份
     * @param integer $userId   购买人ID
     * @return array
     */
    protected function getStatisticsByPurchaser($year, $month, $userId)
    {
        $query = (new Query())
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->andFilterWhere(['Order.created_by' => $userId]);
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 购买素材数量
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
     * 统计素材的引用次数/总收入金额/总学习量
     * @param array $year   年份
     * @param array $month  月份
     * @param integer $mediaId  素材ID
     * @return array
     */
    protected function getStatisticsByMedia($year, $month, $mediaId)
    {
        $query = (new Query())
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.id' => $mediaId])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id');
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 素材引用次数
        $quoteQuery = clone $query;
        $quoteQuery->addSelect(['COUNT(Order.id) AS quote_num'])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED]);  //已发布的素材
        $quoteNum = $quoteQuery->one();
        
        // 素材总收入金额
        $orderQuery = clone $query;
        $orderQuery->addSelect(['SUM(OrderGoods.amount) AS order_amount']);
        $orderAmount = $orderQuery->one();
         
        // 素材总学习量
        $clickQuery = (new Query())->from(['MediaVisitLog' => MediaVisitLog::tableName()]);
        $clickQuery->addSelect(['MediaVisitLog.visit_count AS click_num'])
            ->leftJoin(['Media' => Media::tableName()], 'Media.id = MediaVisitLog.media_id')
            ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED]);  //已发布的素材
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $clickQuery->andFilterWhere(["FROM_UNIXTIME(MediaVisitLog.visit_time, '%Y')" => $year]);
            $clickQuery->andFilterWhere(["FROM_UNIXTIME(MediaVisitLog.visit_time, '%m')" => $month]);
        }
        $clickNum['click_num'] = 0;
        foreach ($clickQuery->all() as $value) {
            $clickNum['click_num'] += $value['click_num'];
        }

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
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的素材
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id')
                ->all();
            
            $nickname = ArrayHelper::map($operator, 'id', 'nickname');
        } else {
            $purchaser = (new Query())
                ->select(['User.id', 'User.nickname'])
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by')
                ->all();
        
            $nickname = ArrayHelper::map($purchaser, 'id', 'nickname');
        }

        return $nickname;
    }
    
    /**
     * 年份
     * @return array
     */
    protected function getYears()
    {
        $startYear = 2019;                  // 平台开始使用年份
        $theYear = date('Y',time());        // 当前年份
        $addYear = $theYear - $startYear;   // 平台已经使用了N年
        
        $years = ['' => '全部'];
        for($i = 0; $i <= $addYear; $i++){
            $years += [
                $startYear + $i => $startYear + $i . '年'
            ];
        }
        
        return $years;
    }
    
    /**
     * 月份
     * @return array
     */
    protected function getMonths()
    {
        $month = [
            '' => '全年',
            '01' => '01月',
            '02' => '02月',
            '03' => '03月',
            '04' => '04月',
            '05' => '05月',
            '06' => '06月',
            '07' => '07月',
            '08' => '08月',
            '09' => '09月',
            '10' => '10月',
            '11' => '11月',
            '12' => '12月',
        ];
        
        return $month;
    }
}
