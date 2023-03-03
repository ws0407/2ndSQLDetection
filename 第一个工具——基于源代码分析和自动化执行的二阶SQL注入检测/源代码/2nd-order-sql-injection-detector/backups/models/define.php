<?php

function getFirstScanResult(){
    $arr = [
        "conn"=>["php", [], "sql"],
        "name"=>["web", ["_POST"], "sql"],
        "_POST"=>["web", [], "sql"],
        "pass"=>["web", ["_POST"], "sql"],
        "username"=>["web", ["conn", "name", ], "sql", "user(username)"],
        "password"=>["web", ["conn", "pass", ], "sql", "user(password)"],
        "sql"=>["sql", [], ""],
        "result"=>["sql", ["conn", "sql", ], "" ],
        "sql1"=>["", [], "sql"]
    ];
    return $arr;
}

function getSecondScanResult(){
    $arr = [
        "username"=>["sql", ["_SESSION"], "sql", "user(username)", "user(username)"],
        "_SESSION"=>["sql", [], "sql"],
        "_POST"=>["web", [], "sql"],
        "conn"=>["php" ,[], "sql"],
        "old_pass"=>["web", ["_POST"], "sql"],
        "new_pass"=>["web", ["_POST"], "sql"],
        "old_password"=>["web", ["conn", "old_pass"], "sql", "user(password)"],
        "new_password"=>["web", ["conn", "new_pass"], "sql", "user(password)"],
        "sql"=>["", [] ,""],
        "result"=>["", ["conn", "sql", ],""],
    ];
    return $arr;
}

function getFileName($arr){
    if($arr["sql"][0] == "sql")
        return "register.php";
    return "change.php";
}
