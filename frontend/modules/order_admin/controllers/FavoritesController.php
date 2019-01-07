<?php

namespace frontend\modules\order_admin\controllers;

use common\models\order\Cart;
use common\models\order\Favorites;
use common\models\order\searchs\FavoritesSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * FavoritesController implements the CRUD actions for Favorites model.
 */
class FavoritesController extends Controller
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
     * Lists all Favorites models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FavoritesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }
    
    /**
     * 把收藏的媒体加入购物车
     * @return minxd
     */
    public function actionAddCart()
    {
        try{
            $id = Yii::$app->request->post('ids');
            $ids = explode(',', $id);
            foreach($ids as $id){
                $model = Cart::findOne(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
                if($model == null){
                    $model = new Cart(['goods_id' => $id, 'created_by' => \Yii::$app->user->id]);
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
     * 取消收藏
     * @return minxd
     */
    public function actionDelFavorites()
    {
        try{
            $id = Yii::$app->request->post('ids');
            $ids = explode(',', $id);
            //数组直接查询
            $models = Favorites::find()->where(['in', 'goods_id', $ids])->all();
            foreach($models as $model){
                $model->is_del = 1;
                $model->save();
            }
            Yii::$app->getSession()->setFlash('success', '取消收藏成功！');
        } catch (Exception $ex) {
            Yii::$app->getSession()->setFlash('error', '取消收藏失败！失败原因：'.$ex->getMessage());
        }
        return $this->redirect('index');
    }

    /**
     * Displays a single Favorites model.
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
     * Deletes an existing Favorites model.
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
     * Finds the Favorites model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Favorites the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Favorites::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
