<?php
  $adminuser=$_COOKIE['meray']['admin'];
  if($adminuser){
    setcookie('meray[admin]','');
    include("includes/dbconn.php");
    db_open();
    $conn->exec('insert into mg_logs set type=2,username=\''.$adminuser.'\',remark=null,addtime=unix_timestamp()');  
    db_close();
  }
  echo '<script language="javascript">top.location.href=".";</script>';
?>
