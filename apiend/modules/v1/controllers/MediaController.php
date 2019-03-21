<?php

namespace apiend\modules\v1\controllers;

use apiend\controllers\ApiController;
use apiend\modules\v1\actions\media\GetDetailAction;
use apiend\modules\v1\actions\media\SearchAction;

/**
 * 短信服务接口
 *
 * @author Administrator
 */
class MediaController extends ApiController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = [
            'get-detail',
            'search',
        ];
        $behaviors['verbs']['actions'] = [
            'get-detail' => ['get'],
            'search' => ['get'],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'get-detail' => ['class' => GetDetailAction::class],
            'search' => ['class' => SearchAction::class],
        ];
    }

}
