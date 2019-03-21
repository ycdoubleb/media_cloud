<?php

namespace apiend\modules\v1\controllers;

use apiend\controllers\ApiController;
use apiend\modules\v1\actions\media_type\ListAction;

/**
 * 短信服务接口
 *
 * @author Administrator
 */
class MediaTypeController extends ApiController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = [
            'list',
        ];
        $behaviors['verbs']['actions'] = [
            'list' => ['get'],
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'list' => ['class' => ListAction::class],
        ];
    }

}
