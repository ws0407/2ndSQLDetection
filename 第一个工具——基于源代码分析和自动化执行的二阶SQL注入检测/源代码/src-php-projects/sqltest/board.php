<?php
/**
 * Created by runner.han
 * There is nothing new under the sun
 */


$SELF_PAGE = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);

if ($SELF_PAGE = "board.php"){
    $ACTIVE = array('','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','active open','','','','','','','active','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
}

include_once "config.inc.php";
include_once "function.php";
include_once "mysql.inc.php";


$link=connect();
$html='';
//没有对传进来的message进行处理，导致INSERT注入
if(array_key_exists("message",$_POST) && $_POST['message']!=null){
    $message=$_POST['message'];
    $query="insert into message(content,time) values('$message',now())";
    $result=execute($link, $query);
    if(mysqli_affected_rows($link)!=1){
        $html.="<p>出现异常，提交失败！</p>";
    }
}


//没对传进来的id进行处理，导致DEL注入
if(array_key_exists('id', $_GET)){
    $query="delete from message where id={$_GET['id']}";
    $result=execute($link, $query);
    if(mysqli_affected_rows($link)==1){
        header("location:board.php");
    }else{
        $html.="<p style='color: red'>删除失败</p>";
    }
}


?>





<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li class="active">该页面存在delete注入、XSS</li>
            </ul><!-- /.breadcrumb -->
            <a href="#" style="float:right" data-container="body" data-toggle="popover" data-placement="bottom" title="tips(再点一下关闭)"
               data-content="删除出现问题">
            </a>


        </div>
        <div class="page-content">

            <div id="sqli_del_main">
                <p class="sqli_del_title">留言板：</p>
                <form method="post">
                    <textarea class="sqli_del_in" name="message"></textarea><br />
                    <input class="sqli_del_submit" type="submit" name="submit" value="submit" />
                </form>
                <?php echo $html;?>
                <br />
                <div id="show_message">
                    <p class="line">留言列表：</p>

                    <?php
                    $query="select * from message";
                    $result=execute($link, $query);
                    while($data=mysqli_fetch_assoc($result)){
                        $content=$data['content'];//未对显示内容进行转义
                        echo "<p class='con'>{$content}</p><a href='board.php?id={$data['id']}'>删除</a>";
                    }
                    ?>
                </div>
            </div>
            <a href="index.php">回到查询界面</a>



        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->

