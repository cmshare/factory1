<?php require('../include/conn.php');
if(!CheckLogin(0)){
  echo '<script LANGUAGE="javascript">alert("您没有登录，无法使用该功能！");parent.closeDialog();</script>';
  exit(0);
}
  
$productid=$_GET['productid'];
$ordername=trim(FilterText($_GET['ordername']));
if(!is_numeric($productid) || empty($ordername)){
  echo ' 参数错误！';
  exit(0);
}

OpenDB();

$row=$conn->query('select * from `mg_ordergoods` where ordername=\''.$ordername.'\' and productid='.$productid,PDO::FETCH_ASSOC)->fetch();
if($row){
  $amount=$row['amount'];
  $remark=$row['remark'];
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="化妆品批发,香水批发,精油批发,进口化妆品批发,南京化妆品批发,韩国化妆品批发">
<META http-equiv="Description" content="<?php echo WEB_NAME;?>提供各种进口化妆品批发,韩国化妆品批发,欧美化妆品批发等">
<title>添加商品到订单 －【<?php echo WEB_NAME;?>】</title>   
<STYLE type=text/css>
    BODY {COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
    TD {COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
</STYLE>
</head>
<body topmargin="0" leftmargin="0"><?php
$sql='select id,name,score,stock0,price0,price1,price'.$LoginUserGrade.' as myprice,onsale from `mg_product` where id='.$productid.' and recommend>0';
$row=$conn->query($sql,PDO::FETCH_ASSOC)->fetch();
if(empty($row)){
  echo '<script LANGUAGE="javascript">alert("该商品不存在或者已经下架！");parent.closeDialog();</script>';
  CloseDB();
  exit(0);
}?>
<table width="100%" height="100%" bordercolor="#FF6600" bgcolor="#FF6600" border="1" cellpadding="1" cellspacing="2" bgcolor="#FFFFFF" >
<tr>
  <td colspan="2" height="20"><font color="#FFFFFF"><?php
  if(@$amount) echo '<b>您选择的商品之前已经在订单上，您现在可以修改购买数量或者备注信息。</b>';
  else{
    $amount=1;
    echo '<b>请核准该商品的价格和数量，然后点击[添加到订单]. </b>';
  }
  $ProScore=$row['score'];
  $myprice=$row['myprice'];
  if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
   ?></font></td>
  </tr>
  <tr bgcolor="#f7f7f7" align="center">
     <td width="150" bgcolor="#FFFFFF" valign="middle"><form name="myfav" onsubmit="Submit_AddToOrder(this);return false;">
     	  <img src="<?php echo product_pic($productid,0);?>" width="150" height="150" border="0"></td>  
	   <td width="500" bgcolor="#FFFFFF" valign="top">
  	<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
        <tr height="35">
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>商品名称：</strong></td>
          <td colspan="5" bgcolor="#FFFFFF"><div style="WIDTH: 100%; height:13px;OVERFLOW:hidden;"><?php echo $row['name'];?></div></td>
        </tr>
        <tr height="35">
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>商品编号：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo substr('0000'.$productid,-5);?></td>
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>零 售 价：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo round($row['price1'],2);?>&nbsp;元</td>
          <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>您的价格：</strong></td>
          <td width="17%" bgcolor="#FFFFFF"><?php echo round($myprice,2);?>&nbsp;元</td>
        </tr>
        <tr height="35">
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>购买数量：</strong></td>
          <td bgcolor="#FFFFFF"><input type="Text" name="amount" value="<?php echo $amount;?>" size="3" maxlength="3" style="text-align:center"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo');else {document.all('ShowTotalPrice').innerText=(<?php echo $myprice;?>*value)+' 元';document.all('ShowTotalScore').innerText=(<?php echo $ProScore;?>*value)+' 分';}" ></td>
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>合 &nbsp;&nbsp; 计：</strong></td>
          <td bgcolor="#FFFFFF" id="ShowTotalPrice"><?php echo round($myprice*$amount,2);?>&nbsp;元</td>
          <td align="right" bgcolor="#f7f7f7" nowrap><strong>赠送积分：</strong></td>
          <td bgcolor="#FFFFFF" id="ShowTotalScore"><?php echo $amount*$ProScore;?>&nbsp;分</td>
        </tr>
        <tr>
          <td width="17%" align="right" bgcolor="#f7f7f7" valign="top"><strong>商品备注：</strong></td>
          <td colspan="5" bgcolor="#FFFFFF">
            <textarea name="remark" rows="1" cols="20" maxlength="250" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080;margin-right:7px;"><?php echo @$remark;?></textarea>
          </td>
        </tr>        
        </table>
     </td>
  </tr>
  <tr>
  <script>
    function Submit_AddToOrder(objform)
  	{ var args=new Array(3);
  		args[0]=<?php echo $productid;?>;
  		args[1]=objform.amount.value; 
  		/*
  		if(args[1]><?php echo $row['stock0'];?>)
  		{ alert('库存不足！');
  			return;
  		}
  		*/
  		args[2]=objform.remark.value;
  		//window.returnValue=args;
  		parent.closeDialog(args);
  	}
    function InitFavRemarks()
	  { var q = document.myfav["remark"];
      var b = function(){if(q.value == "") q.style.background = "#FFFFFF url(../images/cartbg.gif) center center no-repeat";}
      var f = function(){q.style.background = "#ffffff";}
      q.onfocus = f;
      q.onblur = b;
      b();
    }
    InitFavRemarks();   	    
  </script>
  	 <td height="30" align="right" bgcolor="#FFCC00" colspan="2">
	     <input type="submit"  value="添加到订单">&nbsp;
		 </td>
  </tr> </form>
	</table>
	 
	</body>  
  </html><?php
  CloseDB();?>
