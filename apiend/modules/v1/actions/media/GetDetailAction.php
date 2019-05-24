<?php

namespace apiend\modules\v1\actions\media;

use apiend\models\Response;
use apiend\modules\v1\actions\BaseAction;
use common\models\media\Acl;
use common\models\media\Media;

/**
 * 获取素材详细
 *
 * @author Administrator
 */
class GetDetailAction extends BaseAction
{

    protected $requiredParams = ['media_id'];

    public function run()
    {
        $post = $this->getSecretParams();
        //指定ID
        $media_id = $this->getSecretParam('media_id', null);

        $media = Media::findOne(['id' => $media_id, 'del_status' => 0]);

        if ($media == null) {
            /* 找不到目录或者目录已经删除 */
            return new Response(Response::CODE_COMMON_NOT_FOUND, null, null, ['param' => "素材"]);
        }
        $temp_link_url = \Yii::$app->params['media']['use']['temp_link_url'];
        $sn = Acl::getTempSnByMid($media_id);
        $media = $media->toArray(['id', 'name', 'type_id', 'cover_url', 'url', 'duration', 'size', 'tags', 'des', 'visit_count', 'download_count', 'created_at', 'updated_at']);
        $media['url'] = "$temp_link_url?sn={$sn}";

        return new Response(Response::CODE_COMMON_OK, null, $media);
    }

}
