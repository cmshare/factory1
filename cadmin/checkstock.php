<?php require('includes/dbconn.php');
CheckLogin('STOCK');
db_open();

$productid=@$_GET['id'];
if(is_numeric($productid) && $productid>0){
  $row=$conn->query('select * from mg_product where id='.$productid,PDO::FETCH_ASSOC)->fetch();
  if(empty($row))PageReturn('目标不存在！',0);
}else PageReturn('参数错误！',0);
	
$mode=@$_GET['mode'];
if($mode=='save'){
  if(!CheckPopedom('PRODUCT')) PageReturn('权限错误！',0);	
  $sql='';
  $TotalStock=0;
  $rs_depot=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
  foreach($rs_depot as $row_depot){
    $StockName='stock'.$row_depot[0];
    $StockCount=@$_POST[$StockName];
    if(is_numeric($StockCount)){ 
      $StockCount=(int)$StockCount;
      $TotalStock=$TotalStock+$StockCount;
      if($row[$StockName]!=$StockCount){
        $stockchanged=true;
        $sql.="$StockName=$StockCount,";
      }
    }else PageReturn('参数错误！',0);
  }
  if($TotalStock!=$row['stock0']){
    $sql.="stock0=$TotalStock,";
  }
  if($sql && $conn->exec('update mg_product set '.substr($sql,0,-1).' where id='.$productid)){
    echo '<script>parent.closeDialog('.$TotalStock.');</script>';
  }
  else{
    echo '<script>parent.closeDialog();</script>';	
  }
  //关于window.returnValue返回值：-1表示库存修改失败;null表示库存各项未改变;>=0表示库存修成功，并返回修改后的库存总量
  PageReturn();
}
else if($mode=='edit'){
  $InputStyle='onkeyup="CheckInput(this)" maxlength="6" ';
}
else{
  $InputStyle='disabled';
}?><HTML>
<HEAD>
<TITLE>商品库存分布明细</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<STYLE TYPE="text/css">
 td {font-size: 10pt}
</STYLE>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript">
function PageInit(){
  document.forms[0].CloseBtn.focus();
}
function Submit_Select(myform){
    myform.SubmitBtn.disabled=true;
    if(RefreshTotalStockShow(myform)>=0){
      myform.SubmitBtn.value="通讯中...";
      myform.submit();
    }
    else{
      alert("请输入正确的数字！");
      myform.SubmitBtn.disabled=false;
    }
}
function RefreshTotalStockShow(myform){
    var Elements=myform.elements;
    var Elen=Elements.length;
    var strValue,TotalStock=0;
    for (var i=0; i<Elen; i++){
      if (Elements[i].type == "text"){
        strValue=Elements[i].value;
        if(!strValue || isNaN(strValue)){
          Elements[i].value="";
    	  Elements[i].focus();
    	  TotalStock=-1;
    	  break;
    	}
    	else{
          TotalStock+=parseInt(strValue,10);
    	}
      }	
    }
    var obj=document.getElementById("TotalStock");
    if(obj)obj.innerHTML=TotalStock;
    return TotalStock;
}
function CheckInput(obj){
  if(isNaN(obj.value)) document.execCommand("undo");
  RefreshTotalStockShow(obj.form);
}
</script> 
</HEAD>
<BODY  bgcolor="#DFDFDF" topmargin=0 leftmargin=0 onload="PageInit()">
<form method="post" action="?mode=save&id=<?php echo $productid;?>" style="margin:0px">
<TABLE width="100%" height="100%"  border="1"  align="center" bordercolor="#FF6600" bgcolor="#FF6600" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF">
<TR><TD width="100%" height="30"><font color="#FFFFFF"><strong><?php echo $row['name'];?></strong></font></TD></TR>
<TR bgcolor="#f7f7f7">
   <TD valign="top">
     <table width="100%" border="0" cellpadding="0" cellspacing="0">
     <tr><?php
 $rs_depot=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
  $TotalStock=0;
  $jishu=0;
  foreach($rs_depot as $row_depot){
     if($jishu>0 && $jishu%4==0) echo '</tr><tr>';
     $destStock=$row['stock'.$row_depot[0]];	
     echo '<td align="right"><b>'.$row_depot[1].'</b></td><td><input name="stock'.$row_depot[0].'" type="text" value="'.$destStock.'" '.$InputStyle.' size="6">件</td>';
     $TotalStock=$TotalStock+$destStock;
     $jishu++;
  }
  if($TotalStock!=$row['stock0']){
    $conn->exec('update mg_product set stock0='.$TotalStock.' where id='.$productid);
  }
?>
  </tr></table>
  </TD>
</TR>
<TR bgcolor="#f7f7f7">
  <TD bgcolor="#FFCC00" height="30">
     <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
     <tr>
	<td width="50%"><b>库存统计(件)</b>：<font color="#FF0000" id="TotalStock"><?php echo $TotalStock;?></font></td>
	<td width="50%" align="right"><?php if($mode=='edit') echo '<input name="SubmitBtn" type="button" value=" 修改 " onclick="Submit_Select(this.form)">&nbsp;<input name="id" type="hidden" value="'.$productid.'">';?><input name="CloseBtn" type="button"  value=" 关闭 " onclick="parent.closeDialog()">&nbsp;</td>
     </tr>
     </table>
  </TD>
</TR>
</TABLE></form>
</BODY>   
</HTML><?php
 db_close();?>
