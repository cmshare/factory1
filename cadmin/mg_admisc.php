<?php require('includes/dbconn.php');
CheckLogin('MANAGE');
session_start();
$showcost=@$_SESSION['showcost'];
$arrearage=@$_SESSION['arrearage'];
$mode=@$_GET['mode'];
if($mode){
  OpenDB();
  switch($mode){
    case 'showcost':ShowCost();break;
    case 'arrearage':SwitchArrearage();break;
    case 'changeorderuser':ChangeOrderUser();break;
    case 'changeordersupport':ChangeOrderSupport();break;
    case 'checkurl':CheckURL();break;
    case 'costorder':CalOrderCost();break;
    case 'checkcost':CheckCost();break;
    case 'withdraworder':WithdrawOrder();break;
    case 'statgain':StatGain();break;
    case 'statonlinepay':StatOnlinepay();break;
    case 'resetprice':ResetOrderPrice();break;
    case 'sales':StatSales();break;
  }
  CloseDB();
  exit(0);
}
 
function StatSales(){
  global $conn;
  $CurrentDate=time(); 
  $StartDate=$conn->query('select min(actiontime) from mg_orders where state>3')->fetchColumn(0);
  if(empty($StartDate))PageReturn('没有销售记录！',0);
  $StartDate=strtotime(date('Y-m',$StartDate).'-1');
  echo '<table border=0><tr>';
  while($CurrentDate>$StartDate){
     $NextMonth=strtotime('+1 month',$StartDate);
     $totalsum=$conn->query('select sum(totalprice-deliveryFee) from mg_orders where state>3 and actiontime>'.$StartDate.' and actiontime<'.$NextMonth)->fetchColumn(0);
     if($totalsum===false)$totalsum=0;
     else $totalsum=round($totalsum/10000,1);
     echo '<tr><td>'.date('Y-m',$StartDate).'月</td><td>&nbsp; <font color="#FF0000">'.$totalsum.'</font>万</td></tr>';
     $StartDate=$NextMonth;
  }
  echo '</table>';
}


function ResetOrderPrice(){
  global $conn;
  $ordername=FilterText($_POST['ordername']);
  if($ordername){
    $row=$conn->query('select mg_orders.state,mg_users.grade from mg_orders inner join mg_users on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC)->fetch();
    if(empty($row)) PageReturn('订单号不存在，请核实！',0);
    else if($row['state']>1)PageReturn('此订单已经确认，无法重置价格！'	,0);
    else $usergrade=$row['grade'];		
  }
  else PageReturn('订单号为空！',0);
  $sql='update (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) set mg_ordergoods.price=(CASE WHEN (mg_product.onsale&0xf)>0 and '.$usergrade.'>2 and mg_product.onsale>unix_timestamp() and mg_product.price0<mg_product.price'.$usergrade.' THEN mg_product.price0 ELSE mg_product.price'.$usergrade.' END) where mg_ordergoods.ordername=\''.$ordername.'\''; 
  $count=$conn->exec($sql);
   if($count>0){
      $totalprice=$conn->query('select sum(price*amount) from mg_ordergoods where ordername=\''.$ordername.'\'')->fetchColumn(0);
      if($totalprice!==false){ 
        $conn->exec('update mg_orders set totalprice='.$totalprice.'+deliveryfee+adjust where ordername=\''.$ordername.'\' and state=1');
      }
   }
   PageReturn('完成订单价格重置，'.$count.'件商品价格被更改！',0);
}

function StatOnlinepay(){
  $AlipayContractBeginDate='2014-06-17 00:00';
  $AlipayContractDeadline='2015-06-17 00:00';
  $AlipayContractQuota=6; //单位:万元 
  
  $BeginDate=strtotime($AlipayContractBeginDate);
  $EndDate=strtotime($AlipayContractDeadline);
  $totamoney=AlipayPeriodStat($BeginDate,$EndDate,'')+AlipayPeriodStat($BeginDate,$EndDate,DB_HISTORY);
  echo '<b>网站集成支付宝，当前合同（'.$AlipayContractBeginDate.' —— '.$AlipayContractDeadline.'），可走合交易'.$AlipayContractQuota.'万,目前已经交易'.$totamoney.'万</b><br><hr>';
 
  $EndDate=time();
  for($i=0;$i<3;$i++){
    $BeginDate=strtotime("+1 month",$EndDate);
    if($i==0) $BeginDate=strtotime(date('Y-m',$EndDate).'-1');
    $totalmoney=AlipayPeriodStat($BeginDate,$EndDate,'');
    echo '支付宝自'.date('Y-m-d',$BeginDate).'至'.date('Y-m-d',$EndDate).'，走合流量'.$totalmoney.'元<br>';
    $EndDate=$BeginDate;
  }
}

function AlipayPeriodStat($BeginDate,$EndDate,$db){
  global $conn;
  $totalmoney=$conn->query('select sum(amount) from '.(($db)?$db.'.':'').'mg_onlinepay where state=4 and mode=1 and actiontime>'.$BeginDate.' and actiontime<'.$EndDate)->fetchColumn(0);
  return ($totalmoney===false)?0:round($totalmoney);
}

function GetPeriodGain($starttime,$endtime){
  global $conn;
  $where='c.state>3  and c.actiontime>'.$starttime.' and c.actiontime<'.$endtime;
  $amount1=$conn->query('SELECT sum(b.amount*(b.price-a.cost)) FROM mg_product as a,mg_ordergoods AS b, mg_orders AS c  WHERE b.ordername=c.ordername and a.id=b.productid and a.cost>0 and '.$where)->fetchColumn(0);
  $amount2=$conn->query('select sum(adjust) from mg_orders as c where '.$where)->fetchColumn(0);
  if($amount1===false)$amount1=0;
  if($amount2===false)$amount2=0;
  return $amount1+$amount2;
}

function StatGain(){
  global $conn,$showcost;
  $totalsum=$conn->query('select sum(amount) from mg_accountlog where actiontime>unix_timestamp(date_sub(curdate(),interval 2 day)) and actiontime<unix_timestamp(date_sub(curdate(),interval 1 day)) and operation=2 and amount>0')->fetchColumn(0);
  echo '前日入帐总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';
  
  $totalsum=$conn->query('select sum(amount) from mg_accountlog where actiontime>unix_timestamp(date_sub(curdate(),interval 1 day)) and actiontime<unix_timestamp(curdate()) and operation=2 and amount>0')->fetchColumn(0);
  echo '昨日入帐总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';
  
  $totalsum=$conn->query('select sum(amount) from mg_accountlog where actiontime>unix_timestamp(curdate()) and operation=2 and amount>0')->fetchColumn(0);
  echo '今日入帐总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';

  $totalsum=$conn->query('select sum(totalprice-deliveryfee) from mg_orders where actiontime>unix_timestamp(curdate()) and state>3')->fetchColumn(0);
  echo '今日出库总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';
  
  if($showcost){
    $EndDate=time();
    $BeginDate=strtotime(date("Y-m-d"));
    echo '今日毛利收入：<font color="#FF0000">'.round(GetPeriodGain($BeginDate,$EndDate)).'</font>元<br>';
    
    $BeginDate=strtotime(date("Y-m").'-1');
    echo '本月毛利收入：<font color="#FF0000">'.round(GetPeriodGain($BeginDate,$EndDate)).'</font>元<br>';
    

    $EndDate=$BeginDate;
    $BeginDate=strtotime('-1 month',$EndDate);
    echo '上月毛利收入：<font color="#FF0000">'.round(GetPeriodGain($BeginDate,$EndDate)).'</font>元<br>';
    
    $EndDate=$BeginDate;
    $BeginDate=strtotime('-1 month',$EndDate);
    echo '上上月毛利收入：<font color="#FF0000">'.round(GetPeriodGain($BeginDate,$EndDate)).'</font>元<br>';
  }

  $MaxValidStock=6000;

  $totalsum=$conn->query('select sum(deposit) from mg_users where username<>\'aufame\' and username<>\'junhang\'')->fetchColumn(0);
  echo '当前会员预存款总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';

  $totalsum=$conn->query('select sum(stock0*price3) from mg_product where recommend>0 and stock0>0 and stock0<'.$MaxValidStock)->fetchColumn(0);
  echo '当前有效商品批价总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';

  $totalsum=$conn->query('select sum(stock0*cost) from mg_product where recommend>0 and stock0>0 and cost>0 and stock0<'.$MaxValidStock)->fetchColumn(0);
  echo '当前有效商品成本总额：<font color=#FF0000>'.(($totalsum===false)?0:round($totalsum)).'</font>元<br>';
}
  
 
function WithdrawOrder(){
  global $conn;
  $ordername=FilterText(trim($_POST['ordername']));
  $username=FilterText(trim($_POST['username']));
  if(empty($ordername) || empty($username))PageReturn('参数错误！',0);
  try{//事务管理
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();//事务开始
    $row=$conn->query('select mg_orders.username,mg_orders.state,mg_orders.exporter,mg_orders.importer,mg_orders.totalprice,mg_orders.deliveryfee,mg_orders.totalscore,mg_orders.adminremark,mg_users.deposit,mg_users.score from mg_orders inner join mg_users on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$ordername.'\' for update',PDO::FETCH_ASSOC)->fetch();
    if($row){ 
      $OrderUser=$row['username'];
      $OrderState=$row['state'];
      $OrderExporter=$row['exporter'];
      $OrderImporter=$row['importer'];
      if($OrderState==5){
        $RefundScore=$row['totalscore'];
        $RefundMoney=$row['totalprice'];
        $ScoreRemains=$row['score']-$RefundScore;
        $MoneyRemains=$row['deposit']+$RefundMoney;
        $AdminIDNumber=GetAdminIDNumber();
      }
    }
    else PageReturn('错误！ 订单不存在！',0);
    if($OrderUser!=$username)PageReturn('错误！用户名与订单不符合！',0);
    else if($OrderExporter==$OrderImporter)PageReturn('错误！订单出入库信息无效！',0);
    else if($OrderState>-4 && $OrderState<4) PageReturn('错误！此订单尚未出/入库，无需撤回！',0);
    else if($OrderState<-4 || $OrderState>5)PageReturn('错误！该订单已经签收或锁存，无法退单！',0);
    if($OrderState==-4){
      $sql='update mg_orders set state=-2 where ordername=\''.$ordername.'\' and state='.$OrderState;
      if($OrderExporter>0)$sql2='mg_product.stock'.$OrderExporter.'=mg_product.stock'.$OrderExporter.'+mg_ordergoods.amount'; 
      else $sql2='mg_product.stock0=mg_product.stock0-mg_ordergoods.amount';  
      if($OrderImporter>0)$sql2.=',mg_product.stock'.$OrderImporter.'=mg_product.stock'.$OrderImporter.'-mg_ordergoods.amount';
      else $sql2.=',mg_product.stock0=mg_product.stock0+mg_ordergoods.amount';  
      $sql2='update mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid set '.$sql2.' where mg_ordergoods.ordername=\''.$ordername.'\'';
      if($conn->exec($sql) && $conn->exec($sql2)){
         $conn->commit();//事务完成
         PageReturn('操作成功！ 订单('.$ordername.')已撤回！',0); 
      }
    }
    else{
      $sql='update mg_orders set state=2 where ordername=\''.$ordername.'\' and state='.$OrderState;
      $sql2='update mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid set mg_product.solded=mg_product.solded-mg_ordergoods.amount,mg_product.stock0=mg_product.stock0+mg_ordergoods.amount,mg_product.stock'.$OrderExporter.'=mg_product.stock'.$OrderExporter.'+mg_ordergoods.amount where mg_ordergoods.ordername=\''.$ordername.'\'';
      if($OrderState==5){
        $sql3='update mg_users set score='.$ScoreRemains.',deposit='.$MoneyRemains.' where username=\''.$username.'\'';
        if($conn->exec($sql) && $conn->exec($sql2)&& $conn->exec($sql3)){
	  if($RefundMoney && !$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remark,actiontime) values(\''.$username.'\',\''.$AdminIDNumber.'\',2,'.$RefundMoney.','.$MoneyRemains.',\'订单（'.$ordername.'）撤回货款\',unix_timestamp())')) throw new PDOException('财务日志（撤回货款）操作失败！');  
	  if($RefundScore && !$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remark,actiontime) values(\''.$username.'\',\''.$AdminIDNumber.'\',1,'.(-$RefundScore).','.$ScoreRemains.',\'订单（'.$ordername.'）撤回积分\',unix_timestamp())'))throw new PDOException('财务日志（撤回积分）操作失败！');  
          $conn->commit();//事务完成
	  PageReturn('操作成功！ 订单('.$ordername.')已撤回，货款('.$RefundMoney.'元)已返回，积分('.$RefundScore.'分)已经扣除,订单产品已重新入库！',0);
        }
      }
      else{
        if($conn->exec($sql) && $conn->exec($sql2)){
          $conn->commit();//事务完成
	  PageReturn('操作成功！ 订单('.$ordername.')已撤回，订单产品已调整入库！',0);
        }
      }
    }
  }
  catch(PDOException $ex){ 
    $conn->rollBack();  //事务回滚 
    echo  $ex->getMessage();
  } 
}

function CheckCost(){
  global $conn,$showcost;
  if($showcost){
    $res=$conn->query('select name,price3,cost from mg_product where recommend>0 and cost>0 order by (price3-cost) desc',PDO::FETCH_NUM);
    echo '<table border=1 align=center><tr align=center><td>名称</td><td>批发价</td><td>成本价</td><td>毛利</td></tr>';
    $jishu=0;
    foreach($res as $row){
      $jishu++;
      echo '<tr align=center><td align=left>'.$row[0].'</td><td>'.FormatPrice($row[1]).'</td><td>'.FormatPrice($row[2]).'</td><td>'.FormatPrice($row[1]-$row[2]).'</td></tr>';
    }
    echo '</table><p align=center>总计'.$jishu.'件商品</p>';
  }
  else echo '没有打开权限！';
}
 
function CalOrderCost(){
  global $conn,$showcost;
  $ordername=FilterText(trim($_POST['ordername']));
  if($ordername && $showcost){
    $jishu=0;
    $totalgain=0;
    $totalprice=0;
    echo '<table align="center" border=1 bordercolor=#DFDFDF><tr><td><b>商品名称</b></td><td><b>购买数量</b></td><td><b>成交单价</b></td><td><b>单件毛利</b></td></tr>';
    $res=$conn->query('select mg_product.name,mg_product.cost,mg_ordergoods.price,mg_ordergoods.amount from mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id where mg_ordergoods.ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC);
    foreach($res as $row){
      $jishu++;
      if($row['cost']>0){ 
	$gain=$row['price']-$row['cost'];
        $totalgain+=$gain*$row['amount'];
	$gain=FormatPrice($gain);
       }
       else{
	 $gain='<font color=#FF0000>?</font>';  
       }
       $totalprice+=$row['price']*$row['amount'];
       echo '<tr align="center"><td align="left">'.$row['name'].'</td><td>'.$row['amount'].'</td><td>'.FormatPrice($row['price']).'</td><td>'.$gain.'</td></tr>';
    }
    if($jishu>0)echo '</table><p align=center>订单号：'.$ordername.'&nbsp; &nbsp; 商品总价：'.FormatPrice($totalprice).'元&nbsp; &nbsp; 合计毛利：<font color=#FF0000>'.FormatPrice($totalgain).'</font>元</p>';
    else echo '<tr><td colspan=4 align="center" style="color:#FF0000">订单号['.$ordername.']不存在！</td></tr></table>';
  }
  else echo '没有打开权限或者没有指定订单号！';
}

function ShowCost(){
  global $conn,$AdminUsername;
  $showcost=$_POST['showcost'];
  if($showcost=='false'){
    $_SESSION['showcost']=false;
    echo '<OK>';
  }
  else if($showcost=='true'){
    $AdminPassword=FilterText($_POST['pwd']);
    if($AdminPassword){
      $exist=$conn->query('select id from mg_users where password=md5(\''.$AdminPassword.'\') and username=\''.$AdminUsername.'\'')->fetchColumn(0);
      if($exist){
        $_SESSION['showcost']=true;
        echo '<OK>';
      }
      else echo '密码错误！';
    }
    else echo '请输入密码！';
  }
}

function SwitchArrearage(){
  $_SESSION['arrearage']=!@$_SESSION['arrearage'];
  PageReturn('切换成功！');
}

function CheckURL(){
  global $conn;
  $jishu=0;  
  echo '<table width="100%" border=0><tr><td>';
  $res=$conn->query('select id,name,description from mg_product where description is not null and recommend>0 order by updatetime desc',PDO::FETCH_ASSOC);
  foreach($res as $row){
    if(preg_match_all('/http:\/\/[^\s\'"<]+/',$row['description'],$matches)){
      echo '<a href="mg_editproduct.php?id='.$row['id'].'"><img src="images/pic9.gif" width=18 height=15 align="absmiddle" border=0><b>'.GenProductCode($row['id']).'</b></a>&nbsp; &nbsp; <a href="'.GenProductLink($row['id']).'">'.$row['name'].'</a><br>';
      foreach($matches as $url){
        $url=$url[0];
        echo '<a href="'.$url.'">'.$url.'</a><br>';
      }
      $jishu++;
      echo '<br><br>';
    }
  }
  echo '<hr>共定位到'.$jishu.'条记录！</td></tr></table>';  
}

function ChangeOrderUser(){
  global $conn;
  $ordername=FilterText(trim($_POST['ordername']));
  $username=FilterText(trim($_POST['username']));
  if($ordername && $username){
    $exist=$conn->query('select id from mg_users where username=\''.$username.'\'')->fetchColumn(0);
    if($exist){
       $row=$conn->query('select state,username from mg_orders where ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC)->fetch();
       if(!$row)echo '订单不存在：'.$ordername;
       else if($row['username']==$username)echo '订单'.$ordername.'的用户名已经是'.$username.'，无需修改！';
       else if($row['state']==1 || $row['state']==-1){
         if($conn->exec("update mg_orders set username='$username' where ordername='$ordername' and (state=1 or state=-1)"))echo '<OK>';
         else echo '修改失败！';
       }else echo '订单已经确认，无法更改！';
    }else echo '用户名不存在：'.$username;
  }else echo '参数无效！';
}

function ChangeOrderSupport(){
  global $conn;
  $ordername=FilterText(trim($_POST['ordername']));
  $IDNumber=trim($_POST['idnumber']);
  if($ordername && is_numeric($IDNumber) && $IDNumber>0){
    $ordersupport=$conn->query('select username from mg_admins where idnumber<='.$IDNumber.' and idnumber2>='.$IDNumber)->fetchColumn(0);
    if($ordersupport){
      $row=$conn->query('select id,support,operator,state from mg_orders where ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC)->fetch();
      if(!$row) echo '订单不存在！';
      else if($row['support']==$IDNumber) echo '订单'.$ordername.'的客服已经是'.$IDNumber.'，无需修改！';
      else if($row['state']>=5) echo'此订单状态无法修改！';
      else{
        if($conn->exec("update mg_orders set support=$IDNumber,operator='$ordersupport' where ordername='$ordername' and state<5")) echo '<OK>';
        else echo '修改失败！';
      }
    }else echo '该客服工号不存在！';
  }else echo '参数无效！';
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body topmargin="0" leftmargin="0">
<script>
function ResetOrderPrice(){
  var OnGetOrdername=function(ordername){
    if(ordername){
      if(isNaN(ordername)) alert('无效的订单号！');
      else{
        var OnHttpPost=function(ret){
          if(ret)alert(ret);
          return true;
        }
        AsyncPost('ordername='+ordername,'?mode=resetprice',OnHttpPost);
      }
    }
  }
  AsyncPrompt('重置订单价格',"请输入订单号:<br>注：已经确认的订单请先取消确认",OnGetOrdername);
}

function ResetOrderUser(){
  var OnGetForm=function(myform){
    if(myform){
      var ordername=myform.ordername.value.trim();
      var username=myform.username.value.trim();
      if(ordername && username){
        var OnChangeReturn=function(ret){
          if(ret=='<OK>'){
            alert('订单['+ordername+']用户名已经修改为'+username);
          }
          else if(ret)alert(ret);
        }
        AsyncPost('ordername='+ordername+'&username='+encodeURIComponent(username),'?mode=changeorderuser',OnChangeReturn);
        return true;
      }
      else alert('输入不完整！');
    }
  }
  var html='<form onsubmit="closeDialog(this);return false;"><table width="100%" height="100%" border=0><tr><td width="60" nowrap><b>输入订单号</b></td><td><input type="text" name="ordername" maxlength="16" style="width:100%"></td></tr><tr><td><b>新的用户名</b></td><td><input type="text" name="username" maxlength="16"  style="width:100%"></td></tr><tr><td colspan="2" align="center">注：已经确认的订单请先取消确认<br><input type="submit" value=" 确定 "></td></tr></table></form>';
  AsyncDialog('订单更换用户',html,200,100,OnGetForm);
}

function ResetOrderSupport(){
  var OnGetForm=function(myform){
    if(myform){
      var ordername=myform.ordername.value.trim();
      var idnumber=myform.username.value.trim(); if(ordername && idnumber && !isNaN(idnumber)){
        var OnChangeReturn=function(ret){
          if(ret=='<OK>'){
            alert('订单['+ordername+']客服已经修改为'+idnumber);
          }
          else if(ret)alert(ret);
        }
        AsyncPost('ordername='+ordername+'&idnumber='+idnumber,'?mode=changeordersupport',OnChangeReturn);
        return true;
      }
      else alert('输入参数无效！');
    }
  }
  var html='<form onsubmit="closeDialog(this);return false;"><table width="100%" height="100%" border=0><tr><td width="60" nowrap><b>输入订单号</b></td><td><input type="text" name="ordername" maxlength="16" style="width:100%"></td></tr><tr><td><b>新客服工号</b></td><td><input type="text" name="username" maxlength="16" style="width:100%"></td></tr><tr><td colspan="2" align="center"><input type="submit" value=" 确定 "></td></tr></table></form>';
  AsyncDialog('订单更换客服',html,200,100,OnGetForm);
}

function  WithdrawOrder(){
 var OnGetForm=function(myform){
    if(myform){
      var ordername=myform.ordername.value.trim();
      var username=myform.username.value.trim();
      if(ordername && username){
        var OnChangeReturn=function(ret){
          if(ret=='<OK>'){
            alert('订单['+ordername+']已经撤回！');
          }
          else if(ret)alert(ret);
        }
        AsyncPost('ordername='+ordername+'&username='+encodeURIComponent(username),'?mode=withdraworder',OnChangeReturn);
        return true;
      }
      else alert('输入参数无效！');
    }
  }
  var html='<form onsubmit="closeDialog(this);return false;"><table width="100%" height="100%" border=0><tr><td width="60" nowrap><b>输入订单号</b></td><td><input type="text" name="ordername" maxlength="16" style="width:100%"></td></tr><tr><td><b>订单用户名</b></td><td><input type="text" name="username" maxlength="16"  style="width:100%"></td></tr><tr><td colspan="2" align="center"><input type="submit" value=" 确定 "></td></tr></table></form>';
  AsyncDialog('订单撤回',html,200,100,OnGetForm);
}	
	
function CostOrder(){
  var OnGetOrderName=function(ordername){
    if(ordername){
      HttpGet('?mode=costorder','ordername='+ordername);
      return true;
    }
  }
  AsyncPrompt('订单理论估算','请输入订单号',OnGetOrderName);
}

function RestrainToBigClient(){
  var OnGetUsername=function(username){
    if(username){
      if (confirm("确定要将["+username+"]升级为大客户? \n升级后其订单价格将自动更新！")){
        var OnResult=function(ret){
	  if(ret)alert(ret);
	  else alert("系统错误！");
        }
        AsyncPost("username="+encodeURIComponent(username),"change_usergrade.php?mode=restraintobigclient",OnResult);
        return true;
      }
    }
    else alert('请输入有效的用户名！');
  }
  AsyncPrompt('强升大客户',"请输入待升级的用户名:",OnGetUsername,'',16);
}

function EnableGainStat(onoff){
  var OnGetResult=function(ret){
    if(ret=='<OK>'){
      alert('操作成功');
      self.location.reload();
    }else if(ret)alert(ret); 
  }
  if(onoff){
    var OnVerifyPWD=function(pwd){
      if(pwd==='')alert("请输入密码！"); 
      else AsyncPost('showcost=true&pwd='+pwd,'?mode=showcost',OnGetResult);  
    }
    var html='<form method="post" style="margin:0px" onsubmit="closeDialog(this.password.value);return false;"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>请输入密码：</td></tr><tr><td height="30" align="center" bgcolor="#F7F7F7"><input name="password" type="password" maxlength="16" style="width:100%"></td></tr><tr><td align="center"><input type="submit" value=" 确定 "></td></tr></table></form>';
    AsyncDialog('用户密码验证',html,200,120,OnVerifyPWD);  
  }
  else AsyncPost('showcost=false','?mode=showcost',OnGetResult);  
}

function HttpGet(url,postdata){
  AsyncPost(postdata,url,'msgbox');
}
</script>
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif" height=22><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>其他功能设置</font></b></td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
    <table width="100%" height=50  border="0" align="center" cellpadding="5" cellspacing="5">
    <tr align="center">
       <td><input type="button" <?php echo ($showcost)?'onclick="EnableGainStat(false)" value="已开成本预算" style="color:#FF0000"':'onclick="EnableGainStat(true)" value="未开成本预算"';?>/></td>
       <td><form method="post" action="?mode=arrearage" style="margin:0px"><input type="submit" value="<?php echo $arrearage?'未':'已';?>设欠费限额"></form></td>
       <td><input type="button" onclick="ResetOrderUser()" value="订单更换用户"></td>
       <td><input type="button" onclick="ResetOrderSupport()" value="订单更换客服"></td>
       <td><input type="button" onclick="RestrainToBigClient()" value="强升级大客户"></td>
    </tr>
    <tr align="center">
       <td><input type="button" onclick="HttpGet('?mode=checkurl')" value="检查网页链接"></td>
       <td><input type="button" onclick="CostOrder()" value="订单利润估计"></td>	
       <td><input type="button" onclick="HttpGet('?mode=checkcost')" value="商品利润清单"></td>	
       <td><input type="button" onclick="WithdrawOrder()" value="订单退货撤回"></td>
       <td><input type="button" onclick="self.location.href='mg_batchsettlement.php'" value="批量订单结算"></td>
    </tr>
    <tr align="center">
       <td><input type="button" onclick="HttpGet('?mode=statgain')" value="财务信息统计"></td>
       <td><input type="button" onclick="HttpGet('?mode=statonlinepay')" value="在线支付统计"></td> 
       <td><input type="button" onclick="ResetOrderPrice()" value="重置订单价格"></td>
       <td><input type="button" onclick="HttpGet('?mode=sales')" value="历史销售统计"></td>
       <td></td> 
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td id="msgbox" valign="top" align=center bgcolor="#FFFFFF" height="90%">&nbsp;</td>
</tr>
</table>
</body>
</html>
