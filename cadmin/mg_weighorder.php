<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$own_popedomProduct=CheckPopedom('PRODUCT');
if(@$_GET['mode']=='weight'){
  if($own_popedomProduct){
    $newvalue=$_POST['newvalue'];
    $selectid=$_POST['selectid'];
    if(is_numeric($newvalue) && $newvalue>=0 && is_numeric($selectid) && $selectid>0){
       if($conn->exec('update mg_product set weight='.$newvalue.' where id='.$selectid)) echo '<OK>';
    }
  }
  db_close();
  exit(0);
}

$OrderName=FilterText(trim($_GET['ordername']));
if(empty($OrderName)) PageReturn('参数无效！',0); 

$res=$conn->query('select mg_orders.username,mg_orders.state,mg_ordergoods.id,mg_ordergoods.productid,mg_ordergoods.price,mg_ordergoods.amount,mg_ordergoods.productname,mg_product.weight from ((mg_ordergoods inner join mg_orders on mg_ordergoods.ordername=mg_orders.ordername) inner join  mg_product on mg_product.id=mg_ordergoods.productid) where mg_orders.ordername=\''.$OrderName.'\' order by mg_ordergoods.productname',PDO::FETCH_ASSOC);
$row=$res->fetch();
if($row){
 $username=$row['username'];  
 $OrderState=$row['state'];
}
else PageReturn('<p align=center>订单不存在！</p>',0);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>订单详细资料</title>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
A{TEXT-DECORATION: none;}
A:link    {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover   {COLOR: #FF0000; TEXT-DECORATION: underline}
TD   {FONT-FAMILY:宋体;FONT-SIZE: 9pt;line-height: 150%;}
-->
</style>
</head>
<body topmargin="0" leftmargin="0">
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
 <td background="images/topbg.gif">
    	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr> 
    	<td nowrap>
    <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <?php if($OrderState>0) echo  '<a href="mg_orders.php">客户订单管理</a>';else echo '<a href="mg_privateorders.php">内部订单管理</a>';?>  -> <a href="mg_checkorder.php?ordername=<?php echo $OrderName;?>"><font color=#0000FF>订单明细</font></a> -> <font color=#FF0000>称重统计</font></b>
      </td>

    </tr></table>
    </td>
  </tr>
  <tr> 
    <td height="200" valign="top" bgcolor="#FFFFFF">
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
          <td background="images/topbg.gif" nowrap>
          	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	      <tr>
    		      <td width="60%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" /><b>订单号</b>：<a href="mg_checkorder.php?ordername=<?php echo $OrderName;?>"><font color="#FF0000"><?php echo $OrderName;?></font></a> ，<img src="images/pic18.gif" width="17" height="15" align="absmiddle" /><b>下单用户</b>：<a href="javascript:CheckUser('<?php echo $username;?>')"><?php echo $username;?></a></td>
    		     
    		      <td width="20%" nowrap align="center"></td>
    		      <td width="20%" nowrap align="right"></td>
    		           
    		    </tr>
    		    </table>
          </td>
        </tr>
      </table>
      
 

      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="60%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>单件重量(克)</strong></td>
      </tr><?php

$TotalPrice=0;  //价格总计
$TotalWeight=0;
$TotalRecord=0; //商品总项目
$TotalProduct=0;   //商品总件数

while($row){
  $Amount=$row['amount'];
  $Weight=$row['weight'];
  $Price=$row['price'];
  $TotalRecord++;
  $TotalProduct+=$Amount;	
  $TotalPrice+=$Amount*$Price;
  if(is_numeric($Weight)){if($Weight>0)$TotalWeight+=$Amount*$Weight;}
  else $Weight='<font color=#FF0000>??</font>';?> 
    <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
      <TD><a href="mg_stocklog.php?id=<?php echo $row['productid'];?>"><?php echo GenProductCode($row['productid']);?></a></td>
      <TD align="left">&nbsp;<a href="<?php echo GenProductLink($row['productid']);?>" target=_blank style="color:#000000"><?php echo $row['productname'];?></a></TD>
      <TD><?php echo $Amount;?></td>
      <TD><?php echo FormatPrice($Price);?></td>		
      <TD<?php if($own_popedomProduct) echo ' style="color:#0000FF;TEXT-DECORATION:underline;cursor:pointer" onclick="ChangeWeight(this,'.$row['productid'].')"';?>><?php echo $Weight;?></td>
    </TR><?php 
    $row=$res->fetch();
}?>
                 
<tr height="20"> 
<td height="25" align="center" colspan="2" background="images/topbg.gif"><b>合计</b></td>
<td height="25" align="center" background="images/topbg.gif"><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $TotalRecord;?></td>
<td height="25" align="center" background="images/topbg.gif"><font color="#FF0000"><?php echo FormatPrice($TotalPrice);?>元</font></td>
<td height="25" align="center" background="images/topbg.gif"><font color="#FF0000" id="totalweight"><?php echo round($TotalWeight/1000,1);?>千克</font></td>
</tr>
</table>
     
      

    </td>
 
  </tr>
</table>
<script>
function CheckUser(username)
{ window.open("mg_usrinfo.php?user="+encodeURIComponent(username),'',"scrollbars=yes,width=800,height=550")
}

function ChangeWeight(tableCell,productID){
  var defValue=GetInnerText(tableCell).trim();
  var pname=GetInnerText(tableCell.parentNode.cells[1]);
  var getresult = function(newValue){
    if(newValue && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("selectid="+productID+"&newvalue="+newValue,"?mode=weight");
      if(ret){
        if(ret.indexOf("<OK>")>=0){
          var obj=document.getElementById('totalweight');
          tableCell.innerHTML="<font color=#FF0000>"+newValue+"</font>";
          if(obj)obj.innerHTML='<input type="button" onclick="self.location.reload()" value="刷新">';
         
        }else alert(ret);
      }
      else alert("操作失败，请稍候再试！");
    } 
    return true;
  }
  AsyncPrompt("设定商品重量(单位:克)",pname,getresult,defValue,6);  
}

</script>

</body>
</html><?php db_close();?>
