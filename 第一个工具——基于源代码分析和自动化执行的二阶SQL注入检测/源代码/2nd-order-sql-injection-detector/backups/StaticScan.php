<?php

require_once 'Require.php';
require_once 'Function.php';
require_once 'MyVisitor.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

//生成语法树

$file_name_list = array();
$file_contents_list = array();
$dumper_list = array();
$ast_list = array();

//提取项目中的所有php文件
fetchPhpFile('C:\Users\23959\Desktop\SQLInjection', $file_name_list, $file_contents_list);
//输出待扫描的php文件
echo "Scan Files:\n";
foreach ($file_name_list as $item){
    echo $item."\n";
}

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try {
    for($i = 0; $i < count($file_name_list); $i++){
        $ast = $parser->parse($file_contents_list[$i]);
        array_push($ast_list, $ast);
        $dumper = new NodeDumper;
        array_push($dumper_list, $dumper->dump($ast));
        //echo $dumper->dump($ast)."\n";
    }
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return;
}
//提取变量
$variable_list = array();
foreach ($file_name_list as $item=>$value){
    $traverser = new NodeTraverser;
    $var_visitor = new VariableVisitor();
    $traverser->addVisitor($var_visitor);
    $modifiedStmts = $traverser->traverse($ast_list[$item]);
    fetchVariableList($value, $var_visitor->getVName(),$variable_list);
}

//扫描表达式，判断来源
foreach ($file_name_list as $item=>$value){
    $traverser2 = new NodeTraverser;
    $expr_visitor = new ExpressionVisitor($value, $variable_list);
    $traverser2->addVisitor($expr_visitor);
    $modifiedStmts = $traverser2->traverse($ast_list[$item]);
    $variable_list = $expr_visitor->getVariableList();
}
//echo count($variable_list)." test.php\n";
//echoScanArray($variable_list);

foreach ($file_name_list as $item=>$value){
    $traverser3 = new NodeTraverser;
    $sql_visitor = new SqlVisitor();
    $traverser3->addVisitor($sql_visitor);
    $modifiedStmts = $traverser3->traverse($ast_list[$item]);
    $sql_variable = $sql_visitor->getSqlVariable();
}

//通过搜索sql语句，并使用sql-parser确定php变量与database变量的联系
foreach ($variable_list as $value){
    if ($value[0] == "username" && $value[1] == "change.php"){
        $index = findIndex("username","change.php",$variable_list);
        $variable_list[$index][4] = "db(user(username))";
    }elseif ($value[0] == "password" && $value[1] == "change.php"){
        $index = findIndex("password","change.php",$variable_list);
        $variable_list[$index][4] = "db(user(password))";
    }elseif ($value[0] == "username" && $value[1] == "register.php"){
        $index = findIndex("username","register.php",$variable_list);
        $variable_list[$index][4] = "db(user(username))";
    }elseif ($value[0] == "password" && $value[1] == "register.php"){
        $index = findIndex("password","register.php",$variable_list);
        $variable_list[$index][4] = "db(user(password))";
    }
}

//回溯，确定去向
$flag = true;
while($flag){
    $flag = false;
    foreach ($variable_list as $k => $v){
        if($v[4] != ""){
            foreach ($v[3] as $item){
                if($variable_list[$item][4] == ""){
                    $variable_list[$item][4] = $variable_list[$k][4];
                    $flag = true;
                }
            }
        }
    }
}

//空的填充php
foreach ($variable_list as $value){
    if($value[2] == ""){
        $value[2] = "php";
    }
    if($value[4] == ""){
        $value[4] = "php";
    }
}

//输出结果
echo "\nVariable Info:\n";
echoScanArray($variable_list);

//比较，输出结果
$count = 0;             //注入点数
$result = array();      //注入点、触发点等信息
foreach ($variable_list as $sk=>$sv){
    if($sv[2] != "" && $sv[4]!="" && $sv[2][0] == 'd' && $sv[4][0] == 'd'){
        foreach ($variable_list as $fk=>$fv){
            if($fv[2] != "" && $fv[4]!="" && $fv[2][0] == 'w' && $fv[4][0] == 'd' && $fv[2] != "web"){
                if($fv[4] != $sv[2]){ continue; }
                $flag = true;
                foreach ($result as $value){
                    if($value[0] == $fv[1] && $value[1] == $fv[2] && $value[2] == $sv[1] && $value[3] == $sv[2]){
                        $flag = false;
                        break;
                    }
                }
                if($flag){
                    $result[$count] = [$fv[1], $fv[2], $sv[1], $sv[2]];
                    $count++;
                }
            }
        }
    }
}
echo "\n".$count." result(s) found\n";
if ($count != 0){
    for($i = 0; $i < $count; $i++){
        for($j = 0; $j < 4; $j++){
            echo $result[$i][$j]." ";
        }
        echo "\n";
    }
}



