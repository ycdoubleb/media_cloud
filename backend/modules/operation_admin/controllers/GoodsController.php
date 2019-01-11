<?php

namespace backend\modules\operation_admin\controllers;

use backend\modules\operation_admin\searchs\OrderGoodsSearch;
use common\models\order\OrderGoods;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GoodsController implements the CRUD actions for OrderGoods model.
 */
class GoodsController extends Controller
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
     * 列出所有OrderGoods数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderGoodsSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['goods'],
                'key' => 'id'
            ]),
            'uploadedByMap' => ArrayHelper::map($results['data']['uploadedBys'], 'id', 'nickname'),
            'createdByMap' => ArrayHelper::map($results['data']['createdBys'], 'id', 'nickname'),
        ]);
    }   
}
