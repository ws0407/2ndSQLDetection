<?php
require_once 'function.php';

require_once '../vendor/nikic/php-parser/lib/PhpParser/Parser.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/ParserAbstract.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/PrettyPrinterAbstract.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Builder.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Lexer.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Comment.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/ErrorHandler.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/NodeAbstract.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/NodeVisitor.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/NodeVisitorAbstract.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Expr.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Name.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Stmt.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Stmt/TraitUseAdaptation.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Scalar.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Scalar/MagicConst.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Expr/Assign.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Expr/AssignOp.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Expr/BinaryOp.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/Expr/Cast.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Node/FunctionLike.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/NodeTraverserInterface.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Builder/Declaration.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Builder/FunctionLike.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Lexer/Emulative.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Lexer/TokenEmulator/TokenEmulator.php';
require_once '../vendor/nikic/php-parser/lib/PhpParser/Lexer/TokenEmulator/KeywordEmulator.php';

require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/processors/AbstractProcessor.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/processors/SetProcessor.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/Builder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/processors/OrderByProcessor.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/IndexTypeBuilder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/WhereBracketExpressionBuilder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/WhereBuilder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/WhereExpressionBuilder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/builders/ReservedBuilder.php';
require_once '../vendor/PHP-SQL-Parser-master/src/PHPSQLParser/processors/ExplainProcessor.php';

fetchDir("../vendor/nikic/php-parser/lib/PhpParser");
fetchDir("../vendor/PHP-SQL-Parser-master/src/PHPSQLParser");
