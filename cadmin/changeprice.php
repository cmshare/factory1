<?php require('includes/dbconn.php');
$ProductID=@$_GET['id'];
if(is_numeric($ProductID) && $ProductID>0) CheckLogin('PRODUCT');
else exit(0);

db_open();

if(@$_POST['mode']=='save'){
   $price1=trim(@$_POST['price1']);
   $price2=trim(@$_POST['price2']);
   $price3=trim(@$_POST['price3']);
   $price4=trim(@$_POST['price4']);
   if(is_numeric($price1)) $price1=round($price1,2); else $price1=0;
   if(is_numeric($price2)) $price2=round($price2,2); else $price2=0;
   if(is_numeric($price3)) $price3=round($price3,2); else $price3=0;
   if(is_numeric($price4)) $price4=round($price4,2); else $price4=0;
   if($price3==0) echo '请正确填写批发价！';
   else if($price1<$price2 || $price2<$price3 || $price3<$price4) echo '价格分等不合理，请核实！';
   else{
       $sql="update mg_product set price1=$price1,price2=$price2,price3=$price3,price4=$price4";
       $cost=trim(@$_POST['cost']);
       if(is_numeric($cost)){
         $cost=round($cost,2);
         $sql.=",cost=$cost";
       }
       if($conn->exec($sql.' where id='.$ProductID)) echo '修改成功！<OK>';
       else echo '参数错误！';
   }
   db_close();
   exit(0);
}

$row=$conn->query('select name,price1,price2,price3,price4,cost from mg_product where id='.$ProductID,PDO::FETCH_ASSOC)->fetch();
if(empty($row)){
  echo '<p align="center">参数错误！</p>';
  db_close();
  exit(0);
}
else{
  session_start();
  $showcost=@$_SESSION['showcost'];
}

?><HTML>
<HEAD>
<TITLE>修改商品价格</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<STYLE TYPE="text/css"> td {font-size: 10pt}</STYLE>
<script language="javascript" src="checkproduct.js"></script>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT LANGUAGE=JavaScript>
function Submit_Price(myform){
  myform.ConfirmButton.disabled=true;
  var price=CheckProductPrice(myform);
  if(price){
    if(price[1]!=<?php echo $row['price1'];?> || price[2]!=<?php echo $row['price2'];?> || price[3]!=<?php echo $row['price3'];?> || price[4]!=<?php echo $row['price4'];?>  || (price[0]!=null && price[0]!=<?php echo $row['cost'];?>) ){
      var ret=SyncPost("mode=save&price1="+price[1]+"&price2="+price[2]+"&price3="+price[3]+"&price4="+price[4]+((price[0]==null)?"":"&cost="+price[0]),"?id=<?php echo $ProductID;?>");
      if(ret){
        if(ret.indexOf("<OK>")>=0){
          parent.returnValue = price;
	  document.getElementById("msgbox1").innerHTML="<font color=#FF0000>价格修改成功！</font>";
	  document.getElementById("msgbox2").innerHTML="<input type='button' value=' 确定 ' onclick='parent.closeDialog(parent.returnValue)'>";	
	  return;
        }
        else alert(ret);
      }else alert("操作失败，请稍候再试！");
    }else alert("价格没有改变！");
  } 
  myform.ConfirmButton.disabled=false;
}

function InitPage(){
var myform=document.myform;
if(myform)myform.CancelButton.focus();
}
</script> 
</HEAD>
<BODY  bgcolor="#DFDFDF" topmargin=0 leftmargin=0  onload="InitPage()">
<TABLE width="100%" height="100%" border="1" align="center" bordercolor="#FF6600" bgcolor="#FF6600" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<TR><form name="myform">
 <TD width="100%" height="25"><font color="#FFFFFF"><strong><?php echo $row['name'];?></strong></font></TD>
</TR>
<TR bgcolor="#f7f7f7" >
<TD width="100%" align="center" id="msgbox1">
  <table width="100%"  height="100%"  border="1" cellspacing="0" cellpadding="0" bgcolor="#FFCC00" bordercolor="#D6E7FF">
<tr align="center">
<td width="20%">市场价</td>
<td width="20%">VIP价</td>
<td width="20%"><font color=#FF0000><b>批发价</b></font></td>
<td width="20%"><font color=#FF0000><b>大客户价</b></font></td>
<?php if($showcost) echo '<td width="20%" style="font-weight:bold;color:#00AA66">成本价</td>';?>
</tr>
<tr align="center">
      <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price1" type="text" class="input_sr" value="<?php echo FormatPrice($row['price1']);?>" size="5">元</font></td>
      <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price2" type="text" class="input_sr" value="<?php echo FormatPrice($row['price2']);?>" size="5">元</td>
      <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price3" type="text" class="input_sr" value="<?php echo FormatPrice($row['price3']);?>" size="5">元 <font color=#FF0000>＊</font></td>
      <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price4" type="text" class="input_sr" value="<?php echo FormatPrice($row['price4']);?>" size="5">元 <font color=#FF0000>＊</font></td>                
      <?php if($showcost) echo '<td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="cost" type="text" class="input_sr" value="'.FormatPrice($row['cost']).'" size="5">元</td>';?>
    </tr>
    </table> 
  </TD>  
</TR>
<TR bgcolor="#f7f7f7">
	<TD bgcolor="#FFCC00" width="100%" height="28" align="center" id="msgbox2">
		 <table border=0 width="100%" height="100%" cellspacing="0" cellpadding="0">
		 <tr>
		 	<td>&nbsp; <span style="color:#0000FF;text-decoration: underline;cursor:pointer" onclick="AutoPriceClear(document.myform)">清除</span>&nbsp;|&nbsp;<span style="color:#0000FF;text-decoration: underline;cursor:pointer" onclick="AutoPriceFinish(document.myform)">自动完成</span>&nbsp;</td>
		 	<td align="right"><input name="ConfirmButton" type="button"  value=" 修改 " onclick="Submit_Price(this.form)">&nbsp;<input type="button" name="CancelButton" value=" 取消 " onclick="parent.closeDialog();"></td>
		 </tr>
		 </table>
		 	   
</TD>
</TR></form>
</TABLE>

</BODY>   
</HTML><?php db_close();?>
