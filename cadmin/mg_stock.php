<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
$mode=@$_GET['mode'];

if($mode){
  if($mode=='addtocart'){
    $username=FilterText(trim(@$_POST['username']));
    $selectid=FilterText(@$_POST['selectid']);
    $amount=@$_POST['amount'];
    $remark=FilterText(trim(@$_POST['remark']));

    if($username && $selectid && is_numeric($amount) && $amount>0){ 
      $ShopUserID=$conn->query('select id from mg_users where username=\''.$username.'\'')->fetchColumn(0); 	
      if($ShopUserID){
        $jishu=0;
        $res_products=$conn->query('select id from mg_product where id in ('.$selectid.')',PDO::FETCH_NUM);
        foreach($res_products as $row_products){
          $ProductID=$row_products[0];
          $origin_fav=$conn->query('select id,state from mg_favorites where userid='.$ShopUserID.' and productid='.$ProductID,PDO::FETCH_ASSOC)->fetch();
          if($origin_fav){
  	    $state=$origin_fav['state'];
            if(($state&0x2)==0) $state+=0x2; 
            $conn->exec("update mg_favorites set amount=$amount,remark='$remark',state=$state where id={$origin_fav['id']}"); 
          }
          else{
            $sql="mg_favorites set userid=$ShopUserID,productid=$ProductID,amount=$amount,remark='$remark',state=2";
            if(!$conn->exec('update '.$sql.' where state=0 limit 1')){
              if(!$conn->exec('insert into '.$sql)) $errr='insert into '.$sql;
            }
          }
          $jishu++;
        }
        echo '选定的'.$jishu.'件商品已成功加入购物车！<OK>'.@$errr;
      }
      else echo '用户名不存在！';    
    }else echo '参数无效！';
  }
  else if ($mode=='addtoorder'){
    $ordername=FilterText(trim(@$_POST['ordername']));
    $selectid=FilterText($_POST['selectid']);
    $amount=@$_POST['amount'];
    $remark=FilterText(trim($_POST['remark']));
    if($ordername && $selectid && is_numeric($amount) && $amount>0){ 
      $row=$conn->query('select mg_orders.id,mg_orders.state,mg_orders.operator,mg_orders.totalscore,mg_orders.totalprice,mg_orders.username,mg_users.grade from mg_orders inner join mg_users on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC)->fetch();
      if($row){
        $orderstate=$row['state'];
  	if($orderstate==-1 || $orderstate==1 || $orderstate==2){
          if($row['operator']==$AdminUsername ||$row['username']==$AdminUsername || CheckPopedom('STOCK')){
            $jishu=0;
            $ShopUserGrade=$row['grade'];
            $res_product=$conn->query('select * from mg_product where id in ('.$selectid.')',PDO::FETCH_ASSOC);
            foreach($res_product as $row_product){
              $myprice = $row_product['price'.$ShopUserGrade];
              if(($row_product['onsale']&0xf)>0 && $ShopUserGrade>2){
                 if($row_product['onsale']>time() && $row_product['price0']<$myprice) $myprice=$row_product['price0'];
              }
              $sql="mg_ordergoods set productname='{$row_product['name']}',amount=$amount,score={$row_product['score']},price=$myprice,remark='$remark',audit=1";              
              if(!$conn->exec('update '.$sql.' where ordername=\''.$ordername.'\' and productid='.$row_product['id'])){
                $sql.=",ordername='$ordername',productid={$row_product['id']}";
                if(!$conn->exec('update '.$sql.' where ordername is null or ordername=\'\' limit 1') && !$conn->exec('insert into '.$sql)){
                   echo '未知错误！'; 
                   break;
                }            
              } 
              $jishu++;
            }
            echo '选定的'.$jishu.'件商品已成功加入订单！'; 
            if($jishu>0)$conn->exec('update mg_orders set totalprice=null,totalscore=null where id='.$row['id']);                  
          }else echo '您无权限修改该订单！';
        }else echo '该订单状态不允许添加商品！';
      }else echo '订单号不存在！';
    }else echo '参数无效！';
  }

  CloseDB();
  exit(0);
}


$LocalDepot=@$_GET['depot'];
if(is_numeric($LocalDepot)){
   $LocalDepot=(int)$LocalDepot;
   if($LocalDepot<=0) goto set_default_depot;
}
else{
  set_default_depot:
  $LocalDepot=$conn->query("select depot from mg_admins where username='$AdminUsername'")->fetchColumn(0);
}
$LocalStockName='stock'.$LocalDepot;


$sort_name=@$_COOKIE['sort_name'];	  
if($sort_name){
  $sort_keys=array('addtime','id','stock0','recommend','name','price3','weight',$LocalStockName);
  $keycount=count($sort_keys);
  for($i=0;$i<$keycount;$i++){
   if($sort_name==$sort_keys[$i])break;
  }
  if($i==$keycount) goto label_defaut_sort;
}
else{
  label_defaut_sort:$sort_name='addtime';
}

$sort_order=@$_COOKIE['sort_order'];
if($sort_order!='asc' && $sort_order!='dec') $sort_order='desc';
$sql_sort_code='order by '.$sort_name.' '.$sort_order;

function sorts($selec){
   global $conn,$CatList;
   $res=$conn->query('select id from mg_category where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
   foreach($res as $row){
      $brandid = $row[0];
      $CatList = $CatList.','.$brandid;
      sorts($brandid);
   }
}

$cid=@$_GET['cid'];
if(is_numeric($cid) && $cid>0){
  $CatList=(string)$cid;
  sorts($cid);
  $strCat = 'and brand in ('.$CatList.') ';
}
else{
  $cid=0;
  $strCat = '';
}

$depot_options='';
$res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
foreach($res as $row){
  if($row[0]==$LocalDepot){
    $LocalDepotName=$row[1];
    $depot_options.='<option value="'.$row[0].'" selected>'.$row[1].'</option>';
  }
  else $depot_options.='<option value="'.$row[0].'">'.$row[1].'</option>';
}

$Own_popedomProduct=CheckPopedom('PRODUCT');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="editproduct.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/brandsel.js" type="text/javascript"></SCRIPT>
<title>商品库存清单</title>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>  
    <td height="20" align="right" background="images/topbg.gif" bgcolor="#F2F2F2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000><?php echo $LocalDepotName;?>-商品库存清单</font></b></td>
        <td width="45%"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><script language="javascript">CreateBrandSelection("brand",<?php echo $cid;?>,"--------商品分类过滤--------","self.location.href='?depot=<?php echo $LocalDepot;?>&cid='+this.value;");</script>
            <select onchange="self.location.href='?cid=<?php echo $cid;?>&depot='+this.value;"><?php echo $depot_options;?></select></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr> 
     <td height="100" bgcolor="#FFFFFF" align="center"><?php

$SearchTitles=array('name'=>'商品名称','productid'=>'商品编号','barcode'=>'商品条码');
$keyvalue=FilterText(trim(@$_GET['kv']));
if($keyvalue){
  $keyname=FilterText(trim(@$_GET['kn']));
  switch($keyname){
          case 'name':
       		     if(strpos($keyvalue,' ')>0){
       	               $sql='where';
                       $key_list=explode(' ',$keyvalue);
                       for($i=0;$i<count($key_list);$i++){
                         $subkey=trim($key_list[$i]);
                         if($subkey){ 
                           if($i>0) $sql.=' and';
                          $sql.=' name like \'%'.$subkey.'%\' ';
                         }
                       }
                     }
                     else{
                       $sql='where name like \'%'.$keyvalue.'%\' ';
                     }
                     break;
          case 'barcode':
       		     $sql='where barcode= \''.$keyvalue.'\'';
                     break; 
       	  case 'productid':
       	  	     if(is_wholenumber($keyvalue) && $keyvalue>0){
       		       $sql='where id='.$keyvalue;
                       $keyvalue=GenProductCode($keyvalue); 
                     }
                     else{
       		       $sql='where id=0';
                     }
                     break;
          default:   PageReturn('参数错误',0);	
       }
       echo '按<b>'.$SearchTitles[$keyname].'</b>搜索，您查询的关健字是：<font color="red">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
     }
     else{
       $keyname='';
       $sql='where recommend>0 '.$strCat;
     }
     while(1){
       $res=page_query('select id,name,recommend,barcode,'.$LocalStockName.',price3,weight,onsale,addtime','from mg_product',@$sql,$sql_sort_code,15);
       if(!$res && $keyname=='name' && is_numeric($keyvalue)){
         if(strlen($keyvalue)<=8){ 
           $sql='where id='.$keyvalue;
           $keyname='productid'; 
         }
         else{ 
       	   $sql='where barcode= \''.$keyvalue.'\'';
           $keyname='barcode'; 
         }
       }
       else break;
     }
     if(!$res){ 
       echo '<p align="center">找不到相关记录！<br><br><a href="javascript:history.go(-1)" style="color:#FF0000;text-decoration:underline">点击返回上一页</a></p>';
     }
     else{?>
          <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#f7f7f7" align="center" height="20"><form name="cartform" method="post" action="">
            <td width="4%"  background="images/topbg.gif" height="25" ><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)" /></td>
            <td width="8%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('id')" style="cursor:pointer"><strong>编号</strong><?php if($sort_name=='id') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="45%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('name')" style="cursor:pointer"><strong>商品名称</strong><?php if($sort_name=='name') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="7%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('<?php echo $LocalStockName;?>')" style='cursor:pointer'><strong>当地库存</strong><?php if($sort_name==$LocalStockName) echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="7%" background="images/topbg.gif"  title="点击排序" onclick="ProductResort('price3')" style="cursor:pointer" nowrap><strong>批发价</strong><?php if($sort_name=='price3') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="7%" background="images/topbg.gif"  title="点击排序" onclick="ProductResort('weight')" style="cursor:pointer" nowrap><strong>重量(g)</strong><?php if($sort_name=='weight') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="10%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('barcode')" style="cursor:pointer"><strong>条码</strong><?php if($sort_name=='barcode') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="12%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('addtime')" style="cursor:pointer" nowrap><strong>上架时间</strong><?php if($sort_name=='addtime') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td> 
           </tr><?php
     foreach($res as $row){
       $productid=$row['id'];
       $onsale=$row['onsale']&0xf;
       $FontColor=($row['recommend']>0)?'#000000':'#BFBFBF';?>
            <tr height="25" align="center" bgcolor="#F7F7F7" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
              <td><input name="selectid[]" type="checkbox" value="<?php echo $productid;?>" onclick="mChk(this)"></td>
              <td><a href="mg_stocklog.php?id=<?php echo $productid;?>&depot=<?php echo $LocalDepot;?>" style="color:<?php echo $FontColor;?>;text-decoration:underline;"><?php echo GenProductCode($productid);?></a></td>
              <td align="left"><a href="<?php echo GenProductLink($productid);?>" target="_blank"><font color="<?php echo $FontColor;?>"><?php echo $row['name'];?></font></a><?php if($onsale>0) echo '<img src="images/onsale'.$onsale.'.gif" width=16 height=16 alt="特价指数为'.$onsale.'">';?></td>
              <td style="color:<?php echo $FontColor;?>;"><?php echo $row[$LocalStockName];?></td>
              <td style="color:<?php echo $FontColor;?>;"><?php echo FormatPrice($row['price3']);?></td>
              <td style="color:<?php echo $FontColor;?>;" <?php if($Own_popedomProduct) echo 'class="dummyLink" onclick="ChangeWeight(this,'.$productid.')"';?> >&nbsp;<?php echo $row['weight'];?>&nbsp;</td>                
              <td style="color:<?php echo $FontColor;?>;" <?php if($Own_popedomProduct) echo 'class="dummyLink" onclick="ChangeBarcode(this,'.$productid.')"';?> >&nbsp;<?php echo $row['barcode'];?>&nbsp;</td>                
              <td style="color:<?php echo $FontColor;?>;" nowrap><?php echo ($row['recommend']>0)?date('Y-m-d H:i:s',$row['addtime']):'已下架';?></td>
            </tr><?php
          }?>
          <tr bgcolor="#f7f7f7" >
            <td nowrap colspan="2" align="center"><input type="button" name="CartButton" value="放入购物车" onclick="AddToCart(this.form)">&nbsp;<input type="button" name="OrderButton" value="加入订单" onclick="AddToOrder(this.form)">&nbsp;</td>
            <td align="center" colspan="6"><script language="javascript"><?php echo 'GeneratePageGuider("kn='.$keyname.'&kv='.rawurlencode($keyvalue).'&cid='.$cid.'&depot='.$LocalDepot.'",'.$total_records.','.$page.','.$total_pages.');';?></script></td>
         </tr></table><?php
    }?>
    </td>
  </tr></form>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#FFFFFF"> 
  <td align="center"><form name="schform" method="get" style="margin:0px"><b>按</b><select name="kn"><?php
foreach($SearchTitles as $s_key=>$s_value){
  $selectcode=($s_key==$keyname)?' selected':'';
  echo '<option value="'.$s_key.'"'.$selectcode.'>'.$s_value.'</option>';
}?></select><input name="kv" type="text" style="color:#FF0000" value="<?php echo $keyvalue;?>"> <input type="submit" value=" 搜 索 "></form></td>
</tr>
</table>
</body>
</html><?php CloseDB();?>
