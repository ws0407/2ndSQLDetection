<?php

require_once 'Require.php';
require_once 'function.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

//生成语法树

$file_name_list = array();
$file_contents_list = array();

//获取整个项目内所有的php文件
fetchPhpFile('C:\Users\23959\Desktop\SQLInjection', $file_name_list, $file_contents_list);

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

$parser_list = array();
try {
    //对于每个php文件，创建一颗语法分析树
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
//变量控制流，格式：["src",[父变量列],"dst", "若来自db，详细来源", "若去向db，详细去向"]组成的数组
$variable_list = array();
//AST内容
$contents = $parser_list[1];
//搜索变量
while(true){
    $pos = strpos($contents, "Expr_Variable");
    if($pos == false){ break; }
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
/*
foreach ($variable_list as $k => $v) {
    echo $k.": ".$v[0]." ".$v[1]."\n";
}
*/

$variable_list["sql"][0] = "sql";
$variable_list["sql1"][2] = "sql";

//确定来源
foreach ($parser->parse($file_contents_list[1]) as $k => $v){
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

            }else if($v->expr->expr->getType() == "Scalar_Encapsed"){
                //SQL语句的处理
            }

        }
    }

    /*
    if($v instanceof \PhpParser\Node\Stmt\Namespace_){
        foreach ($v->stmts as $kk => $vv){
        }

    }*/
}

//确定去向
//扫描sql1，确定了username去向为user(username)
$variable_list["username"][2] = "sql";
$variable_list["username"][3] = "user(username)";
$variable_list["password"][2] = "sql";
$variable_list["password"][3] = "user(password)";

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
