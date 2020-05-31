<?php require('includes/dbconn.php');
CheckLogin();
db_open();
/* 旧版-交易状态说明-参考
0   :  删除的交易记录
10  :  网银交易开始
110 :  网银交易完成（即时到帐）
20  :  支付宝交易开始，等待买家付款
21  :  买家付款成功,等待卖家发货
22  :  卖家已发货等待买家确认
120 :  支付宝交易完成（即时到帐）
30  :  快钱交易开始
130 :  快钱交易完成（即时到帐） 

//   交易状态说明（默认支付宝:1）
0  :  删除的交易记录
1  :  支付宝交易开始，等待买家付款
2  :  买家付款成功,等待卖家发货
3  :  卖家已发货等待买家确认
4  :  支付宝交易完成（即时到帐）
*/
define('PAY_FINAL_STATE',4);

$mode=@$_GET['mode'];
if($mode){
  if($mode=='delete'){
    $OrderID=$_POST['orderid'];
    if(is_numeric($OrderID) && CheckPopedom('SYSTEM')){
      if($conn->exec('update mg_onlinepay set state=0 where id='.$OrderID)) PageReturn('在线支付订单记录删除成功！');
    }
  }
  else if($mode=='confirmpay'){
    $OrderID=$_POST['orderid'];
    if(is_numeric($OrderID) && CheckPopedom('FINANCE')) try{//事务管理
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();//事务开始
      $row=$conn->query('select mg_onlinepay.amount,mg_onlinepay.state,mg_onlinepay.mode,mg_users.deposit,mg_users.username from mg_onlinepay inner join mg_users on mg_onlinepay.username=mg_users.username where mg_onlinepay.id='.$OrderID.' and (mg_onlinepay.state>0 and mg_onlinepay.state<'.PAY_FINAL_STATE.') for update',PDO::FETCH_ASSOC)->fetch();
      if($row){
        $Amount=$row['amount'];
        if($Amount>0){
          $Username=$row['username'];
          $new_deposit=$row['deposit']+$Amount;
          $PayMethod=GetPayMethod($row['mode']);
          $sql_1="mg_accountlog set username='$Username',adminuser='$AdminUsername',operation=2,amount=$Amount,surplus=$new_deposit,remark='{$PayMethod}支付（手动入账）',actiontime=unix_timestamp()";
          $sql_2='update (mg_onlinepay inner join mg_users on mg_onlinepay.username=mg_users.username) set mg_onlinepay.state='.PAY_FINAL_STATE.',mg_users.deposit=mg_users.deposit+mg_onlinepay.amount where mg_onlinepay.id='.$OrderID.' and (mg_onlinepay.state>0 and mg_onlinepay.state<'.PAY_FINAL_STATE.')'; 
          if($conn->exec('update '.$sql_1.' where operation=0 limit 1') || $conn->exec('insert into '.$sql_1)){
            if($conn->exec($sql_2)){
              $conn->commit();//事务完成
              PageReturn('支付确认完成，已将'.$Amount.'元注入用户['.$Username.']的预存款帐户！');
            }else throw new PDOException('生成财务日志失败,数据库异常！');  
          }else throw new PDOException('入账操作失败,数据库异常！');  
        }
      }
      else PageReturn('找不到对象！');
    }
    catch(PDOException $ex){ 
      $conn->rollBack();  //事务回滚 
      PageReturn($ex->getMessage());
    } 
  }
}   

function GetPayMethod($mode){
  switch($mode){
    case 1: return '支付宝';
    case 2: return '快钱';
    case 3: return '网银';
    default: return '其它';
  }
}

function GetWebSite($url){
  if($url){
    $arr = parse_url($url); 
    return $arr['host'];
  }   
  return '';
}

function GetPayState($state){
  switch($state){
    case 1  : return '等待付款';
    case 2  : return '<font color="#00aaff" title="支付宝担保交易，买家确认收货后资金才能到帐">买家已付款，等待卖家发货</font>';
    case 3  : return '<font color="#0000FF" title="支付宝担保交易，买家确认收货后资金才能到帐">卖家已发货，等待买家确认</font>';
    case PAY_FINAL_STATE:return '<font color="#00aa00">交易成功</font>';
    default : return '未知';
  }
}

$own_popedomFinance=CheckPopedom('FINANCE');
$own_popedomSystem=CheckPopedom('SYSTEM');
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Refresh" content="300;URL=<?php echo $_SERVER['PHP_SELF'];?>">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body topmargin="0" leftmargin="0">
<script language=javascript>
function RadioButtonSelected(myform){
  var CheckBoxes=myform.orderid;
  var CheckBoxCount=CheckBoxes.length;
  if(!CheckBoxCount) return CheckBoxes.checked;
  else{
    for(var i=0;i<CheckBoxCount;i++)
     if(CheckBoxes[i].checked) return true;
  }  
  return false;
}
 
function DeleteLog(myform){
  if(!RadioButtonSelected(myform)) alert("没有选择操作对象！");
  else if(confirm("确定要删除所选的在线支付日志吗？")){
    myform.action = "?mode=delete";
    myform.submit();
  }
}

function CompleteOrder(myform){
  if(!RadioButtonSelected(myform))alert("没有选择操作对象！");
  else if(confirm("请登录在线支付后台确认笔汇款是否已经支付成功，\n\n如果已经确认支付成功，请点击确定完成帐户充值！")){
    myform.action = "?mode=confirmpay";
    myform.submit();
  }
}
</script>
<form method="post" style="margin:0px">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td background="images/topbg.gif">
    <table border=0 cellpadding=0 cellspacing=0 width="100%">
    <tr>
      <td><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>在线支付日志</font></b></td>
      <td align="right"><?php
        if($own_popedomFinance) echo '<input type="button" onclick="CompleteOrder(this.form)" value="确认收到货款"> &nbsp;';
        if($own_popedomSystem) echo '<input type="button" onclick="DeleteLog(this.form)" value="删除所选日志"> &nbsp;';?>
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr> 
   <td height="200" valign="top" align="center" bgcolor="#FFFFFF"><?php

$keyvalue=FilterText(trim(@$_GET['kv']));
$where='where state>0';
if($keyvalue){
  $where.=" and username like '%$keyvalue%'"; 
  echo '<b>根据<font color="#FF6600">用户名</font>搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
}
$res=page_query('select *','from mg_onlinepay',$where,'order by actiontime desc',20);
if($total_records==0) echo '<p align="center"> 对不起，没有找到相关记录！</p>';
else{
  echo '<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr height="25" align="center" bgcolor="#F7F7F7"> 
          <td WIDTH="20%" background="images/topbg.gif"><strong>支付单号</strong></td>
          <td WIDTH="15%" background="images/topbg.gif"><strong>用户名</strong></td>
          <td WIDTH="15%" background="images/topbg.gif"><strong>充值金额</strong></td>
          <td WIDTH="15%" ackground="images/topbg.gif"><strong>登录站点</strong></td>
          <td WIDTH="10%" background="images/topbg.gif"><strong>支付方式</strong></td>
          <td WIDTH="15%" background="images/topbg.gif"><strong>支付时间</strong></td>
          <td WIDTH="10%" background="images/topbg.gif"><strong>支付状态</strong></td>
        </tr>';
  foreach($res as $row){
    $state=$row['state'];
    echo '<tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
          <td height="25"><input name="orderid" type="radio" value="'.$row['id'].'">&nbsp;'.$row['tradeno'].'</td>
            <td height="25" align="center"><a href="mg_usrinfo.php?user='.$row['username'].'">'.$row['username'].'</a></td>
            <td height="25">'.FormatPrice($row['amount']).'</td>
            <td height="25">'.GetWebSite($row['site']).'</td>
            <td height="25">'.GetPayMethod($row['mode']).'</td>
            <td height="25" nowrap>'.date('Y-m-d H-i',$row['actiontime']).'</td>
            <td height="25">'.GetPayState($row['state']).'</td>
          </tr>';
  }
  echo '  </table>
          <script language="javascript"> GeneratePageGuider("kv='.$keyvalue."\",$total_records,$page,$total_pages);</script>";
}?>
  </td>
</tr>
</table></form>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td height="60" bgcolor="#FFFFFF" align="center"> 
    <form method="get" style="margin:0px">
       按用户名<input name="kv" type="text" size="12"> &nbsp; <input type="submit" value="查 询">
    </form>
  </td>
</tr>
</table>
</body>
</html><?php db_close();?>
