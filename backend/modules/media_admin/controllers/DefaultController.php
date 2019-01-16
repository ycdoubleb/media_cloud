<?php

namespace backend\modules\media_admin\controllers;

use common\components\redis\RedisService;
use common\models\media\Acl;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `media_admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        
        $id = 9;
        
        
        $params['newInfo'] = Acl::getAclInfoById($id);
        
        RedisService::incrementView($id, Acl::$redisKey); //添加浏览量
        
        var_dump($params['newInfo']);exit;
        
        if (empty($params['newInfo'])) $this->redirect('site/error');
        
        
        return $this->render('info', $params);
    }
}
