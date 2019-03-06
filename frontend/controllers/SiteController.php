<?php

namespace frontend\controllers;

use common\models\api\ApiResponse;
use common\models\Banner;
use common\models\LoginForm;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use common\models\UserProfile;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use linslin\yii2\curl\Curl;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use const YII_ENV_TEST;

/**
 * Site controller
 */
class SiteController extends Controller {

    public static $sendYunSmsConfig = 'sendYunSms'; //发送短信的配置
    
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        /* 宣传 */
        $banners = Banner::find()
                ->where(['is_publish' => Banner::YES_PUBLISH])
                ->orderBy('sort_order')
                ->all();
        
        return $this->render('index', [
            'banners' => $banners,
            'totals' => $this->getTotalMediaNumber(),
            'month' => $this->getTotalMediaNumber(true),
            'amount' => $this->getTotalOrderAmount()
        ]);
    }

    public function actionUpdateUser($id) {
        /* @var $user User */
        $post = \Yii::$app->request->post();
        $user = User::findOne(['id' => $id]);
        if ($user->load($post)) {
            if ($user->validate() && $user->save()) {
                \Yii::$app->response->format = 'json';
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, null, $user->toArray());
            }
        } else {
            return $this->renderAjax('_test_form', ['model' => $user]);
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->scenario = LoginForm::SCENARIO_PASS;    //设置密码登录场景
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout() {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup() {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;
        $post = \Yii::$app->request->post();            //post传值
        
        if ($model->load($post)) {
            $phone = ArrayHelper::getValue($post, 'User.phone');        //获取post传的号码
            $code = ArrayHelper::getValue($post, 'User.code');          //获取post传的验证码
            $sessonPhone = Yii::$app->session->get('code_phone', '');   //保存在sesson中的电话号码
            $sessonCode = Yii::$app->session->get('code_code', '');     //保存在sesson中的验证码
            $time_out = Yii::$app->session->get('code_timeOut', '');    //保存在sesson中的过期时间
            $now_time = time();                                         //当前时间
            
            if ($sessonPhone != $phone || $sessonCode != $code || $now_time >= $time_out) {
                Yii::$app->getSession()->setFlash('error', '号码、验证码不匹配或验证码已失效！');
            }elseif ($user = $this->signup($post)) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
       
        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    
    /**
     * 重置密码请求（短信验证）
     * @return mix
     */
    public function actionGetPassword() {
        return $this->render('get-password');
    }
    
    /**
     * 重置密码（短信验证）
     * @return mix
     */
    public function actionSetPassword() {
        $sessonPhone = Yii::$app->session->get('code_phone');
        if (empty($sessonPhone)) {
            return $this->goHome();
        }
        $model = User::findOne(['phone' => $sessonPhone]);
        $model->password_hash = '';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->password_hash);
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'New password saved.'));
//                unset(Yii::$app->session['code_timeOut']);  //销毁sesson
                return $this->goHome();
            }
        }

        return $this->render('set-password', [
                    'model' => $model,
        ]);
    }
    
    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }
    
    /**
     * 发送验证码的动作
     * @return array
     */
    public function actionSendSms() {
        $sendYunSmsConfig = \Yii::$app->params[self::$sendYunSmsConfig];         //发送验证码配置
        $BINGDING_PHONE_ID = $sendYunSmsConfig['SMS_TEMPLATE_ID']['BINGDING_PHONE'];  //注册绑定手机号码/短信登录短信模板ID
        $RESET_PASSWORD_ID = $sendYunSmsConfig['SMS_TEMPLATE_ID']['RESET_PASSWORD'];  //重置密码短信模板ID

        \Yii::$app->getResponse()->format = 'json';
        $post = \Yii::$app->request->post();
        $phone = ArrayHelper::getValue($post, 'MOBILE');   //获取输入的电话号码
        $pathName = ArrayHelper::getValue($post, 'pathname');   //获取点击发送验证码时的路径
        $name = trim(strrchr($pathName, '/'), '/');

        //检查提交的号码是否存在
        $hasPhone = (new Query())->select(['id'])->from(['User' => User::tableName()])
                ->andFilterWhere(['status' => User::STATUS_ACTIVE, 'phone' => $phone])
                ->one();
        if ($name == 'signup') {      //注册页面
            if (empty($hasPhone)) {
                $xmlResult = $this->sendSms($phone, $BINGDING_PHONE_ID);    //发送验证码功能
            } else {
                Yii::$app->session->setFlash('error', '号码错误或已存在！不能继续注册！！');
                return $this->goHome();
            }
        } elseif ($name == 'login') {   //登录页面
            if (!empty($hasPhone)) {
                $xmlResult = $this->sendSms($phone, $BINGDING_PHONE_ID);    //发送验证码功能
            } else {
                Yii::$app->session->setFlash('error', '号码错误或不存在！');
                return $this->goHome();
            }
        } elseif ($name == 'get-password') {    //重置密码页面
            if (!empty($hasPhone)) {
                $xmlResult = $this->sendSms($phone, $RESET_PASSWORD_ID);    //发送验证码功能
            } else {
                Yii::$app->session->setFlash('error', '号码错误或不存在！');
                return $this->goHome();
            }
        }

        if ($xmlResult == 1) {
            return new ApiResponse(ApiResponse::CODE_COMMON_OK, '发送成功');
        } else {
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '发送失败');
        }
    }
    
    /**
     * 检查号码是否已被注册
     * @return array
     */
    public function actionChickPhone() {
        \Yii::$app->getResponse()->format = 'json';
        $post = \Yii::$app->request->post();
        $phone = ArrayHelper::getValue($post, 'phone');   //获取输入的邀请码
        
        $hasPhone = (new Query())->select(['id'])->from(['User' => User::tableName()])
                ->andFilterWhere(['status' => User::STATUS_ACTIVE, 'phone' => $phone])
                ->one();

        if (empty($hasPhone)) {
            return new ApiResponse(ApiResponse::CODE_COMMON_OK, '该号码未被注册');
        } else {
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '该号码已被注册');
        }
    }

    /**
     * 验证输入的验证码是否正确
     * @return array
     */
    public function actionProvingCode() {
        \Yii::$app->getResponse()->format = 'json';
        $post = \Yii::$app->request->post();
        $code = ArrayHelper::getValue($post, 'code');   //获取输入的邀请码
        //保存在sesson中的验证码
        $params_code = Yii::$app->session->get('code_code', '');
        //保存在sesson中的过期时间
        $time_out = Yii::$app->session->get('code_timeOut', '');
        $now_time = time();      //当前时间

        if ($time_out >= $now_time) {
            if ($params_code == $code) {
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '验证码正确');
            } else {
                return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '验证码错误');
            }
        } else {
            return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '验证码失效');
        }
    }

    /**
     * 检查手机号码和验证码是否匹配(重置密码)
     * @return array
     */
    public function actionCheckPhoneCode() {
        \Yii::$app->getResponse()->format = 'json';
        $post = \Yii::$app->request->post();
        $phone = ArrayHelper::getValue($post, 'phone');        //联系方式
        $code = ArrayHelper::getValue($post, 'code');  //验证码

        if (empty($phone)) {
            Yii::$app->getSession()->setFlash('error', '手机号不能为空！');
        } elseif (empty($code)) {
            Yii::$app->getSession()->setFlash('error', '验证码不能为空！');
        }
        //保存在sesson中的电话号码
        $sessonPhone = Yii::$app->session->get('code_phone', '');
        $sessonCode = Yii::$app->session->get('code_code', '');
        if ($sessonPhone != $phone) {
            Yii::$app->getSession()->setFlash('error', '手机号与验证码不匹配！');
        } elseif ($code != $sessonCode) {
            Yii::$app->getSession()->setFlash('error', '验证码错误！');
        } else {
            return new ApiResponse(ApiResponse::CODE_COMMON_OK, '验证成功');
        }
        return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, '验证失败');
    }

    /**
     * 获取素材数量
     * 统计素材类型饼图信息
     * @param bool $month   是否需要过滤非本月数据
     * @return array
     */
    protected function getTotalMediaNumber($month = false)
    {
        // 符合条件的媒体
        $mediaQuey = (new Query())
                ->select(['Media.id', 'Media.type_id'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['status' => Media::STATUS_PUBLISHED, 'Media.del_status' => 0]);
        /* 过滤非本月的数据 */
        if($month){
            $monthStart = mktime(0, 0, 0, date('m'), 1, date('Y')); 
            $monthEnd = mktime(23, 59, 59, date('m'), date('t'), date('Y')); 
            $mediaQuey->andFilterWhere(['between', 'Media.created_at', $monthStart, $monthEnd]);
        }
        
        // 素材类型统计
        $statisticsQuery = (new Query())
                ->select(['MediaType.name', 'COUNT(Media.id) AS value'])
                ->from(['MediaType' => MediaType::tableName()])
                ->leftJoin(['Media' => $mediaQuey], 'Media.type_id = MediaType.id')
                ->andFilterWhere(['MediaType.is_del' => 0])
                ->groupBy(['MediaType.id'])
                ->orderBy(['MediaType.id' => SORT_ASC])
                ->all();

        // 素材总数量
        $totalNumber = 0;
        foreach ($statisticsQuery as $value){
            $totalNumber += $value['value'];
        }
        
        $data = [
            'totalNumber' => $totalNumber,
            'statisticsByType' => $statisticsQuery,
        ];

        return $data;
    }
    
    /**
     * 获取总收入金额
     * 统计各个素材类型的收入金额饼图信息
     * @return array
     */
    protected function getTotalOrderAmount()
    {
        // 符合条件的订单
        $orderQuery = (new Query())
                ->select(['Media.type_id', 'OrderGoods.amount'])
                ->from(['Order' => Order::tableName()])
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], '(OrderGoods.order_id = Order.id AND OrderGoods.is_del = 0)')
                ->leftJoin(['Media' => Media::tableName()], 'Media.id = OrderGoods.goods_id')
                ->andFilterWhere(['order_status' => [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]]);
        
        // 素材类型统计
        $statisticsQuery = (new Query())
                ->select(['MediaType.name', 'SUM(amount) AS value'])
                ->from(['MediaType' => MediaType::tableName()])
                ->andFilterWhere(['MediaType.is_del' => 0])
                ->leftJoin(['Media' => $orderQuery], 'MediaType.id = Media.type_id')
                ->groupBy('MediaType.id')
                ->orderBy(['MediaType.id' => SORT_ASC])
                ->all();
        
        // 总收入
        $totalAmount = 0;
        foreach ($statisticsQuery as &$value) {
            $value['value'] = (integer)$value['value']; 
            $totalAmount += $value['value'];
        }

        $data = [
            'totalAmount' => $totalAmount,
            'statisticsByType' => $statisticsQuery,
        ];

        return $data;
    }
    
    /**
     * 注册
     * @param type $post
     * @return type
     * @throws NotAcceptableHttpException
     */
    protected function signup($post) {
        $user = new User();
        
        $phone = ArrayHelper::getValue($post, 'User.phone');        //联系方式
        $nickname = ArrayHelper::getValue($post, 'User.nickname');  //姓名
        $password_hash = ArrayHelper::getValue($post, 'User.password_hash');    //密码

        $company = ArrayHelper::getValue($post, 'User.company');    //公司名称
        $department = ArrayHelper::getValue($post, 'User.department');    //部门名称
        
        //赋值保存用户信息
        $user->username = $phone;
        $user->nickname = $nickname;
        $user->phone = $phone;
        $user->avatar = ($user->sex == null) ? '/upload/avatars/default.jpg' :
                '/upload/avatars/default/' . ($user->sex == 1 ? 'man' : 'women') . rand(1, 25) . '.jpg';
        $user->setPassword($password_hash);
        $user->generateAuthKey();
        $isTrue = $user->save();

        //保存用户扩展信息
        if ($isTrue) {
            $userProfile = new UserProfile();
            $userProfile->user_id = $user->id;
            $userProfile->company = $company;
            $userProfile->department = $department;
            $userProfile->is_certificate = 0;
            $userProfile->save();
        }

        return $isTrue ? $user : null;
    }

    /**
     * 发送验证码
     * @param integer $phone    电话号码
     * @param string $SMS_TEMPLATE_ID   短信模板
     * @return array
     */
    public function sendSms($phone, $SMS_TEMPLATE_ID) {
        $sendYunSmsConfig = Yii::$app->params[self::$sendYunSmsConfig];         //发送验证码配置
        $SMS_APP_ID = $sendYunSmsConfig['SMS_APP_ID'];                          //应用ID

        $str = '0123456789876543210';
        $randStr = str_shuffle($str);           //打乱字符串  
        //把生成的验证码和到期时间保存到sesson中
        Yii::$app->session->set('code_phone', $phone);
        Yii::$app->session->set('code_code', substr($randStr, 0, 4)); //验证码【substr(string,start,length);返回字符串的一部分】
        Yii::$app->session->set('code_timeOut', time() + 30 * 60);


        $PARAMS = Yii::$app->session->get('code_code');
        //传递的参数【必须是以下xml格式】
        $xmlDatas = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<tranceData>' .
                "<MOBILE><![CDATA[$phone]]></MOBILE>" .
                "<SMS_TEMPLATE_ID><![CDATA[$SMS_TEMPLATE_ID]]></SMS_TEMPLATE_ID>" .
                "<SMS_APP_ID><![CDATA[$SMS_APP_ID]]></SMS_APP_ID>" .
                '<PARAMS>' .
                "<![CDATA[$PARAMS]]>" .
                '</PARAMS>' .
                '</tranceData>';

        $url = 'http://eesms.gzedu.com/sms/sendYunSms.do';  //发送短信的请求地址
        $curl = new Curl();
        $response = $curl
                        ->setOption(CURLOPT_HTTPHEADER, Array("Content-Type:text/xml; charset=utf-8"))
                        ->setOption(CURLOPT_POSTFIELDS, $xmlDatas)->post($url); //提交发送
        //转换为simplexml对象
        $xmlResult = simplexml_load_string($response); //XML 字符串载入对象中

        return (string) $xmlResult->result;
    }

}
