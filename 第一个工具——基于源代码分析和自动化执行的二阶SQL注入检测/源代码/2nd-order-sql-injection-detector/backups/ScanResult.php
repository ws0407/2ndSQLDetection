<?php

require_once 'models/define.php';
require_once 'function.php';
require_once 'Require.php';

$first_scan_result = getFirstScanResult();

echoScanArray($first_scan_result);
echo "\n";

$second_scan_result = getSecondScanResult();

echoScanArray($second_scan_result);
echo "\n";

compareTwoResults($first_scan_result, $second_scan_result);

