<?php

namespace common\utils;

use Yii;

/**
 * mysql 工具类
 *
 * @author Administrator
 */
class MysqlUtil {

    /**
     * 创建批量插入(数据重复时更新指定字段)
     * @param string $table             表名     eg: %_t 
     * @param array $insertColumns      插入字段 eg:[id,name,nickname]
     * @param array $rows               插入数据 eg;[[1,2,3],[1,2,3]]
     * @param array $updateColumns      更新字段 eg:[name,nickname]
     * 
     * eg: 
        INSERT INTO table (id, col1, col2)
            VALUES (1, 1, 1), (2, 2, 2), (3, 3, 3), (4, 4, 4)
        ON DUPLICATE KEY UPDATE
            col1 = VALUES(col1),
            col2 = VALUES(col2);
     */
    public static function createBatchInsertDuplicateUpdateSQL($table, $insertColumns, $rows, $updateColumns) {
        if(count($rows) == 0){
            return  '';
        }
        //解析表名
        $table = Yii::$app->db->quoteSql($table);
        $insertColumns = implode(',', $insertColumns);
        //插入数据
//        $values = [];
//        foreach($rows as $row){
//            $values []= "(".implode(',', $row).")";
//        }
//        $values = implode(',', $values);
        
        $schema = Yii::$app->db->getSchema();
        $values = [];
        foreach ($rows as $row) {
            $vs = [];
            foreach ($row as $i => $value) {
                if (is_string($value)) {
                    $value = $schema->quoteValue($value);
                } elseif (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    $value = StringHelper::floatToString($value);
                } elseif ($value === false) {
                    $value = 0;
                } elseif ($value === null) {
                    $value = 'NULL';
                }
                $vs[] = $value;
            }
            $values[] = '(' . implode(', ', $vs) . ')';
        }
        $values = implode(',', $values);
        
        
        //更新的字段
        $updateValues = [];
        foreach($updateColumns as $updateColumn){
            $updateValues []= "$updateColumn = VALUES($updateColumn)";
        }
        $updateValues = implode(',', $updateValues);
        
        $sql = "INSERT INTO $table ($insertColumns) VALUES $values ON DUPLICATE KEY UPDATE $updateValues";
        
        return $sql;
        
    }

}
