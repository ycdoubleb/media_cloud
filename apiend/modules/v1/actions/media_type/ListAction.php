<?php

namespace apiend\modules\v1\actions\media_type;

use apiend\models\Response;
use apiend\modules\v1\actions\BaseAction;
use common\models\media\MediaType;

/**
 * 获取所有素材类型
 *
 * @author Administrator
 */
class ListAction extends BaseAction
{

    public function run()
    {
        $types = MediaType::find()
                ->select(['id', 'name', 'sign'])
                ->where(['is_del' => 0])
                ->all();
        return new Response(Response::CODE_COMMON_OK, null, $types);
    }

}
