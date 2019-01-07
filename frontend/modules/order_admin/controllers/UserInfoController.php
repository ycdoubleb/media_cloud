<?php

namespace frontend\modules\order_admin\controllers;

use common\models\User;
use common\models\UserProfile;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserInfoController implements the CRUD actions for Cart model.
 */
class UserInfoController extends Controller
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
     * 更改用户信息页面
     * @return type
     */
    public function actionSetting()
    {       
        $userModel = $this->findUserModel(Yii::$app->user->id);
        $userModel->scenario = User::SCENARIO_UPDATE;
        $peofileModel = $this->findProfileModel(Yii::$app->user->id);

        if($userModel->id != Yii::$app->user->id){
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        // 判断更改的数据
        $sets = ArrayHelper::getValue(Yii::$app->request->queryParams, 'set', 'base');
        if($sets == 'base'){
            // 基础数据
            if ($userModel->load(Yii::$app->request->post())) {
                $userModel->save();
            }
        } else {
            // 附加数据
            if ($peofileModel->load(Yii::$app->request->post())) {
                $peofileModel->save();
            }
        }
        
        return $this->render('setting', [
            'userModel' => $userModel,
            'peofileModel' => $peofileModel,
            'sets' => $sets,
            'filter' => Yii::$app->request->queryParams,
        ]);

    }
    
    /**
     * 个人统计页面
     * @return type
     */
    public function actionStatistics()
    {
        return $this->render('statistics');
    }
    
    
    /**
     * 根据其主键值查找 User 模型。
     * 如果找不到模型，就会抛出404个HTTP异常。
     * @param string $id
     * @return model User 
     * @throws NotFoundHttpException
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    /**
     * 根据其主键值查找 UserProfile 模型。
     * 如果找不到模型，就会抛出404个HTTP异常。
     * @param string $id
     * @return model User 
     * @throws NotFoundHttpException
     */
    protected function findProfileModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}

