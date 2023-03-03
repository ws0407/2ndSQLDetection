<?php

require_once '../Require.php';
require_once 'MyVisitor.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

$code = <<<'CODE'
<?php
// 创建连接
$conn = new mysqli("127.0.0.1", "root", "123456", "sql_test");

// 检测连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

$name = $_POST["username"];
$pass = $_POST["password"];

$username = mysqli_real_escape_string($conn, $name);
$password = mysqli_real_escape_string($conn, $pass);

$sql = "select distinct * from user where username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0){
	echo "<script>alert('用户名已被注册！')</script>";
	echo "<script>window.location.href='register.html';</script>";
}

$sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('注册成功')</script>";
	echo "<script>window.location.href='index.html';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
CODE;

// 创建解析器、遍历器
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$traverser = new NodeTraverser();
$traverser->addVisitor(new SqlVisitor());
//$traverser->addVisitor(new VariableVisitor());

try {
    $ast = $parser->parse($code);
    $traverser->traverse($ast);
}catch (Error $error){
    echo "Parse error: {$error->getMessage()}\n";
    return;
}
echo strpos("Hello world!","shit");
//$dumper = new NodeDumper();
//echo $dumper->dump($ast) . "\n";    // dumper->dump()返回一个string类型变量

/*
select distinct * from user where username = '
username
'

SELECT DISTINCT * FROM user WHERE username = 'username'

object(PhpMyAdmin\SqlParser\Statements\SelectStatement)#1284 (17) {
  ["expr"]=>
  array(1) {
    [0]=>
    object(PhpMyAdmin\SqlParser\Components\Expression)#1286 (7) {
      ["database"]=>
      NULL
      ["table"]=>
      NULL
      ["column"]=>
      NULL
      ["expr"]=>
      string(1) "*"
      ["alias"]=>
      NULL
      ["function"]=>
      NULL
      ["subquery"]=>
      NULL
    }
  }
  ["from"]=>
  array(1) {
    [0]=>
    object(PhpMyAdmin\SqlParser\Components\Expression)#1287 (7) {
      ["database"]=>
      NULL
      ["table"]=>
      NULL
      ["column"]=>
      NULL
      ["expr"]=>
      string(4) "user"
      ["alias"]=>
      NULL
      ["function"]=>
      NULL
      ["subquery"]=>
      NULL
    }
  }
  ["index_hints"]=>
  NULL
  ["partition"]=>
  NULL
  ["where"]=>
  array(1) {
    [0]=>
    object(PhpMyAdmin\SqlParser\Components\Condition)#1288 (3) {
      ["identifiers"]=>
      array(1) {
        [0]=>
        string(8) "username"
      }
      ["isOperator"]=>
      bool(false)
      ["expr"]=>
      string(21) "username = 'username'"
    }
  }
  ["group"]=>
  NULL
  ["having"]=>
  NULL
  ["order"]=>
  NULL
  ["limit"]=>
  NULL
  ["procedure"]=>
  NULL
  ["into"]=>
  NULL
  ["join"]=>
  NULL
  ["union"]=>
  array(0) {
  }
  ["end_options"]=>
  NULL
  ["options"]=>
  object(PhpMyAdmin\SqlParser\Components\OptionsArray)#1285 (1) {
    ["options"]=>
    array(1) {
      [1]=>
      string(8) "DISTINCT"
    }
  }
  ["first"]=>
  int(0)
  ["last"]=>
  int(16)
}
INSERT INTO user (username, password) VALUES ('
username
', '
password
')

INSERT INTO user(`username`, `password`) VALUES ('username', 'password')

object(PhpMyAdmin\SqlParser\Statements\InsertStatement)#1264 (8) {
  ["into"]=>
  object(PhpMyAdmin\SqlParser\Components\IntoKeyword)#1290 (7) {
    ["type"]=>
    NULL
    ["dest"]=>
    object(PhpMyAdmin\SqlParser\Components\Expression)#1291 (7) {
      ["database"]=>
      NULL
      ["table"]=>
      string(4) "user"
      ["column"]=>
      NULL
      ["expr"]=>
      string(4) "user"
      ["alias"]=>
      NULL
      ["function"]=>
      NULL
      ["subquery"]=>
      NULL
    }
    ["columns"]=>
    array(2) {
      [0]=>
      string(8) "username"
      [1]=>
      string(8) "password"
    }
    ["values"]=>
    NULL
    ["fields_options"]=>
    NULL
    ["fields_keyword"]=>
    NULL
    ["lines_options"]=>
    NULL
  }
  ["values"]=>
  array(1) {
    [0]=>
    object(PhpMyAdmin\SqlParser\Components\ArrayObj)#1292 (2) {
      ["raw"]=>
      array(2) {
        [0]=>
        string(10) "'username'"
        [1]=>
        string(10) "'password'"
      }
      ["values"]=>
      array(2) {
        [0]=>
        string(8) "username"
        [1]=>
        string(8) "password"
      }
    }
  }
  ["set"]=>
  NULL
  ["select"]=>
  NULL
  ["onDuplicateSet"]=>
  NULL
  ["options"]=>
  object(PhpMyAdmin\SqlParser\Components\OptionsArray)#1289 (1) {
    ["options"]=>
    array(0) {
    }
  }
  ["first"]=>
  int(0)
  ["last"]=>
  int(20)
}


 */






