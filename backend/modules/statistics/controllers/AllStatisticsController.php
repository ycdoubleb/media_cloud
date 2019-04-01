<?php

namespace backend\modules\statistics\controllers;

use common\models\log\MediaVisitLog;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Order;
use common\models\order\OrderGoods;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `statistic_admin` module
 */
class AllStatisticsController extends Controller
{
    private $category_id;


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->category_id = ArrayHelper::getValue(Yii::$app->request->queryParams, 'category_id');
        
        return $this->render('index', [
            'total_media_num' => $this->getTotalMediaNumber(),
            'total_order_amount' => $this->getTotalOrderAmount(),
            'total_visit_num' => $this->getTotalVisitNumber(),
            'statistics_chart' => $this->getStatisticsByType()
        ]);
    }
    
    /**
     * 获取素材总数量
     * @return array
     */
    protected function getTotalMediaNumber()
    {
        $query = (new Query())
                ->select(['COUNT(id) AS total_media_num'])
                ->from(['Media' => Media::tableName()])
                ->where([
                    'category_id' => $this->category_id,
                    'status' => Media::STATUS_PUBLISHED, 'del_status' => 0
                ])->one();

        return $query;
    }
    
    /**
     * 获取总收入金额
     * @return array
     */
    protected function getTotalOrderAmount()
    {
        $orderQuery = (new Query())
                ->select(['order_amount'])
                ->from(['Order' => Order::tableName()])
                ->leftJoin(['Goods' => OrderGoods::tableName()], 'Goods.order_id = Order.id')
                ->leftJoin(['Media' => Media::tableName()], 'Media.id = Goods.goods_id')
                ->where(['order_status' =>
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]]
                )
                ->andWhere(['category_id' => $this->category_id])
                ->groupBy('Order.id');
        
        $query = (new Query())
                ->select(['SUM(Order.order_amount) AS total_order_amount'])
                ->from(['Order' => $orderQuery])
                ->one();

        return $query;
    }
    
    /**
     * 获取总学习量
     * @return array
     */
    protected function getTotalVisitNumber()
    {
        $query = (new Query())
                ->select(['SUM(MediaVisitLog.visit_count) AS value'])
                ->from(['MediaVisitLog' => MediaVisitLog::tableName()])
                ->leftJoin(['Media' => Media::tableName()], 'Media.id = MediaVisitLog.media_id')
                ->where(['category_id' => $this->category_id])
                ->one();
        
        return $query['value'];
    }
    
    /**
     * 获取统计饼图信息
     * @return array
     */
    protected function getStatisticsByType()
    {
        $query = (new Query())
                ->select(['MediaType.name', 'COUNT(Media.id) AS value'])
                ->from(['Media' => Media::tableName()])
                ->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id')
                ->where(['category_id' => $this->category_id, 'status' => Media::STATUS_PUBLISHED, 'del_status' => 0])
                ->groupBy('MediaType.id')
                ->all();

        return $query;
    }
}
