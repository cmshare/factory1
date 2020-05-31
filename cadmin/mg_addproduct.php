<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$referer=strtolower($_SERVER['HTTP_REFERER']);
if(strpos($referer,'mg_leftnav.php')>0) $editor_page='mg_editproduct.php';		  
else if(strpos($referer,'mg_shareproduct.php')>0) $editor_page='mg_editshare.php';	
else PageReturn('无效入口！',0);

$conn->exec('lock tables mg_product write'); 

$set1='supplier=\''.$AdminUsername.'\',recommend=-1,addtime=unix_timestamp()';
$set2='name=null,description=null,brand=0,cids=null,solded=0,weight=0,onsale=0,price0=0';

//查找可用的记录
$ProductID=$conn->query("select id from mg_product where recommend=-1 and addtime<(unix_timestamp()-12*60*60) order by addtime asc limit 1")->fetchColumn(0);
if($ProductID){
  $ret=$conn->exec("update mg_product set $set1,$set2 where id=$ProductID");
}
else{
  $count=$conn->query("select count(*) from mg_product where recommend=-1 and supplier='$AdminUsername'")->fetchColumn(0); 
  if($count<8){//一个管理员最大允许有8个商品同时编辑
    if($conn->exec('inert into mg_product set '.$set1))$ProductID=$conn->query('select last_insert_id()');
  }
  else{//查找可用的记录
    $ProductID=$conn->query("select id from mg_product where recommend=-1 and supplier='$AdminUsername' order by addtime asc  limit 1")->fetchColumn(0);
    if($ProductID) $conn->exec("update mg_product set $set1,$set2 where id=$ProductID");
  }
}
$conn->exec('unlock tables');  
if(@$ProductID) header("Location: $editor_page?id=$ProductID");
else echo 'erro!';
db_close();
exit(0);?>
