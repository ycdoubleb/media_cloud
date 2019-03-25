<?php

namespace apiend\modules\v1\actions\dir;

use apiend\models\Response;
use apiend\modules\v1\actions\BaseAction;
use common\models\media\Dir;

/**
 * 获取目录详细
 *
 * @author Administrator
 */
class GetDetailAction extends BaseAction {

    public function run() {
        if (!$this->verify()) {
            return $this->verifyError;
        }
        $post = $this->getSecretParams();
        //指定目录ID
        $dir_id = $this->getSecretParam('dir_id', 0);
        //指定素材库
        $category_id = $this->getSecretParam('category_id');

        /* 检查必须参数 */
        $notfounds = $this->checkRequiredParams($post, ['category_id']);
        if (count($notfounds) > 0) {
            return new Response(Response::CODE_COMMON_MISS_PARAM, null, null, ['param' => implode(',', $notfounds)]);
        }
        /* 目录为空时返回根目录 */
        if ($dir_id == '') {
            $dir_id = 0;
        }
        $dir = Dir::getDirById($dir_id);

        if ($dir == null || ($dir->is_del == 1)) {
            /* 找不到目录或者目录已经删除 */
            return new Response(Response::CODE_COMMON_NOT_FOUND, null, null, ['param' => '目录']);
        }

        /* 子目录 */
        $children = Dir::find()
                ->select(['id', 'name'])
                ->where(['category_id' => $category_id, 'parent_id' => $dir_id, 'is_del' => 0])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all();

        $path = $dir->getParents(['id', 'name'], true);
        $dir = $dir->toArray(['id', 'name', 'level', 'path']);
        $dir['path'] = $path;

        return new Response(Response::CODE_COMMON_OK, null, [
            'dir' => $dir,
            'children' => $children,
        ]);
    }

}
