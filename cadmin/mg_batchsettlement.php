<?php require('includes/dbconn.php');
CheckLogin('FINANCE');
db_open();
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="includes/mg_htmupdate.js" type="text/javascript"></SCRIPT>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
  <td height="20" width="100%" background="images/topbg.gif" bgcolor="#F7F7F7">
    <table border=0 width="100%">
    <tr>
      <td width="65%"><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>批量订单结算</font></b></td><td width="35%" align="center"><a href="#" onclick="ControlUpdate(true)"><b>开始自动审核收款</b></a>&nbsp;|&nbsp;<a href="#" onclick="ControlUpdate(false)"><b>停止自动审核收款</b></a></td>
     </tr>
     </table>
   </td>
</tr>
<tr bgcolor="#F7F7F7"> 
  <td height="100%" width="100%" bgcolor="#F7F7F7">
     <table id="mytable" width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr bgcolor="#F7F7F7" align="center"> 
       <td height="25" background="images/topbg.gif"><strong>订单号</strong></td>
       <td height="25" background="images/topbg.gif"><strong>下单用户</strong></td>
       <td height="25" background="images/topbg.gif"><strong>出货点</strong></td>
       <td height="25" background="images/topbg.gif"><strong>收货人</strong></td>
       <td height="25" background="images/topbg.gif"><strong>订单金额</strong></td>
       <td height="25" background="images/topbg.gif"><strong>配送方式</strong></td>
       <td height="25" background="images/topbg.gif"><strong>客服</strong></td>
       <td height="25" background="images/topbg.gif" width="10%"><strong>&nbsp;</strong></td>
       <td height="25" background="images/topbg.gif"><strong>订单处理</strong></td>
     </tr><?php

$AdminDepotIndex=GetAdminDepot();

$res=$conn->query('select mg_orders.*,mg_depot.depotname from mg_orders left join mg_depot on mg_orders.exporter=mg_depot.id where mg_orders.state=4 order by mg_orders.actiontime asc',PDO::FETCH_ASSOC);
foreach($res as $row){
  $DepotName=$row['depotname'];
  if(empty($DepotName))$DepotName='其它单位';
  $style=$row['adminremark']?' style="BACKGROUND-POSITION: right 30%;BACKGROUND-IMAGE:url(images/memo.gif);BACKGROUND-REPEAT:no-repeat;Cursor:hand" title="'.$row['adminremark'].'"':'';
  echo '<tr align="center" bgcolor="#FFFFFF" height=25 onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
        <td background="images/topbg.gif"> <a href="mg_checkorder.php?ordername='.$row['ordername'].'" target="_blank">'.$row['ordername'].'</a></td>
        <td><a href="mg_usrinfo.php?user='.$row['username'].'" target="_blank">'.$row['username'].'</a></td>
        <td'.(($row['exporter']==$AdminDepotIndex)?'':' style="color:#FF0000;font-weight:bold').'>'.$DepotName.'</td>
        <td>'.$row['receipt'].'</td>
        <td>'.FormatPrice($row['totalprice']).'</td>
        <td>'.$row['deliverymethod'].'</td>
        <td>'.(empty($row['operator'])?'NONE':$row['operator']).'</td>
        <td>&nbsp;</td>
        <td'.$style.'><input type="button" value="审核收款" onclick="UpdateItem(this,\'ordername='.$row['ordername'].'&username=\'+encodeURIComponent(\''.$row['username'].'\')+\'&newstate=5\')"></td></tr>';
}?>
</table></td></tr></table>
<script>
InitHtmlUpdate(5,"mg_checkorder.php?mode=batchsettlement","mytable");
</script>
</body>
</html><?php db_close();?>
