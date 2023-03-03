<?php

require_once 'Require.php';
require_once 'function.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

//生成语法树

$file_name_list = array();
$file_contents_list = array();
$parser_list = array();

fetchPhpFile('C:\Users\23959\Desktop\SQLInjection', $file_name_list, $file_contents_list);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
//******

try {
    for($i = 0; $i < count($file_name_list); $i++){
        $ast = $parser->parse($file_contents_list[$i]);
        $dumper = new NodeDumper;
        array_push($parser_list, $dumper->dump($ast));
        //echo $dumper->dump($ast)."\n";
    }
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return;
}

//清洗数据


//提取变量
$variable_list = array();

$contents = $parser_list[0];

//搜索变量
while(true){
    $pos = strpos($contents, "Expr_Variable");
    if($pos == false){
        break;
    }
    $contents = substr($contents, $pos);
    $pos_name = strpos($contents, "name:");

    $contents = substr($contents, $pos_name+6);

    $pos_right = strpos($contents, "\n");
    $name = substr($contents, 0, $pos_right);

    if($name == "_POST"){
        $variable_list[$name] = ["web",[],""];
    }else if($name == "_SESSION"){
        $variable_list[$name] = ["sql",[], ""];
    }
    else{
        $variable_list[$name] = ["", [], ""];
    }
    $contents = substr($contents, $pos_right);
}

//此处获取到username的去向：
$variable_list["username"][2] = "sql";
$variable_list["username"][3] = "user(username)";
$variable_list["username"][4] = "user(username)";
$variable_list["new_password"][2] = "sql";
$variable_list["new_password"][3] = "user(password)";
$variable_list["old_password"][2] = "sql";
$variable_list["old_password"][3] = "user(password)";

//确定来源
foreach ($parser->parse($file_contents_list[0]) as $k => $v){
    if($v->getType() == "Stmt_Expression"){
        if($v->expr->getType() == "Expr_Assign"){
            $variable_left = $v->expr->var->name;
            if($v->expr->expr->getType() == "Expr_ArrayDimFetch"){
                $variable_right = $v->expr->expr->var->name;
                if($variable_list[$variable_right][0] != ""){
                    $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                    array_push($variable_list[$variable_left][1], $variable_right);
                }
            }else if($v->expr->expr->getType() == "Expr_FuncCall" || $v->expr->expr->getType() == "Expr_New"){
                $flag = true;
                foreach($v->expr->expr->args as $item){
                    if($item->value->getType() == "Expr_Variable"){
                        $flag = false;
                        $variable_right = $item->value->name;
                        array_push($variable_list[$variable_left][1], $variable_right);
                        if($variable_list[$variable_left][0] == ""){
                            if($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql"){
                                $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                            }
                        }
                    }
                }
                if($flag){
                    $variable_list[$variable_left][0] = "php";
                }
            }else if($v->expr->expr->getType() == "Expr_MethodCall"){
                $flag = true;

                $variable_right = $v->expr->expr->var->name;
                array_push($variable_list[$variable_left][1], $variable_right);
                if($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql"){
                    $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                }else{
                    foreach($v->expr->expr->args as $item){
                        if($item->value->getType() == "Expr_Variable"){
                            $flag = false;
                            $variable_right = $item->value->name;
                            array_push($variable_list[$variable_left][1], $variable_right);
                            if($variable_list[$variable_left][0] == "" ){
                                if($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql"){
                                    $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                                }
                            }
                        }
                    }
                    if($flag){
                        $variable_list[$variable_left][0] = "php";
                    }

                }

            }
        }
    }else if ($v->getType() == "Stmt_If"){
        foreach ($v->stmts as $vv){
            if($vv->getType() == "Stmt_Expression") {
                if ($vv->expr->getType() == "Expr_Assign") {
                    $variable_left = $vv->expr->var->name;
                    if ($vv->expr->expr->getType() == "Expr_ArrayDimFetch") {
                        $variable_right = $vv->expr->expr->var->name;
                        if ($variable_list[$variable_right][0] != "") {
                            $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                            array_push($variable_list[$variable_left][1], $variable_right);
                        }
                    } else if ($vv->expr->expr->getType() == "Expr_FuncCall" || $vv->expr->expr->getType() == "Expr_New") {
                        $flag = true;
                        foreach ($vv->expr->expr->args as $item) {
                            if ($item->value->getType() == "Expr_Variable") {
                                $flag = false;
                                $variable_right = $item->value->name;
                                array_push($variable_list[$variable_left][1], $variable_right);
                                if ($variable_list[$variable_left][0] == "") {
                                    if ($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql") {
                                        $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                                    }
                                }
                            }
                        }
                        if ($flag) {
                            $variable_list[$variable_left][0] = "php";
                        }
                    } else if ($vv->expr->expr->getType() == "Expr_MethodCall") {
                        $flag = true;

                        $variable_right = $vv->expr->expr->var->name;
                        array_push($variable_list[$variable_left][1], $variable_right);
                        if ($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql") {
                            $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                        } else {
                            foreach ($vv->expr->expr->args as $item) {
                                if ($item->value->getType() == "Expr_Variable") {
                                    $flag = false;
                                    $variable_right = $item->value->name;
                                    array_push($variable_list[$variable_left][1], $variable_right);
                                    if ($variable_list[$variable_left][0] == "") {
                                        if ($variable_list[$variable_right][0] == "web" || $variable_list[$variable_right][0] == "sql") {
                                            $variable_list[$variable_left][0] = $variable_list[$variable_right][0];
                                        }
                                    }
                                }
                            }
                            if ($flag) {
                                $variable_list[$variable_left][0] = "php";
                            }

                        }

                    }
                }
            }
        }
    }

}

//扫描sql，确定了username去向为user(username)

//确定去向
$flag = true;
while($flag){
    $flag = false;
    foreach ($variable_list as $k => $v){
        if($v[2] != ""){
            foreach ($v[1] as $item){
                if($variable_list[$item][2] == ""){
                    $variable_list[$item][2] = $variable_list[$k][2];
                    $flag = true;
                }
            }
        }
    }
}

echoScanArray($variable_list);