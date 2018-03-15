<?php require('includes/dbconn.php');
CheckLogin();
OpenDB(); 

$username=FilterText(trim($_GET['user']));
if(empty($username))PageReturn('参数错误！',0); 
else{
  session_start();
  $showcost = @$_SESSION['showcost'] && CheckPopedom('MANAGE');
}

function  SalesMonthStat($begintime,$endtime){
  global $conn,$username,$showcost;
  $TotalConsume=$conn->query('SELECT sum(totalprice) FROM mg_orders WHERE username=\''.$username.'\' and state>3 and importer=0 and actiontime>'.$begintime.' and actiontime<'.$endtime)->fetchColumn(0);
  if($TotalConsume===false ||$TotalConsume===null) $TotalConsume=0;
  echo '<tr align="center" bgcolor=#FFFFFF><td>'.date('Y-m',$begintime).'</td><td><font color=#FF0000>'.round($TotalConsume).'</font></td>';
  if($showcost){
    $totalgain=$conn->query('select sum((mg_ordergoods.price-mg_product.cost)*mg_ordergoods.amount) from ((mg_ordergoods inner join mg_orders on mg_ordergoods.ordername=mg_orders.ordername) inner join mg_product on mg_ordergoods.productid=mg_product.id) where mg_orders.username=\''.$username.'\' and mg_orders.state>3 and mg_orders.importer=0 and mg_orders.actiontime>'.$begintime.' and mg_orders.actiontime<'.$endtime)->fetchColumn(0);
    if($totalgain===false ||$totalgain==null) $totalgain=0;
    echo '<td color=#00FF00>'.round($totalgain).'</td>';
  }
  echo '</tr>';
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>              
   <td background="images/topbg.gif" height="35"><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_users.php">客户管理</a> -&gt; <font color=#FF0000>会员按月消费统计</font></b></td>
</tr>             
<tr>              
   <td valign="top" bgcolor="#FFFFFF">
     <table width="80%" border="0" align="center">
     <tr><td>会员 <a href="mg_usrinfo.php?user=<?php echo $username;?>"><font color=#FF6600><?php echo $username;?></font></a> 按月消费统计</td></tr>
     </table>
	   	
     <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr align="center" bgcolor="#FFFFFF" height="25"> 
       <td width="45%" background="images/topbg.gif" bgcolor="#F7F7F7"><b>月份</b></td>
       <td width="55%" background="images/topbg.gif"><b>消费额</b></td>
       <?php if($showcost) echo '<td width="55%" background="images/topbg.gif"><b>毛利</b></td>';?>
     </tr><?php
$EndDate=time();
strtotime(date('Y-m-d'));
for($i=0;$i<6;$i++){
  $BeginDate=strtotime(date('Y-m',$EndDate).'-1');
   SalesMonthStat($BeginDate,$EndDate);
   $EndDate=strtotime('-1 day',$BeginDate);
}?>
     <tr align="center" bgcolor=#FFFFFF><td>...</td><td>...</td><?php if($showcost) echo '<td>...</td>';?></tr>
     </table>
  </td> 
</tr>
</table>
</body>
</html><?php CloseDB();?>
