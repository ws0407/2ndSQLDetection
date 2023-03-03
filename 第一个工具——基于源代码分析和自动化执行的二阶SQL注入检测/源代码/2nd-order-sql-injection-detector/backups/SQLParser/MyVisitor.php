<?php

require_once '../Require.php';
require_once '../../vendor/autoload.php';

use PhpMyAdmin\SqlParser\Parser;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;


class SqlVisitor extends NodeVisitorAbstract {
    public function enterNode(Node $node)
    {
        // 寻找sql语句
        if ($node instanceof Encapsed)
        {
            // 对语句的内容进行分离
            $sql_array = $node->parts;
            $sql_string = "";
            // 生成sql语句
            foreach ($sql_array as $sql){
                if ($sql instanceof EncapsedStringPart){
                    $sql_string = $sql_string . $sql->value;
                    echo $sql->value . "\n";
                }

                // 获取sql语句中的变量
                if ($sql instanceof Variable){
                    $sql_string = $sql_string . $sql->name;
                    echo $sql->name . "\n";
                }
            }
            echo "\n";
            $sql_parser = new Parser($sql_string);
            $stmt0 = $sql_parser->statements[0];
            echo $stmt0->build() . "\n\n";
            //echo var_dump($stmt0) . "\n";
            echo var_dump($stmt0);
            $array = $stmt0->getClauses();
            foreach($array as $arr) {
                echo var_dump($arr) . "\n";
            }
            //sql_string中保存了没有变量的sql语句，识别sql语句关键字以获取变量的来源或去向
            $from_pos = strpos(strtolower($sql_string), "from");
            $where_pos = strpos(strtolower($sql_string), "where");
            $equal_pos = strpos(strtolower($sql_string), "=");

            if ($from_pos != false && $where_pos != false) {
                // 获取表名
                $table_name = substr($sql_string, $from_pos + 5, $where_pos - $from_pos - 6);
                echo "表名" . $table_name . "\n";

                // 获取表项
                $tag1 = substr($sql_string, $where_pos + 6, $equal_pos - $where_pos - 7);
                echo "表项" . $tag1 . "\n";
            }
        }
    }
}
