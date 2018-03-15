<?php require('../include/conn.php');
$action=$_GET['action']; 
if(!CheckLogin(0)){
  if($action=='getlist')echo '<div style="width:798px;height:40px;background-image:url(images/kubars/kubar_cart.gif);"></div>';
  echo '<script LANGUAGE="javascript">alert("您没有登录，无法使用该功能！");</script>';
  exit(0);
}

OpenDB();
switch($action){
  case 'del':  DeleteFromCart();break;
  case 'save': UpdateCart();break;
  case 'seltofav': SelToFav();break;
  default: GetList();break;
}
CloseDB();

function DeleteFromCart(){
  global $conn,$LoginUserID;
  $selectid=$_POST['selectid'];
  if(empty($selectid)){
    echo '<script LANGUAGE="javascript">alert("您没有选择要删除的商品！");history.go(-1);</script>';
  }
  else if($conn->exec('update `mg_favorites` set state=state-2 where userid='.$LoginUserID.' and productid in ('.implode(',',$selectid).') and (state=2 or state=3)')){
    echo '<script LANGUAGE="javascript">alert("选定的商品已经从您的购物车中删除！");parent.show_mycart();</script>'; 
    return true;
  } 
  return false;
}
  
function UpdateCart(){
  global $conn,$LoginUserID;
  $selectid=$_POST['selectid'];
  if(empty($selectid)){
    echo '<script LANGUAGE="javascript">alert("您没有选择需要更新的商品！");history.go(-1);</script>'; 
  }
  else{
    foreach($selectid as $itemid){
      if(is_numeric($itemid)){
       $amount=$_POST['amount_'.$itemid];
       if(is_numeric($amount)){
         $remark=FilterText(trim($_POST['remark_'.$itemid]));
         if(strlen($remark)>255)$remark=substr($remark,0,250).'...';
         $conn->exec("update `mg_favorites`set amount=$amount,remark='$remark' where productid=$itemid and userid=$LoginUserID");
       }
      }
    }
    echo '<script LANGUAGE="javascript">alert("更新成功！");parent.show_mycart();</script>'; 
  } 
}


function SelToFav(){
  global $conn,$LoginUserID;
  $selectid=$_POST['selectid'];
  if(empty($selectid)){ 
    echo '<script LANGUAGE="javascript">alert("您没有选择所要购买的商品！");history.go(-1);</script>'; 
  }
  else{
    $conn->query('update `mg_favorites` set state=3 where userid='.$LoginUserID.' and productid in ('.implode(',',$selectid).') and state=2');
    echo '<script LANGUAGE="javascript">alert("选定的商品已经加入收藏架");parent.show_myfav();</script>';
  }
} 

function GetList(){
  global $conn,$LoginUserID,$LoginUserGrade;
  $res=$conn->query('select `mg_favorites`.productid,`mg_favorites`.amount,`mg_favorites`.state,`mg_favorites`.remark,`mg_product`.name,`mg_product`.price0,`mg_product`.price1,`mg_product`.price'.$LoginUserGrade.' as myprice,`mg_product`.score,`mg_product`.stock0,`mg_product`.onsale from (`mg_favorites` inner join `mg_product` on  `mg_favorites`.productid=`mg_product`.id) inner join `mg_brand` on `mg_brand`.id=`mg_product`.brand where `mg_favorites`.userid='.$LoginUserID.' and (`mg_favorites`.state=2 or `mg_favorites`.state=3) order by `mg_brand`.sortindex,`mg_product`.name',PDO::FETCH_ASSOC);	
  $row=$res->fetch();
  if(empty($row))echo '<br><br><p align=center>购物车为空！</p><br><br>';
  else{?>
<table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
  <td height=20 valign="bottom">

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
     <td><font color="#FF0000">您选择的商品已经放入购物车，您现在可以前往收银台支付，也可以继续购物。</font></td>
     <td align="right"><a href="<?php echo WEB_ROOT;?>user/downcart.php"><img src="images/xls.gif" border="0" width="16" height="16" align=absMiddle>下载购物清单...</a></td>
   </tr>
   </table>

   </td>
</tr>
<tr>
   <td>

   <form method="POST" action="mycart.php" target="dummyframe" style="margin:0px">
   <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%" bgcolor="#f2f2f2">
   <tr bgcolor="#f7f7f7" height="25" align="center"> 
     <td width="4%"><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
     <td width="61%"><strong>商 品 名 称</strong></td>
     <td width="9%"><strong>零售价</strong></td>
     <td width="9%"><strong>您的价格</strong></td>
     <td width="7%"><strong>数 量</strong></td>
     <td width="10%" align="center"><strong>备 注</strong></td>
   </tr><?php
$TotalSum=0;
$TotalScore=0;
do{
  echo '<TR height="20" align="center" bgcolor="#FFFFFF" onMouseOut="this.bgColor=\'#FFFFFF\'" onMouseOver="this.bgColor=\'#FFFFBB\'" ><td><input type="checkbox" name="selectid[]" value="'.$row['productid'].'"></td><td align="left">';
  $ProScore = $row['score']; 
  $Amount = $row['amount'];
  $remark = $row['remark'];
  $myprice = $row['myprice']; 
  if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
  $TotalSum += $myprice*$Amount;
  $TotalScore += $ProScore*$Amount;
        
  if(WEB_SITE>1)$ProductTitle='<a href="'.WEB_ROOT.'product.htm?id='.$row['ProductID'].'" target="_blank"';
  else $ProductTitle='<a href="/products/'.$row['productid'].'.htm" target="_blank"';  

  if($row['state']==3) $ProductTitle.=' style="color:#FF6600"';
 
  if($row['stock0']<1)#if($row['stock0']<$Amount) 
  { $ProductTitle .= ' style="text-decoration:line-through" title="库存不足，目前该商品库存'.$row['stock0'].'件，请联系客服核实！">'.$row['name'].'<img src="images/lack.gif" border=0 width=16 height=16></a>';
  }
  else{
   $ProductTitle .= '>'.$row['name'].'</a>';
  }
  echo $ProductTitle;
  echo '</td><td><STRIKE>'.round($row['price1'],2).'</STRIKE></td><td><font color="#FF0000">'.round($myprice,2).'</font></td><td><input type="Text" name="amount_'.$row['productid'].'" value="'.$Amount.'" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  maxlength="3" style="text-align:center;width:100%;height:20px"></td><td><input name="remark_'.$row['productid'].'" maxlength="255" value="'.$remark.'"  style="width:100%;height:20px"  onFocus="this.select()" ></td></TR>';
  $row=$res->fetch();
}while($row);?>
   <tr bgcolor="#F7F7F7"> 
     <td height="25" colspan="2" align="center">    
     <input type="button" value="保存修改" onclick="SaveProductInCart(this.form)"> 
     <input type="button" value=" 删除商品 " onclick="DeleteProductInCart(this.form)">
     <input type="button" value="加入收藏架" onclick="SelToFav(this.form)">
     <input type="button" value=" 去收银台 " onclick="window.open('<?php echo WEB_ROOT;?>paybill.php?handle='+Math.random());"> 
     </td>
     <td colspan="4" align="right">
       价格总计：<font color="#FF0000"><b><?php echo round($TotalSum,2);?></b></font>&nbsp;元 | 积分总计：<?php echo $TotalScore;?>&nbsp;分
     </td>
   </tr>
   </table>
   </form>
   </td>
 </tr>
 <tr>
   <td> 
   	   注：(1) 商品名称显示为<font color="#FF6600">黄色</font>表示该商品同时在您的购物车和收藏架中。<br>
       	   &nbsp; &nbsp; (2) 修改完商品数量或者备注信息后，必须在需要修改的商品前面<font color="#FF0000">打勾</font>，并且单击“<font color="#FF0000">保存修改</font>”才能生效。<br>
       	   &nbsp; &nbsp; (3) 您购物车中的商品在您递交订单前会被<font color="#FF0000">永久保存</font>，您可以随时登录本站并向您的购物车续添商品。<br>
       	   &nbsp; &nbsp; (4) 您在最终确定好购买的商品后，请务必前往收银台提交订单，否则系统无法将商品积分累计到您的帐户。<br>
   </td>
</tr>
</table><br><?php
     }
}


?>
