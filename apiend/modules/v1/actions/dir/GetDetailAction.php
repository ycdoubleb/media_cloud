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
class GetDetailAction extends BaseAction
{

    protected $requiredParams = ['category_id'];

    public function run()
    {
        $post = $this->getSecretParams();
        //指定目录ID
        $dir_id = $this->getSecretParam('dir_id', 0);
        //指定素材库
        $category_id = $this->getSecretParam('category_id');

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
