<?php require('includes/dbconn.php');
CheckLogin();
db_open();
if(@$_GET['mode']=='addnew'){
  $OrderName=$_POST['ordername'];
  $ProductID=$_POST['productid'];
  $Amount=$_POST['amount'];
  if($OrderName && is_numeric($ProductID) && $ProductID>0 && is_numeric($Amount)){
    $ShopUserGrade=$conn->query('select mg_users.grade from mg_users inner join mg_orders on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$OrderName.'\' and (mg_orders.state=2 or mg_orders.state=1 or mg_orders.state=-1)')->fetchColumn(0);
    if($ShopUserGrade){
      $row=$conn->query('select id,name,score,price0,price'.$ShopUserGrade.' as myprice,onsale from mg_product where id='.$ProductID.' and recommend>0',PDO::FETCH_ASSOC)->fetch();
      if($row){
        $product_name=$row['name'];
        $product_score=$row['score'];
        $product_price=$row['myprice'];
        if(($row['onsale']&0xf)>0 && $ShopUserGrade>2){
          if($row['onsale']>time() && $row['price0']<$product_price) $product_price=$row['price0'];
        }
        $Remark=FilterText(trim($_POST['remark']));
        if(strlen($Remark)>255) $Remark=substr($Remark,250).'...';
        $sql="mg_ordergoods set productid=$ProductID,productname='$product_name',score=$product_score,price=$product_price,amount=$Amount,remark='$Remark',audit=1";
        $goodsid=$conn->query('select id from mg_ordergoods where ordername=\''.$OrderName.'\' and productid='.$ProductID)->fetchColumn(0);
        if($goodsid){
           if($conn->exec('update '.$sql.' where id='.$goodsid)){
             label_add_ok:
             setcookie('newgoods',$goodsid);
             echo '订单商品添加成功！<OK>';
           }else echo '订单商品未改变！';
        }        
        else{
          $sql.=',ordername=\''.$OrderName.'\'';
          $goodsid=$conn->query('select id from mg_ordergoods where ordername is null limit 1')->fetchColumn(0);
          if($goodsid && $conn->exec('update '.$sql.' where ordername is null && id='.$goodsid)) goto label_add_ok; 
          else{
            if($conn->exec('insert into '.$sql)){
               $goodsid=$conn->query('select last_insert_id()')->fetchColumn(0);
               if($goodsid) goto label_add_ok; 
            }
          }
        }
      }
      else echo '该商品编号不存在或者已经下架！'; 
    }
  }
  db_close();
  exit(0);
}


$ProductID=@$_GET['productid'];
$OrderName=FilterText(@$_GET['ordername']);
if(!is_numeric($ProductID) || empty($OrderName)) PageReturn('参数错误！！！',0);

db_open();
$row=$conn->query('select mg_users.username,mg_users.grade,mg_orders.state from mg_users inner join mg_orders on mg_users.username=mg_orders.username where mg_orders.ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();  
if($row && ($row['state']==1 || $row['state']==2 || $row['state']==-1)){
  $ShopUserGrade=$row['grade'];
  $order_username=$row['username'];
}
else PageReturn('参数错误！',0);


?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>添加商品到订单</title>
  <STYLE type=text/css>
    BODY {COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
    TD {COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
  </STYLE>
</head>
<body topmargin="0" leftmargin="0" onload="if(document.myfav)document.myfav.confirmbtn.focus();"><?php
 
if(strlen($ProductID)>5)$sql='select * from mg_product where barcode =\''.$ProductID.'\' and recommend>0';
else $sql='select * from mg_product where id='.$ProductID.' and recommend>0';
$res=$conn->query($sql,PDO::FETCH_ASSOC);
$row=$res->fetch(); 
if($row){
    $ProductID=$row['id'];
    $ProductName=$row['name'];
    $ProScore=$row['score'];
    $MarketPrice=$row['price1'];
    $myprice=$row['price'.$ShopUserGrade];
    if(($row['onsale']&0xf)>0 && $ShopUserGrade>2){
      if($row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
    }
    $ProPic=product_pic($ProductID,0);
    $row=$res->fetch();
    if($row){?>
      <script>
       function JumpToProduct(pid){
         document.getElementById("wndlg").innerHTML='<iframe src="?productid='+pid+'&ordername=<?php echo $OrderName;?>" style="width:100%;height:100%;" marginwidth=0 marginheight=0 scrolling="no" Frameborder="no"></iframe>';
       }</script><?php
      echo '<center><div id="wndlg"><select multiple onchange="JumpToProduct(this.value)" size=16 style="width:100%;height:100%">';
      do{ 
        echo '<option value="'.$ProductID.'">'.$ProductName.'</option>';
        $ProductID=$row['id'];
        $ProductName=$row['name'];
        $row=$res->fetch();
      }while($row);
      echo '</select></div></center>';
      db_close();
      exit(0);
    }
}
else{
  PageReturn('该商品不存在或者已经下架！',0);
}

  
$row=$conn->query('select amount,remark from mg_ordergoods where ordername=\''.$OrderName.'\' and productid='.$ProductID,PDO::FETCH_NUM)->fetch();
if($row){
  $amount=$row[0];
  $remark=$row[1];
}?>

<table width="100%" height="100%" border="1"  bordercolor="#FF6600" bgcolor="#FF6600" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
<tr>
   <td colspan="2" height="20"><font color="#FFFFFF"><?php
     if(@$amount)echo '<b>您选择的商品之前已经在订单上，您现在可以修改购买数量或者备注信息。</b>';
     else{
       $amount=1;
       echo '<b>请核准该商品的价格和数量，然后点击[添加到订单]. </b>';
     }?></font> 
    </td>
  </tr>
	<tr bgcolor="#f7f7f7"  align="center"><form name="myfav" onsubmit="Submit_AddToOrder(this);return false;">
     <td width="150" bgcolor="#FFFFFF" valign="middle"><img src="<?php echo $ProPic;?>" width="150" height="150" border="0"></td>  
	   <td width="500" bgcolor="#FFFFFF" valign="top">
	   	  <table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
        <tr height="35">
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>商品名称：</strong></td>
          <td colspan="5" bgcolor="#FFFFFF"><div style="WIDTH: 100%; height:13px;OVERFLOW:hidden;"><?php echo $ProductName;?></div></td>
        </tr>
        <tr height="35">
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>商品编号：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo GenProductCode($ProductID);?></td>
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>零售价：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo FormatPrice($MarketPrice);?>&nbsp;元</td>
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>成交价格：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo FormatPrice($myprice);?>&nbsp;元</td>
        </tr>
        <tr height="35">
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>购买数量：</strong></td>
          <td bgcolor="#FFFFFF"><input type="Text" name="amount" value="<?php echo $amount;?>" size="3" maxlength="3" style="text-align:center"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo');else {document.all('ShowTotalPrice').innerText=(<?php echo $myprice;?>*value)+' 元';document.all('ShowTotalScore').innerText=(<?php echo $ProScore;?>*value)+' 分';}" ></td>
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>合计：</strong></td>
          <td bgcolor="#FFFFFF" id="ShowTotalPrice"><?php echo FormatPrice($myprice*$amount);?>&nbsp;元</td>
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>赠送积分：</strong></td>
          <td bgcolor="#FFFFFF" id="ShowTotalScore"><?php echo $amount*$ProScore;?>&nbsp;分</td>
        </tr>
        <tr>
          <td width="17%" align="right" bgcolor="#f7f7f7" valign="top"><strong>商品备注：</strong></td>
          <td colspan="5" bgcolor="#FFFFFF">
            <textarea name="remark" rows="1" cols="20" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%; font-size: 9pt; border: 1 solid #808080;margin-right:7px;"><?php echo @$remark;?></textarea>
          </td>
        </tr>        
        </table>
     </td>
  </tr>
  <tr>
  	 <td height="30" align="right" bgcolor="#FFCC00" colspan="2">
	     <input name="confirmbtn" type="submit"  value="添加到订单" >&nbsp;
	 </td>
  </tr> </form>
	</table>
<script>
function Submit_AddToOrder(myform){
  var ret=SyncPost("ordername=<?php echo $OrderName;?>&productid=<?php echo $ProductID;?>&amount="+myform.amount.value+"&remark="+encodeURIComponent(myform.remark.value),"?mode=addnew");
  parent.closeDialog(ret);
}
function InitFavRemarks()
{ var q = document.myfav["remark"];
  var b = function(){if(q.value == "") q.style.background = "#FFFFFF url(images/cartbg.gif) center center no-repeat";}
  var f = function(){q.style.background = "#ffffff";}
  q.onfocus = f;
  q.onblur = b;
  b();
}
InitFavRemarks();    	 
</script> 
</body>  
</html><?php db_close();?>
