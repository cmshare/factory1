<?php require('../include/conn.php');
$pid=$_POST['pid'];
if(is_numeric($pid)){
  OpenDB();
  $row=$conn->query('select id,stock0 from `mg_product` where id='.$pid,PDO::FETCH_NUM)->fetch();
  if($row) echo $row[0].'|'.$row[1];
  CloseDB();
}?>
