<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();

$EndDate=trim(@$_GET['enddate']);
if($EndDate && ($EndDate=strtotime($EndDate))){
  $BeginDate=trim(@$_GET['begindate']);
  if(!$BeginDate || !($BeginDate=strtotime($BeginDate)) || $BeginDate>=$EndDate) $BeginDate=$EndDate-30*24*60*60;
}
else{
  $EndDate=time();
  $BeginDate=time()-30*24*60*60;
}

$str_begindate=date('Y-m-d',$BeginDate);
$str_enddate=date('Y-m-d',$EndDate);

$LastRetrench=$conn->query('select min(actiontime) from mg_orders where state>3 and state<8')->fetchColumn(0);
$LastRetrench=date('Y-m-d',$LastRetrench);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="editproduct.js" type="text/javascript"></SCRIPT>
<title>商品缺货统计</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr><form name="cartform" method="post" action="?"> 
    <td height="22"  background="images/topbg.gif" bgcolor="#F2F2F2">
    	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	<tr>
    		<td nowrap>
    	     <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>商品缺货统计</font></b>
           &nbsp; （<b>时间统计范围</b>：&nbsp;<input  name="begindate" type="text" value="<?php echo $str_begindate;?>" style="width:75px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 1px solid #CCCCCC;background-color:transparent;cursor:pointer;"> ～ <input name="enddate" type="text" value="<?php echo $str_enddate;?>" style="width:75px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 1px solid #CCCCCC;background-color:transparent;cursor:pointer;">）
        </td>
        <td nowrap align="right"><input type="button" value="重新统计" onclick="CheckSearch(this.form)">&nbsp;</td>
      </tr>
      </table>    </td>
  </tr>
  <tr> 
    <td height="100%" align="center"  bgcolor="#FFFFFF" valign="top">
      <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr bgcolor="#f7f7f7" align="center" height="20">
         <td width="5%"  background="images/topbg.gif" height="25"><input type="checkbox" onClick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
         <td width="10%" background="images/topbg.gif"><strong>编号</strong></td>
         <td width="55%" background="images/topbg.gif"><strong>名称</strong></td>
         <td width="10%" background="images/topbg.gif" nowrap title="本期售出是指当前时间统计范围内售出的商品数量"><strong>本期售出(个)</strong></td>
         <td width="10%" background="images/topbg.gif" nowrap title="总计售出是指历史上累计售出的商品数量"><strong>累计售出(个)</strong></td>
         <td width="10%" background="images/topbg.gif" nowrap ><strong>缺货时间</strong></td>
      </tr><?php 
$res=page_query('SELECT a.id,a.name,a.solded,sum(b.amount) as salenum,max(c.actiontime) as maxtime','FROM ((mg_product as a inner join mg_ordergoods as b on a.id=b.productid) inner join mg_orders as c on b.ordername=c.ordername)','WHERE c.state>3 and b.amount>0 and a.stock0<=0 and c.actiontime>'.$BeginDate.' and c.actiontime<'.$EndDate.' GROUP BY a.id, a.name,a.solded','order by maxtime desc',20);	
if($total_records==0) echo  '<tr><td colspan=6 align=center><p align="center">数据库中暂时无相应记录！</td></tr></table>';
else{
  foreach($res as $row){
    echo '<tr height="25" bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
            <td><input name="selectid[]" type="checkbox" value="'.$row['id'].'" onclick="mChk(this)"></td>
            <td><a href="mg_stocklog.php?id='.$row['id'].'">'.GenProductCode($row['id']).'</a></td>
            <td align="left"><a href="'.GenProductLink($row['id']).'" target="_blank">'.$row['name'].'</a></td>
            <td><font color="#FF0000">'.$row['salenum'].'</font></td>
            <td>'.$row['solded'].'</td>
            <td nowrap>'.date('Y-m-d',$row['maxtime']).'</td></tr>';
  }
  echo '</table>
     <TABLE cellSpacing=0 cellPadding=0 width="98%" align="center"  border="0">
     <TR>
       <TD width="1%" nowrap><input type="button" name="CartButton" value="放入购物车" onclick="AddToCart(this.form)">&nbsp;<input type="button" name="OrderButton" value="加入订单" onclick="AddToOrder(this.form)"></TD>
       <TD align="center"><script language="javascript">'."GeneratePageGuider(\"begindate=$str_begindate&enddate=$str_enddate\",$total_records,$page,$total_pages);".'</script></TD>
     </tr>
     </TABLE>';
}?>

   </td>
  </tr></form>
</table>
<script language="javascript">
function StrToDate(strDate)
{ var  sd=strDate.split("-"); 
 	 return new Date(sd[0],sd[1],sd[2]);   
}

function CheckSearch(myform){
  var begindate=myform.begindate.value;
   if( StrToDate(begindate) < StrToDate("<?php echo $LastRetrench;?>") ){
     alert("对不起，只能查询<?php echo $LastRetrench;?>后的统计明细信息！");
   }
   else self.location.href="?begindate="+begindate+"&enddate="+myform.enddate.value; 
}

</script>	

</body>
</html><?php CloseDB();?>

