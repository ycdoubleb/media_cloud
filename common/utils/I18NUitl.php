<?php

namespace common\utils;

use Yii;

/**
 * Description of I18NUitl
 *
 * @author Administrator
 */
class I18NUitl {

    /**
     * Translates a message to the specified language.
     * 自动转换{name}为Yii::t('app',name)
     * @param string $category      app
     * @param string $message       I'am {name}
     */
    public static function t($category, $message, $params = []) {
        preg_match_all('/\{.+?\}/', $message, $matches);
        foreach ($matches[0] as &$matche) {
            $matche = str_replace(['{', '}'], '', $matche);
            if(!isset($params[$matche])){
                $params[$matche] = Yii::t($category, $matche);
            }
        }
        return Yii::t($category, $message, $params);
    }

}
