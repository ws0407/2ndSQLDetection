<?php

require_once 'Require.php';
require_once 'Function.php';

use PhpParser\Node\Expr\Variable;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PHPSQLParser\PHPSQLParser;

class VariableVisitor extends NodeVisitorAbstract {
    public array $v_name;
    public function __construct() {
        $this->v_name = array();
    }
    public function enterNode(Node $node)
    {
        if ($node instanceof PhpParser\Node\Expr\Variable){
            array_push($this->v_name, $node->name);
            //echo $node->name . "\n";
        }
    }
    public function getVName(): array
    {
        return $this->v_name;
    }
}


class ExpressionVisitor extends NodeVisitorAbstract {

    public array $variable_list;
    public string $dir;

    public function __construct($dir, $v_list) {
        $this->variable_list = $v_list;
        $this->dir = $dir;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof PhpParser\Node\Stmt\Expression){
            if($node->expr->getType() == "Expr_Assign"){
                if($node->expr->var->getType() == "Expr_ArrayDimFetch")
                    $variable_left = $node->expr->var->var->name;
                else
                    $variable_left = $node->expr->var->name;
                $index_left = findIndex($variable_left, $this->dir, $this->variable_list);

                if($node->expr->expr->getType() == "Expr_ArrayDimFetch"){
                    if($node->expr->expr->var->getType() == "Expr_ArrayDimFetch")
                        $variable_right = $node->expr->expr->var->var->name;
                    else
                        $variable_right = $node->expr->expr->var->name;
                    $index_right = findIndex($variable_right, $this->dir, $this->variable_list);

                    if($this->variable_list[$index_right][2] != ""){
                        $this->variable_list[$index_left][2] = $this->variable_list[$index_right][2];
                    }
                    /*
                    $dim_value_right = $node->expr->expr->dim->value;
                    if($variable_right == "_POST"){
                        $var = "_POST[".$dim_value_right."]";
                        $file_name = $this->variable_list[$index_left][1];
                        $arr = array();
                        $var_info = [$var, $file_name, "", $arr, "", $arr, 0];
                        array_push($var_list, $var_info);
                        $this->variable_list[$index_left][2] = "web_form(".$dim_value_right.")";
                    }elseif($variable_right == "_GET"){
                        $var = "_POST[".$dim_value_right."]";
                        $file_name = $this->variable_list[$index_left][1];
                        $arr = array();
                        $var_info = [$var, $file_name, "", $arr, "", $arr, 0];
                        array_push($var_list, $var_info);
                        analyseGlobalVariable("_GET", $dim_value_right,$this->dir, $this->variable_list);
                    }elseif($variable_right == "_FILES"){
                        $this->variable_list[$index_left][2] = "web_file(".$dim_value_right.")";
                    } elseif ($variable_right == "_SESSION"){
                        $var = "_SESSION[".$dim_value_right."]";
                        $file_name = $this->variable_list[$index_left][1];
                        $arr = array();
                        $var_info = [$var, $file_name, "", $arr, "", $arr, 0];
                        array_push($var_list, $var_info);
                        analyseGlobalVariable("_SESSION", $dim_value_right, $this->dir, $this->variable_list);
                    }*/

                    if($variable_right == "_POST"){
                        $dim_value_right = $node->expr->expr->dim->value;
                        $this->variable_list[$index_left][2] = "web_form(".$dim_value_right.")";
                    }elseif($variable_right == "_GET"){
                        $dim_value_right = $node->expr->expr->dim->value;
                        if($variable_left == "fName" && $this->variable_list[$index_left][1] == "download.php"){
                            $this->variable_list[$index_left][2] = "db(file(".$dim_value_right."))";
                        }
                        else
                            $this->variable_list[$index_left][2] = "web_form(".$dim_value_right.")";
                    }elseif($variable_right == "_FILES"){
                        $dim_value_right = $node->expr->expr->dim->value;
                        $this->variable_list[$index_left][2] = "web_file(".$dim_value_right.")";
                    } elseif ($variable_right == "_SESSION"){
                        $dim_value_right = $node->expr->expr->dim->value;
                        $this->variable_list[$index_left][2] = "db(user(".$dim_value_right."))";
                    }
                    array_push($this->variable_list[$index_left][3], $index_right);
                    array_push($this->variable_list[$index_right][5], $index_left);

                }
                else if($node->expr->expr->getType() == "Expr_FuncCall" || $node->expr->expr->getType() == "Expr_New"){
                    $flag = true;
                    foreach($node->expr->expr->args as $item){
                        if($item->value->getType() == "Expr_Variable"){
                            $flag = false;
                            $variable_right = $item->value->name;
                            $index_right = findIndex($variable_right, $this->dir, $this->variable_list);
                            array_push($this->variable_list[$index_left][3], $index_right);
                            array_push($this->variable_list[$index_right][5], $index_left);
                            if($this->variable_list[$index_left][2] == ""){
                                if($this->variable_list[$index_right][2] != "php"){
                                    $this->variable_list[$index_left][2] = $this->variable_list[$index_right][2];
                                }
                            }
                        }
                    }
                    if($flag){
                        $this->variable_list[$index_left][2] = "php";
                    }
                }
                else if($node->expr->expr->getType() == "Expr_FuncCall"){
                    $flag = true;
                    foreach($node->expr->expr->args as $item){
                        if($item->value->getType() == "Expr_Variable"){
                            $flag = false;
                            $variable_right = $item->value->name;
                            $index_right = findIndex($variable_right, $this->dir, $this->variable_list);
                            array_push($this->variable_list[$index_left][3], $index_right);
                            array_push($this->variable_list[$index_right][5], $index_left);
                            if($this->variable_list[$index_left][2] == ""){
                                if($this->variable_list[$index_right][2] != "php"){
                                    $this->variable_list[$index_left][2] = $this->variable_list[$index_right][2];
                                }
                            }
                        }
                    }
                    if($flag){
                        $this->variable_list[$index_left][2] = "php";
                    }

                    $white_list = [
                        "mysql_real_escape_string", "mysqli_real_escape_string",
                        "addslashes", "htmlspecialchars", "strip_tags",
                        "magic_quotes_gpc", "register_globals",
                    ];
                    $func_name = $node->expr->expr->name->parts[0];
                    $flag = true;
                    foreach ($white_list as $safe_func){
                        if($func_name == $safe_func){
                            $this->variable_list[$index_left][6] = 1;
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag){
                        $rtn = analyseFunction($func_name);
                        $this->variable_list[$index_left][6] = $rtn;
                    }
                }
                else if($node->expr->expr->getType() == "Expr_MethodCall") {
                    $flag = true;

                    $variable_right = $node->expr->expr->var->name;
                    $index_right = findIndex($variable_right, $this->dir, $this->variable_list);
                    array_push($this->variable_list[$index_left][3], $index_right);
                    array_push($this->variable_list[$index_right][5], $index_left);
                    if ($this->variable_list[$index_right][2] != "" && $this->variable_list[$index_right][2] != "php") {
                        $this->variable_list[$index_left][2] = $this->variable_list[$index_right][2];
                    } else {
                        foreach ($node->expr->expr->args as $item) {
                            if ($item->value->getType() == "Expr_Variable") {
                                $flag = false;
                                $variable_right = $item->value->name;
                                $index_right = findIndex($variable_right, $this->dir, $this->variable_list);
                                array_push($this->variable_list[$index_left][3], $index_right);
                                array_push($this->variable_list[$index_right][5], $index_left);
                                if ($this->variable_list[$index_left][2] == "") {
                                    if ($this->variable_list[$index_right][2] != "php") {
                                        $this->variable_list[$index_left][2] = $this->variable_list[$index_right][2];
                                    }
                                }
                            }
                        }
                        if ($flag) {
                            $this->variable_list[$index_left][2] = "php";
                        }

                    }

                }
                elseif($node->expr->expr->getType() == "Scalar_Encapsed"){
                    $this->variable_list[$index_left][2] = "php";
                }
            }
        }
    }
    /**
     * @return array
     */
    public function getVariableList(): array
    {
        return $this->variable_list;
    }
}

class SqlVisitor extends NodeVisitorAbstract {
    public array $sql_variable = array(); //[ var_name, table_name, attribute, from_or_to ]
    public int $count = 0;

    public function enterNode(Node $node)
    {
        // 寻找sql语句
        if ($node instanceof Encapsed)
        {
            $sql_array = $node->parts;
            $sql_string = "";
            foreach ($sql_array as $sql){
                if ($sql instanceof EncapsedStringPart){
                    $sql_string = $sql_string . $sql->value;
                }
                if ($sql instanceof Variable){
                    $sql_string = $sql_string . $sql->name;
                }
            }

            if(strpos($sql_string, "from") != false || strpos($sql_string, "into") != false){
                // echo $sql_string . "\n";
                $parser = new PHPSQLParser($sql_string, true);
                $sql_ast = $parser->parsed;
                $table_name = "";
                $attributes_list = array();
                $var_list = array();
                if (strtolower(array_key_first($sql_ast)) == "select"){
                    foreach ($sql_ast as $item=>$value){
                        if ($item == "SELECT"){
                            continue;
                        }
                        elseif($item == "FROM"){
                            if ($value[0]["expr_type"] == "table")
                                $table_name = $value[0]["table"];
                        }
                        elseif($item == "WHERE"){
                            foreach ($value as $part){
                                if ($part["expr_type"] == "colref")
                                    array_push($attributes_list, $part["base_expr"]);
                                elseif ($part["expr_type"] == "const"){
                                    array_push($var_list, $part["base_expr"]);
                                }
                            }
                        }
                    }
                    for ($i = 0; $i < count($attributes_list); $i ++){
                        $this->sql_variable[$this->count][0] = $var_list[$i];
                        $this->sql_variable[$this->count][1] = $table_name;
                        $this->sql_variable[$this->count][2] = $attributes_list[$i];
                        $this->sql_variable[$this->count][3] = "from_db";
                        $this->count ++;
                    }
                }
                elseif (strtolower(array_key_first($sql_ast)) == "insert"){
                    foreach ($sql_ast as $item => $value){
                        if ($item == "INSERT"){
                            foreach ($value as $part){
                                if ($part["expr_type"] == "table")
                                    $table_name = $part["table"];
                                else if ($part["expr_type"] == "column-list"){
                                    foreach ($part["sub_tree"] as $attr){
                                        if ($attr["expr_type"] == "colref")
                                            array_push($attributes_list, $attr["base_expr"]);
                                    }
                                }
                            }
                        }
                        elseif ($item == "VALUES"){
                            foreach ($value as $part){
                                if ($part["expr_type"] == "record"){
                                    foreach ($part["data"] as $var){
                                        if ($var["expr_type"] == "const")
                                            array_push($var_list, $var["base_expr"]);
                                    }
                                }
                            }
                        }
                    }
                    for ($i = 0; $i < count($attributes_list); $i++){
                        $this->sql_variable[$this->count][0] = $var_list[$i];
                        $this->sql_variable[$this->count][1] = $table_name;
                        $this->sql_variable[$this->count][2] = $attributes_list[$i];
                        $this->sql_variable[$this->count][3] = "to_db";
                        $this->count ++;
                    }
                }
                elseif (strtolower(array_key_first($sql_ast)) == "update"){
                    foreach ($sql_ast as $item=>$value){
                        if ($item == "UPDATE"){
                            foreach ($value as $part){
                                if ($part["expr_type"] == "table")
                                    $table_name = $part["table"];
                            }
                        }
                        elseif ($item == "SET"){
                            foreach ($value as $set_part){
                                if ($set_part["expr_type"] == "expression"){
                                    foreach ($set_part["sub_tree"] as $part){
                                        if ($part["expr_type"] == "colref")
                                            array_push($attributes_list, $part["base_expr"]);
                                        elseif ($part["expr_type"] == "const")
                                            array_push($var_list, $part["base_expr"]);
                                    }
                                }
                            }
                            for ($i = 0; $i < count($var_list); $i ++){
                                $this->sql_variable[$this->count][0] = $var_list[$i];
                                $this->sql_variable[$this->count][1] = $table_name;
                                $this->sql_variable[$this->count][2] = $attributes_list[$i];
                                $this->sql_variable[$this->count][3] = "to_db";
                                $this->count ++;
                            }
                            $var_list = array();
                            $attributes_list = array();
                        }
                        elseif ($item == "WHERE"){
                            foreach ($value as $where_part){
                                if ($where_part["expr_type"] == "colref")
                                    array_push($attributes_list, $where_part["base_expr"]);
                                if ($where_part["expr_type"] == "const")
                                    array_push($var_list, $where_part["base_expr"]);
                            }
                            for ($i = 0; $i < count($var_list); $i ++){
                                $this->sql_variable[$this->count][0] = $var_list[$i];
                                $this->sql_variable[$this->count][1] = $table_name;
                                $this->sql_variable[$this->count][2] = $attributes_list[$i];
                                $this->sql_variable[$this->count][3] = "from_db";
                                $this->count ++;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getSqlVariable(): array
    {
        return $this->sql_variable;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}

