<?php
define('UP_LOCKGRADE',1);
$UpgradeDepositDemand=array(0,200,500,5000);
$UpgradeConsumeDemand=array(0,500,5000,30000);
 
function UserPermitGrade($userid){
  global $conn,$UpgradeDepositDemand,$UpgradeConsumeDemand;
  $row=$conn->query('select username,deposit,grade,property from mg_users where id='.$userid,PDO::FETCH_ASSOC)->fetch();
  if(empty($row))return 0;
  $Is_GradeLocked=$row['property']&(1<<UP_LOCKGRADE);
  if($Is_GradeLocked) return $row['grade'];
  if($row['deposit']>=$UpgradeDepositDemand[3]) $UserPermitGrade=4;
  else if($row['deposit']>=$UpgradeDepositDemand[2]) $UserPermitGrade=3;
  else if($row['deposit']>=$UpgradeDepositDemand[1]) $UserPermitGrade=2;
  else $UserPermitGrade=1;
  if($UserPermitGrade>0 && $UserPermitGrade<4){
     $upgrade_username=$row['username'];
     $totalConsume=$conn->query('select sum(totalprice) from mg_orders where username=\''.$upgrade_username.'\' and state>4')->fetchColumn(0);
     if(!is_numeric($totalConsume))$totalConsume=0;

       if($totalConsume>=$UpgradeConsumeDemand[3]) $UserPermitGrade=4;
       else if($UserPermitGrade<3){
         if($totalConsume>=$UpgradeConsumeDemand[2]) $UserPermitGrade=3;
       }
       else if($UserPermitGrade<2){ 
   	     if($totalConsume>=$UpgradeConsumeDemand[1]) $UserPermitGrade=2;
       }
  }
  if($UserPermitGrade==4){
     $existid=$conn->query('select mg_admins.id from mg_admins inner join mg_users on mg_admins.username=mg_users.username where mg_users.id='.$userid)->fetchColumn(0);
     if($existid) $UserPermitGrade=3;
  }
  return $UserPermitGrade;
}
  

function ChangeUserGrade($UserID,$NewGrade){
  global $conn;
  if($UserID>0 && $NewGrade>0 && $conn->exec('update mg_users set grade='.$NewGrade.' where id='.$UserID)){
    #重置交易中订单的价格
    $orders=$conn->query('select mg_orders.id,mg_orders.ordername,mg_orders.totalprice,mg_orders.deliveryfee,mg_orders.adjust from mg_orders inner join mg_users on mg_orders.username=mg_users.username where mg_orders.state>0 and mg_orders.state<3 and mg_users.id='.$UserID);
    foreach($orders as $order){
       $res=$conn->query('select id,productid,price from mg_ordergoods where ordername=\''.$order['ordername'].'\'');
       $changecounter=0;
       foreach($res as $row){
 	  $row_2=$conn->query('select price0,price'.$NewGrade.',onsale from mg_product where id='.$row['productid'],PDO::FETCH_ASSOC)->fetch();
          if($row_2){
             $myprice=$row_2['price'.$NewGrade];
             if(($row_2['onsale']&0xf)>0 && $NewGrade>2 && $row_2['onsale']>time() && $row_2['price0']<$myprice) $myprice=$row_2['price0'];
             if($row['price']!=$myprice){
               $conn->exec('update mg_ordergoods set price='.$myprice.' where id='.$row['id']);
               $changecounter++;
             }
          }
       }
       if($changecounter>0){
          $totalprice=$conn->query('select sum(price*amount) from mg_ordergoods where ordername=\''.$order['ordername'].'\'')->fetchColumn(0);
          if(!is_numeric($totalprice))$totalprice=0;
          $totalprice+=($order['deliveryfee']+$order['adjust']);
          $conn->exec('update mg_orders set totalprice='.$totalprice.' where id='.$order['id']);    
       }
    }
    return TRUE;
  }
  return FALSE;
}


