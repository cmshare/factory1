<?php require('../include/conn.php');

function DlgReturn($info){
  echo '<table width=100% height=100% border=0 bgcolor="#dfdfdf"><tr><td align=center valign=middle><font color="#FF0000">'.$info.'</font> <input type="button" value=" 确定 " onclick="parent.closeDialog()"></td></tr></table>';
  CloseDB();
  exit(0);
}

function  PageHalt($errmsg){
  echo $errmsg;
  CloseDB();
  exit(0);
}
if(!CheckLogin((0)))DlgReturn('您没有登录，无法使用该功能！');

$ProductID=$_GET['id'];
if(!is_numeric($ProductID) || $ProductID<=0)DlgReturn('参数错误！');

OpenDB();
if(@$_GET['action']=='addsave'){
  $amount=trim($_POST['amount']);
  if(!is_numeric($amount) || $amount<1) PageHalt('购买数量无效！');
  
  $remark=FilterText(trim($_POST['remark']));
  if(strlen($remark)>255) PageHalt('备注信息不能超过255个字！');
  
  $stock0=$conn->query('select stock0 from `mg_product` where id='.$ProductID.' and recommend>0')->fetchColumn(0);
  if($stock0===FALSE) PageHalt('该商品不存在或者已经下架！');

  #暂时不检查库存
  #if($stock0<1) PageHalt('对不起，该商品暂时库存不足，请过段时间再来购买该商品！');
  #else if($amount>$stock0)PageHalt('对不起，该商品暂时库存只有'.$stock0.'件！');

  $row=$conn->query('select id,state,remark from `mg_favorites` where userid='.$LoginUserID.' and productid='.$ProductID,PDO::FETCH_ASSOC)->fetch();
  if($row){
    $state=$row['state'];
    if(!($state&0x2)){
      $state+=2;
      echo '选定的商品已经成功放入购物车！';	
    }
    else echo '选定的商品已经成功更新至购物车！';
    $conn->exec("update `mg_favorites` set state=$state,amount=$amount,remark='$remark' where id={$row['id']}");
  } else{ 
    $sql="`mg_favorites` set userid=$LoginUserID,productid=$ProductID,amount=$amount,remark='$remark',state=2"; 
    if($conn->exec('update '.$sql.' where state=0 limit 1') || $conn->exec('insert into '.$sql)) echo '选定的商品已经成功放入购物车！';
  }
}#end of{if action=addsave}
else{
  $row=$conn->query('select * from `mg_favorites` where userid='.$LoginUserID.' and productid='.$ProductID.' and (state&0x2)',PDO::FETCH_ASSOC)->fetch();
  if($row){
    $amount=$row['amount'];
    $remark=$row['remark'];
    $ExistInCart=true;
  }
  else{
    $amount=@$_POST['amount'];
    if(!is_numeric($amount) || $amount<1)$amount=1; 
    $remark=''; 
    $ExistInCart=false;
  }

  $row=$conn->query('select name,score,stock0,price0,price1,price'.$LoginUserGrade.' as myprice,onsale from `mg_product` where id='.$ProductID.' and recommend>0',PDO::FETCH_ASSOC)->fetch();
  if(empty($row)) PageHalt('该商品不存在或者已经下架！');

  #暂时不检查库存
  #if($row['stock0']<1)DlgReturn('对不起，该产品库存暂时不足，请过段时间再来购买该商品！');
?><html>
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<SCRIPT language="JavaScript" src="cmbase.js" type="text/javascript"></SCRIPT>
<title>添加商品到购物车 －【<?php echo WEB_NAME;?>】</title>
<STYLE type=text/css>
   BODY {margin:0px;COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
   TD {COLOR: #000000; FONT-size: 9pt; TEXT-DECORATION: none}
</STYLE>
</head>
<body>
<form name="cartform" onsubmit="return SaveToCart(this)">
<table width="100%" height="100%" style="border: 1px solid #FF6600;background-color:#FF6600;" cellpadding="1" cellspacing="2">
<tr height="20">
  <td colspan="2" style="line-height:200%;color:#FFFFFF;font-weight:bold"><?php
if($ExistInCart)echo '<img src="../images/mespic.gif" align="absMiddle">该商品已经在购物车上了，您现在可以修改购买数量或者备注信息。';
else echo '<img src="../images/icon_buy.gif" align="absMiddle">请核准该商品的价格和数量，然后点击[放入购物车]. ';
$ProScore=$row['score'];
$myprice=$row['myprice'];             
if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0']; ?>  
  </td>
</tr>
<tr bgcolor="#f7f7f7" align="center">
   <td width="25%" bgcolor="#FFFFFF" valign="middle"><img src="<?php echo product_pic($ProductID,0);?>" width="150" height="150" border="0"></td>  
   <td width="75%" bgcolor="#FFFFFF" valign="top">
   	  <table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
      <tr height="35">
        <td align="right" bgcolor="#f7f7f7" nowrap><strong>商品名称：</strong></td>
        <td colspan="5" bgcolor="#FFFFFF"><div style="WIDTH: 100%; height:13px;OVERFLOW:hidden;"><?php echo $row['name'];?></div></td>
      </tr>
      <tr height="35">
        <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>商品编号：</strong></td>
        <td width="17%" bgcolor="#FFFFFF"><?php echo substr('0000'.$ProductID,-5);?></td>
        <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>零 售 价：</strong></td>
        <td width="17%" bgcolor="#FFFFFF"><?php echo round($row['price1'],2);?>&nbsp;元</td>
        <td width="17%" align="right" bgcolor="#f7f7f7" nowrap><strong>您的价格：</strong></td>
        <td width="17%" bgcolor="#FFFFFF"><?php echo round($myprice,2);?>&nbsp;元</td>
      </tr>
      <tr height="35">
        <td align="right" bgcolor="#f7f7f7" nowrap><strong>订购总数：</strong></td>
        <td bgcolor="#FFFFFF"><input type="Text" name="amount" value="<?php echo $amount;?>" size="3" maxlength="3" style="text-align:center"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo');else {document.all('ShowTotalPrice').innerText=(Math.round(<?php echo round($myprice,2);?>*value*10)/10)+' 元';document.all('ShowTotalScore').innerText=(<?php echo $ProScore;?>*value)+' 分';}" >件</td>
        <td align="right" bgcolor="#f7f7f7" nowrap><strong>合 &nbsp;&nbsp; 计：</strong></td>
        <td bgcolor="#FFFFFF" id="ShowTotalPrice"><?php echo round($myprice*$amount,2);?>&nbsp;元</td>
        <td align="right" bgcolor="#f7f7f7" nowrap><strong>赠送积分：</strong></td>
        <td bgcolor="#FFFFFF" id="ShowTotalScore"><?php echo $amount*$ProScore;?>&nbsp;分</td>
      </tr>
      <tr>
        <td width="17%" align="right" bgcolor="#f7f7f7" valign="top"><strong>商品备注：</strong></td>
        <td colspan="5" bgcolor="#FFFFFF">
          <textarea name="remark" rows="1" cols="20" wrap="VIRTUAL" maxlength="255" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080;margin-right:7px;"><?php echo $remark;?></textarea>
        </td>
      </tr>        
      </table>
   </td>
</tr>
<tr height="30">
	 <td align="right" bgcolor="#FFCC00" colspan="2">
	   <?php if($ExistInCart) echo '<font color="#008800"><b>该商品已存在于购物车，请重新修改数量...</b></font>';?><input type="submit" name="ActionButton" value="<?php echo ($ExistInCart)?'更新':'放入';?>购物车">&nbsp;
	 </td>
</tr>
</table>
</form> 
<script>
function OnGetSaveResult(responsetext){
  if(responsetext){
    var myform=document.forms["cartform"];
    if(responsetext.indexOf("成功")>=0){
       myform.ActionButton.parentNode.innerHTML="<img width=17 height=15 src='../images/pic21.gif'>"+responsetext+"&nbsp; &nbsp; <input name='CloseBtn' type='button' value=' 确认 ' onclick='parent.closeDialog()'>&nbsp; ";
    }
    else{
      myform.ActionButton.value="放入购物车";
      myform.ActionButton.disabled=false;
      myform.amount.disabled=false;
      myform.remark.disabled=false;
    }
  }
} 
function SaveToCart(myform)
{ var amount=myform.amount.value;
  if ( isNaN(amount) || parseInt(amount)<=0){
    alert("数量无效！");
  }
  else{
    myform.ActionButton.value="正在通讯，请稍候...";
    myform.ActionButton.disabled=true;
    myform.amount.disabled=true;
    myform.remark.focus(); /*为了取消myform.amount的选中状态*/
    myform.remark.disabled=true;
    AsyncPost("amount="+amount+"&remark="+encodeURIComponent(myform.remark.value),"?action=addsave&id=<?php echo $ProductID;?>",OnGetSaveResult);
  }
  return false;	
}
function InitCartRemarks(){
  var q = document.cartform["remark"];
  var b = function(){if(q.value == "") q.style.background = "#FFFFFF url(../images/cartbg.gif) center center  no-repeat";}
  var f = function(){q.style.background = "#ffffff";}
  q.onfocus = f;
  q.onblur = b;
  b();
}
InitCartRemarks(); 
</script>	
</body>  
</html><?php
}
CloseDB();?>
