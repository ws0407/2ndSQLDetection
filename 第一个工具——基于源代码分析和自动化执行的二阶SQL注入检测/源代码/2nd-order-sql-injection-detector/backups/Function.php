<?php
function fetchDir($dir) {
    foreach(glob($dir.'/*') as $file) {
        if(is_dir($file)) {
            fetchDir($file);
        }else{
            require_once $file.'';
        }
    }
}

//获取所有php文件，文件名+文件内容
function fetchPhpFile($dir, &$array_name, &$array_contents) {
    foreach(glob($dir.'/*') as $file) {
        if(is_dir($file)) {
            fetchPhpFile($file, $array_name, $array_contents);
        }else{
            $file_name = basename($file);
            if(substr($file_name, -4) == '.php'){
                $handle = fopen($file, "r");
                $contents = fread($handle, filesize ($file));
                array_push($array_contents, $contents);
                array_push($array_name, $file_name);
                fclose($handle);
            }

        }
    }
}

function fetchVariableList($file_name, $var_list_tmp, &$var_list){
    foreach ($var_list_tmp as $var){
        //[var_name, var_dir, src(db,php,web), src_var, dst(db,php,web), dst_var, safe_level]
        $arr = array();
        $var_info = [$var, $file_name, "", $arr, "", $arr, 0];
        if($var == "_POST")
            $var_info[2] = "web";
        elseif ($var == "_SESSION")
            $var_info[2] = "db";
        $flag = false;
        for($i = 0; $i < count($var_list); $i++){
            if($var_list[$i][0] == $var && $var_list[$i][1] == $file_name){
                $flag = true;
            }
        }
        if($flag == false)
            array_push($var_list, $var_info);
    }
}

function echoScanArray($array){
    foreach ($array as $k => $v) {
        echo $k.": ".$v[0]." ".$v[1]." ".$v[2]." [";
        foreach ($v[3] as $vv){
            echo $vv.", ";
        }
        echo "] ".$v[4]." [";
        foreach ($v[5] as $vv){
            echo $vv.", ";
        }
        echo "] ".$v[6]."\n";
    }
}

function compareTwoResults($first, $second){
    $count = 0;             //注入点数
    $result = array();      //注入点、触发点等信息
    foreach ($second as $sk=>$sv){
        if($sv[0] == 'sql' && $sv[2] == 'sql'){
            if(count($sv) != 5) { continue; }
            foreach ($first as $fk=>$fv){
                if($fv[0] == 'web' && $fv[2] == 'sql'){
                    if(count($fv) != 4){ continue; }
                    if($fv[3] != $sv[3]){ continue; }
                    $count++;
                    foreach ($fv[1] as $bv){
                        if($first[$bv][0] != 'web'){
                            continue;
                        }
                        foreach ($first[$bv][1] as $lv){
                            if($first[$lv][0] != 'web'){ continue; }
                            $result[$count-1] = [ getFileName($first), $lv, getFileName($second), $fv[3]];
                        }
                    }
                }
            }
        }
    }
    echo $count." result(s) found\n";
    if ($count != 0){
        for($i = 0; $i < $count; $i++){
            for($j = 0; $j < 4; $j++){
                //echo count($result[0]);
                echo $result[$i][$j]." ";
            }
            echo "\n";
        }
    }

}
