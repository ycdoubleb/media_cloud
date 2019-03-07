<?php

namespace backend\modules\operation_admin\controllers;

use common\models\searchs\UserSearch;
use common\models\User;
use common\models\UserProfile;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * 列出所有用户模型 .
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch(['status' => User::STATUS_ACTIVE]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $dataProvider,
                'key' => 'id',
            ]),
        ]);
    }

    /**
     * 启用
     * @return mixed
     */
    public function actionEnable($ids)
    {
        try {
            $ids = explode(',', $ids);
            User::updateAll(['status' => User::STATUS_ACTIVE], ['id' => $ids]);
            Yii::$app->getSession()->setFlash('success','操作成功！');
        } catch (Exception $exc) {
            Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * 停用
     * @return mixed
     */
    public function actionDisable($ids)
    {
        try {
            $ids = explode(',', $ids);
            User::updateAll(['status' => User::STATUS_STOP], ['id' => $ids]);
            Yii::$app->getSession()->setFlash('success','操作成功！');
        } catch (Exception $exc) {
            Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * 通过认证
     * @return mixed
     */
    public function actionPass($ids)
    {
        try {
            $ids = explode(',', $ids);
            UserProfile::updateAll(['is_certificate' => UserProfile::CERTIFICATE_YES], ['user_id' => $ids]);
            Yii::$app->getSession()->setFlash('success','操作成功！');
        } catch (Exception $exc) {
            Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * 编辑用户
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        
        $model = User::findOne($id);
        $model->scenario = User::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($model->save())
                return $this->redirect(['index']);
            else
                Yii::error($model->errors);
        }else {
            $model->password_hash = '';
            return $this->render('update', ['model' => $model]);
        }
    }
}
