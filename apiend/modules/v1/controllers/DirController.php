<?php

namespace apiend\modules\v1\controllers;

use apiend\controllers\ApiController;
use apiend\modules\v1\actions\dir\GetDetailAction;

/**
 * 短信服务接口
 *
 * @author Administrator
 */
class DirController extends ApiController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = [
            'get-detail',
        ];
        $behaviors['verbs']['actions'] = [
            'get-detail' => ['get'],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'get-detail' => ['class' => GetDetailAction::class],
        ];
    }

}
