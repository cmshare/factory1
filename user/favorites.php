<?php require('../include/conn.php');
$action=$_GET['action'];
CheckLogin(0);

switch($action){
  case 'add':AddToFav();break;
  case 'del':DelFromFav();break;
  case 'seltocart':SelTotCart();break;
  case 'getlist':GetList();break;
  default: header('Location:'.WEB_ROOT.'usrmgr.htm?action=myfav');break;
}

function AddToFav(){
  global $conn,$LoginUserID;
  if($LoginUserID==0){
    echo '您没有登录，无法使用该功能！';
    return false;
  }
  $ProductID=@$_GET['prodid'];
  if(!is_numeric($ProductID) || $ProductID<=0){ 
    echo '参数错误！';
    return false;
  }
  OpenDB();
  $row=$conn->query('select id,state from `mg_favorites` where userid='.$LoginUserID.' and productid='.$ProductID,PDO::FETCH_NUM)->fetch();
  if($row && $row[1])
  { if(($row[1]%2)!=1){
      $conn->exec('update `mg_favorites` set state='.($row[1]+1).' where id='.$row[0]);
      echo '收藏成功！';	
    }
    else echo '您已经收藏该商品信息,请勿重复收藏！';
  }
  else
  { $sql='`mg_favorites` set userid='.$LoginUserID.',productid='.$ProductID.',state=1';
    if($conn->exec('update '.$sql.' where state=0 limit 1')||$conn->exec('insert into '.$sql))
    { echo '收藏成功！';	
    }
  }
  CloseDB();
}

function DelFromFav(){
  global $conn,$LoginUserID;
  if($LoginUserID==0){ 
    echo '<script LANGUAGE="javascript">alert("您没有登录，无法使用该功能！");</script>';
    return false;
  }
  $selectid=$_POST['selectid'];
  if(empty($selectid)){
    echo '<script LANGUAGE="javascript">alert("您没有选择要删除的商品！");history.go(-1);</script>'; 
    return false;
  }
  OpenDB();
  $conn->exec('update `mg_favorites` set state=state-1 where userid='.$LoginUserID.' and productid in ('.implode(',',$selectid).') and (state=1 or state=3)');
  echo '<script LANGUAGE="javascript">alert("选定的商品已经从您的收藏架中删除！");parent.show_myfav();</script>'; 
  CloseDB();
} 

function SelTotCart(){
  global $conn,$LoginUserID;
  if($LoginUserID==0){
    echo '<script LANGUAGE="javascript">alert("您没有登录，无法使用该功能！");</script>';
    return false;
  }
  $selectid=$_POST['selectid'];
  if(empty($selectid)){
    echo '<script LANGUAGE="javascript">alert("您没有选择所要购买的商品！");history.go(-1);</script>'; 
    return false;
  }
  OpenDB();
  $conn->exec('update `mg_favorites` set state=3,amount=1,remark=null where userid='.$LoginUserID.' and productid in ('.implode(',',$selectid).') and state=1');
  echo '<script LANGUAGE="javascript">alert("选定的商品已经放入购物车!"); parent.show_mycart();</script>'; 
  CloseDB();
}

function GetList(){
  global $conn,$LoginUserID,$LoginUserGrade;
  echo '<div style="width:798px;height:40px;background-image:url(images/kubars/kubar_myfav.gif);"></div>';
  if($LoginUserID==0){
    echo '<br><br><br><p align="center">请先登录</p><br><br><br>';
    return false;
  }
  OpenDB();
  $sql='select `mg_favorites`.productid,`mg_favorites`.state,`mg_product`.name,`mg_product`.onsale,`mg_product`.price0,`mg_product`.price1,`mg_product`.price'.$LoginUserGrade.' as myprice from (`mg_favorites` inner join `mg_product` on `mg_favorites`.productid=`mg_product`.id) inner join `mg_brand` on `mg_brand`.id=`mg_product`.brand where `mg_favorites`.userid='.$LoginUserID.' and (`mg_favorites`.state=1 or `mg_favorites`.state=3) order by `mg_brand`.sortindex,`mg_product`.name';	
  $res=$conn->query($sql,PDO::FETCH_ASSOC);
  $row=$res->fetch();
  if(empty($row)){
     echo '<br><br><p align=center>收藏架为空！</p><br><br>';
  }
  else{?>
    <form method="post" action="favorites.php" target="dummyframe" style="margin:0px">
    <TABLE  border="0" cellSpacing=1 cellPadding=3 width="770"  align="center" bgColor="#f2f2f2">
    <TR bgcolor="#F0F0F0" align="center">
      <TD width="30"><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)"></TD>
      <TD width="80"><b>商品编号</b></TD>
      <TD width="480" align="left"><b>商品名称</b></TD>
      <TD width="80"><b>零售价</b></TD>
      <TD width="80"><b>您的价格</b></TD>
    </TR><?php
    do{
      $myprice=$row['myprice'];             
      if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
      echo '<TR height="20" align="center" bgcolor="#FFFFFF" onMouseOut="this.bgColor=\'#FFFFFF\'" onMouseOver="this.bgColor=\'#FFFFBB\'">';
      echo '<TD><input type="checkbox" name="selectid[]" value="'.$row['productid'].'"></TD><TD>'.substr('0000'.$row['productid'],-5).'</td><TD align="left">';
      if(WEB_SITE>1)echo '<a href="'.WEB_ROOT.'product.htm?id='.$row['productid'].'" target="_blank">';
      else echo '<a href="/products/'.$row['productid'].'.htm" target="_blank">';
      echo ($row['state']==3)?'<font color=#FF6600>'.$row['name'].'</font>':$row['name'];
      echo '</a></TD><TD>￥'.round($row['price1'],2).'元</TD><TD><font color="#FF0000">￥'.round($myprice,2).'元</font></TD></TR>';
      $row=$res->fetch();
    }while($row);?>
    <TR align=middle bgcolor="#F7F7F7">
      <TD colspan="5" align="left">
      <TABLE  border=0 cellSpacing=0 cellPadding=0 width="100%">
      <tr><td nowrap width="60%">&nbsp; 注：<font color="#FF6600"><b>黄色</b></font>字体标记表示该商品同时在您的购物车和收藏架中。</td>
          <td nowrap width="40%" align="right"><input name="Submit3" type="button" value="从收藏架删除" onclick="DeleteFromFav(this.form)">
             <input name="Submit3" type="button" value=" 放入购物车 " onclick="SelToCart(this.form)">&nbsp; 
           </td>
      </tr>
      </TABLE>
      </td>      
    </TR>
    </TABLE>
    </form>
    <br><?php
  }
  CloseDB();
}
?>
