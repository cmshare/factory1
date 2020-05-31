<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();
session_start();

$mode=@$_GET['mode'];
if($mode){
  if($mode=='withdraw'){
     $selectid=@$_POST['selectid'];
     if(is_array($selectid)){
       $idlist=implode(',',$selectid);
       $ret=$conn->exec('update mg_product set recommend=0,addtime=unix_timestamp() where id in ('.$idlist.') and recommend>0');
       if(is_numeric($ret)) PageReturn('完成'.$ret.'件商品下架！');
     }
     else if(is_numeric($selectid) && $selectid>0){
       if($conn->exec('update mg_product set recommend=0,addtime=unix_timestamp() where id='.$selectid.' and recommend>0')) echo '商品下架成功<OK>';
     }
     else  PageReturn("没有选择操作对象！",-1);
  }
  else if($mode=='forward'){
     $selectid=@$_POST['selectid'];
     if(is_array($selectid)){
       $idlist=implode(',',$selectid);
       $ret=$conn->exec('update mg_product set recommend=1,addtime=unix_timestamp() where id in ('.$idlist.') and recommend=0');
       if(is_numeric($ret)) PageReturn('完成'.$ret.'件商品上架！');
     }
     else if(is_numeric($selectid) && $selectid>0){
       if($conn->exec('update mg_product set recommend=1,addtime=unix_timestamp() where id='.$selectid.' and recommend=0')) echo '商品上架成功<OK>';
     }
     else PageReturn("没有选择操作对象！",-1);
  }
  else if($mode=='onsale'){
     $onsale=@$_POST['newvalue'];
     $selectid=@$_POST['selectid'];
     if(is_numeric($selectid) && $selectid>0 && is_numeric($onsale)){
       if($conn->exec('update mg_product set onsale=(onsale&~0xf|'.($onsale&0xf).') where id='.$selectid)) echo '商品特价指数修改成功<OK>';
     }else echo '参数无效';
  }
  else if($mode=='batchonsale'){
     $onsale=$_GET['onsale'];
     $selectid=@$_POST['selectid'];
     if(is_array($selectid) && is_numeric($onsale)){
       $idlist=implode(',',$selectid);
       if($conn->exec('update mg_product set onsale=(onsale&~0xf|'.($onsale&0xf).') where id in ('.$idlist.')'))PageReturn('商品特价指数修改成功！');
     }
     else PageReturn("参数错误！",-1);
  }
  else if($mode=='recommend'){
     $selectid=@$_POST['selectid'];
     $newvalue=@$_POST['newvalue'];
     if(is_numeric($selectid) && $selectid>0 && is_numeric($newvalue)){
       if($conn->exec('update mg_product set recommend ='.$newvalue.' where id='.$selectid)) echo '商品推荐指数修改成功！<OK>';
     }
     else echo '参数无效';
  }
  else if($mode=='score'){
    $selectid=@$_POST['selectid'];
    $newvalue=@$_POST['newvalue'];
    if(is_numeric($selectid) && $selectid>0 && is_numeric($newvalue)){
      if($conn->exec('update mg_product set score ='.$newvalue.' where id='.$selectid)) echo '商品积分修改成功！<OK>';
    }
    else echo '参数无效';
  }
  else if($mode=='barcode'){
    $selectid=@$_POST['selectid'];
    if(is_numeric($selectid) && $selectid>0){
	$newvalue=trim(@$_POST['newvalue']);
	if($newvalue=='' || is_numeric($newvalue)){
   	  if($conn->exec("update `mg_product` set barcode ='$newvalue' where id=$selectid")) echo '商品条码设置成功！<OK>';
        }
    }
  }
  else if($mode=='weight'){
    $selectid=@$_POST['selectid'];
    $newvalue=@$_POST['newvalue'];
    if(is_numeric($selectid) && $selectid>0 && is_numeric($newvalue)){
      if($conn->exec('update mg_product set weight ='.$newvalue.' where id='.$selectid))echo '商品重量修改成功<OK>';
    }
    else echo '参数无效';
  }
/*
  else if($mode=='batchbrand'){
    $selectid=@$_POST['selectid'];
    $newbrand=@$_GET['newvalue'];
    if(empty($selectid)) PageReturn("没有选择操作对象！",-1);
    else if(is_numeric($newbrand) && $newbrand>0){
      $idlist=implode(',',$selectid);
      if($conn->exec('update mg_product set brand='.$newbrand.' where id in ('.$idlist.')'))PageReturn('修改成功！');
    }
    else PageReturn('参数错误！');
  }
*/
  else if($mode=='delete'){
    if(CheckPopedom('SYSTEM')){
      $selectid=$_POST['selectid'];
      if($selectid && is_array($selectid)){
        $idlist=implode(',',$selectid);
        if($conn->exec('update mg_product set recommend=-1 where id in ('.$idlist.') and recommend=0')) PageReturn('商品删除成功！');
      }
    }
  }
  db_close();
  exit(0);
}  

//$_SESSION['showcost']=true;
$fixfields=array('id'=>'商品编号','name'=>'商品名称','stock0'=>'总库存','score'=>'积分','price3'=>'批发价','price4'=>'大客户价');
$customfields=array('addtime'=>'上架时间','updatetime'=>'更新时间','recommend'=>'推荐指数','onsale'=>'特价指数','weight'=>'商品重量','solded'=>'已售数量','barcode'=>'商品条码');
if(@$_SESSION['showcost']) $customfields['cost']='价格成本';

$customfield=@$_COOKIE['customfield'];
if($customfield){
  $matched=false;
  foreach($customfields as $key=>$value){
    if($customfield==$key){
      $matched=true;
      break;
    }
  }
  if(!$matched) goto label_defaut_custom;
}
else{
  label_defaut_custom:
  $customfield='addtime';
}

$sort_name=@$_COOKIE['sort_name'];	  
if($sort_name){
  $matched=false;
  foreach($fixfields as $key=>$value){
    if($sort_name==$key){
      $matched=true;
      break;
    }
  }
  if(!$matched){
    foreach($customfields as $key=>$value){
      if($sort_name==$key){
        $matched=true;
        break;
      }
    }
    if(!$matched) goto label_defaut_sort;
  }
}
else{
  label_defaut_sort:
  $sort_name='addtime';
}

$sort_order=@$_COOKIE['sort_order'];
if($sort_order!='asc' && $sort_order!='dec') $sort_order='desc';
$sql_sort_code='order by '.(($sort_name=='onsale')?'(onsale&0xf)':$sort_name).' '.$sort_order;

$cid=@$_GET['cid'];
if(is_numeric($cid) && $cid>0){
  $strCat = 'and cids like \'%,'.$cid.',%\' ';
}
else{
  $cid=0;
  $strCat = '';
}

$onshelf=@$_COOKIE['onshelf'];
switch($onshelf){
  case '1':$shelf_code='recommend>0';$customfields['addtime']='上架时间';break;
  case '2':$shelf_code='recommend=0';$customfields['addtime']='下架时间';;break;
  case '0':$shelf_code='recommend>=0';break;
  default:$onshelf='0';$shelf_code='recommend>=0';break;
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="checkproduct.js"></script>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/category.js" type="text/javascript"></SCRIPT>
<title>商品管理</title>
<style type="text/css">
TR.grayrow TD,TR.grayrow TD A{color:#BFBFBF;}
</style>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td height="20" align="right" background="images/topbg.gif" bgcolor="#F2F2F2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>商品列表</font></a></b></td>
        <td width="45%"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><select onchange="ChangeShelf(this.value)"><option value="0">所有商品</option><option value="1"<?php if($onshelf=='1')echo ' selected';?>>架上商品</option><option value="2"<?php if($onshelf=='2')echo ' selected';?>>架下商品</option></select><script language="javascript">CreateCategorySelection("brand",<?php echo $cid;?>,"--------商品分类过滤--------","self.location.href='?cid='+this.value;");</script></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <form method="post" action="?" style="margin:0px">
      <td height="100" align="center" bgcolor="#FFFFFF"><?php
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
                     $search_title='商品名称';
                     break;
       		case 'barcode':
       		     $sql='where barcode= \''.$keyvalue.'\'';
                     $search_title='商品条码';
                    break; 
       		case 'productid':
                     if(is_wholenumber($keyvalue) && $keyvalue>0){
       		       $sql='where id='.$keyvalue;
                       $keyvalue=GenProductCode($keyvalue); 
                     }
                     else{
       		       $sql='where id=0';
                     }
                     $search_title='商品编号';
                     break;
       		case 'supplier':
       		     $sql='where supplier= \''.$keyvalue.'\'';
                     $search_title='供 货 商';
                     break;

                default:   PageReturn('参数错误',0);	
             }

             echo '<b>根据<font color="#FF6600">'.$search_title.'</font>搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
             $sql.=' and '.$shelf_code;
         }
         else{
            $keyname='';
            $sql='where '.$shelf_code;
            if($strCat)$sql.=' '.$strCat;
         }
         while(1){
           $res=page_query('select id,name,price3,price4,cost,recommend,onsale,stock0,score,weight,solded,barcode,updatetime,addtime','from mg_product',@$sql,$sql_sort_code,15);
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
          <tr bgcolor="#f7f7f7" align="center" height="20">
            <td width="4%"  background="images/topbg.gif" height="25" ><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)" /><?php
            $td_sizes=array('8%','45%','7%','7%','7%','7%');
            $index=0; 
            foreach($fixfields as $fieldkey=>$fieldvalue){ 
              echo '</td><td width="'.$td_sizes[$index++].'" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort(\''.$fieldkey.'\')"><strong>'.$fieldvalue.'</strong>';
              if($sort_name==$fieldkey) echo '<img src="images/sort_'.$sort_order.'.gif">';
            }?>
            </td><td width="10%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="var target=(event.target||event.srcElement).tagName;if(target=='TD'||target=='IMG'){ProductResort('<?php echo $customfield;?>');}" nowrap><select style="font-weight:bold" onchange="ChangeCustomField(this.value);"><?php
               foreach($customfields as $key=>$value){
                  $selected=($key==$customfield)?' selected':'';
                  echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
               }?></select><?php if($sort_name==$customfield) echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="5%" background="images/topbg.gif"><strong>操作</strong></td>
          </tr><?php
        foreach($res as $row){
          $productid=$row['id'];?>
          <tr height="25"  align="center" bgcolor="#F7F7F7" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"<?php if($row['recommend']<=0)echo ' class="grayrow"';?>>
            <td><input name="selectid[]" type="checkbox" onclick="mChk(this)" value="<?php echo $row['id'];?>" /></td>
            <td><a href="mg_stocklog.php?id=<?php echo $productid;?>"><?php echo GenProductCode($productid);?></a></td>
            <td align="left"><a href="<?php echo GenProductLink($productid);?>" target="_blank"><?php echo $row['name'];?></a><?php
               $onsale=$row['onsale']&0xf;
               if($onsale>0) echo '<img src="images/onsale'.$onsale.'.gif" width=16 height=16 alt="特价指数为'.$onsale.'">';?>
            </td>
            <td style="cursor:pointer;text-decoration:underline" onclick="ChangeStock(<?php echo $productid;?>)"><?php echo $row['stock0'];?></td>
            <td style="cursor:pointer;text-decoration:underline"  onclick="ChangeScore(this,<?php echo $productid;?>)"><?php echo $row['score'];?></td>
            <td style="cursor:pointer;;text-decoration:underline" onclick="ChangePrice(this,<?php echo $productid;?>)"><?php echo FormatPrice($row['price3']);?></td>
            <td style="cursor:pointer;;text-decoration:underline"  onclick="ChangePrice(this,<?php echo $productid;?>)"><?php echo FormatPrice($row['price4']);?></td>
            <?php
               switch($customfield){
                 case 'updatetime':
                      echo '<td>'.date('Y-m-d',$row['updatetime']).'</td>';
                      break;
                 case 'addtime':
                      echo  '<td>'.(($row['recommend']>0 || $onshelf)?date('Y-m-d',$row['addtime']):'已下架').'</td>';
                      break;
                 case 'recommend':
                      if($row['recommend']>0) echo '<td style="cursor:pointer;text-decoration:underline;" onclick="ChangeRecommend(this,'.$productid.')">'.$row['recommend'].'</td>';
                      else echo '<td><font color="#BFBFBF">已下架</font></td>';
                      break;
                 case 'onsale':
                      echo '<td style="cursor:pointer;text-decoration:underline;" onclick="ChangeOnsale(this,'.$productid.')">'.$onsale.'</td>';
                      break; 
                 case 'cost':
                      echo  '<td style="cursor:pointer;text-decoration:underline" onclick="ChangePrice(this,'.$productid.')">'.FormatPrice($row['cost']).'</td>';
                      break;
                 case 'weight':
                      echo  '<td style="cursor:pointer;text-decoration:underline" onclick="ChangeWeight(this,'.$productid.')">'.$row['weight'].'</td>';
                      break; 
                 case 'barcode':
                      echo  '<td style="cursor:pointer;text-decoration:underline" onclick="ChangeBarcode(this,'.$productid.')">'.$row['barcode'].'</td>';
                      break;
                 default:
                      echo  '<td>'.$row[$customfield].'</td>';
                      break;

               }?>

            <td><a href="mg_editproduct.php?id=<?php echo $productid;?>" title="编辑"><img src="images/pic9.gif" width="18" height="15" align="absmiddle" border=0></a></td>
          </tr><?php
        }?>
          <tr bgcolor="#FFFFFF">
          <td colspan="9" width="100%">
             <table width="100%" height="35"  border=0>
             <tr><td width="10%" nowrap><input type="button" name="CartButton" value="放入购物车" onclick="AddToCart(this.form)">&nbsp;<input type="button" name="OrderButton" value="加入订单" onclick="AddToOrder(this.form)"></td>
                 <td align="center"><script language="javascript"><?php 
          $queryparam='kn='.$keyname.'&kv='.rawurlencode($keyvalue).'&cid='.$cid;
          echo "GeneratePageGuider(\"$queryparam\",$total_records,$page,$total_pages);";?>
     </script></td>
                  <td align="right" width="20%" nowrap><input type="button"  onclick="BatchOnsale(this.form,true);" value="设定特价" /> &nbsp;<input type="button"  onclick="BatchOnsale(this.form,false);" value="取消特价"/> &nbsp;<input type="button"  onclick="BatchWithdrawProduct(this.form);" value="商品下架"/> &nbsp;<input type="button" onclick="BatchForwardProduct(this.form)" value="商品上架"></td>
             </tr>
             </table>
            </td>
          </tr>
        </table>
     <?php
}?>
</td>
    </form>
  </tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td align="center"><form name="schform" method="get" style="margin:0px"><b>按</b><select name="kn">
	<OPTION VALUE="name">商品名称</OPTION>
	<OPTION VALUE="productid">商品编号</OPTION>
	<OPTION VALUE="barcode">商品条码</OPTION>
	<OPTION VALUE="supplier">供 货 商</OPTION>
    </select><input name="kv" type="text" style="color:#FF0000"> &nbsp; <input type="submit" value=" 搜 索 "></form>
   </td>
</tr>
</table>
<script language=javascript>
function ChangePrice(tdCell,productID){
  var OnChangeClose=function(ret){
    if(ret){
      var myCells=tdCell.parentNode.cells;
      myCells[5].innerHTML=ret[3];
      myCells[6].innerHTML=ret[4];
      <?php if($customfield=='cost') echo 'if(ret[0]!=null)myCells[7].innerHTML=ret[0];';?>
      return true;
    }
  };
  AsyncDialog("修改价格","changeprice.php?id="+productID+"&handle="+Math.random(),580,120,OnChangeClose)
} 

/*
function SwitchShelf(obj,onoff){
 var row=obj.parentNode.parentNode;
 var pname=GetInnerText(row.cells[2]);
 var operation=((onoff)?'上':'下')+'架产品';
 if(confirm('确定要'+operation+'：'+pname)){
   var pid=GetInnerText(row.cells[1]);
   var OnPostReturn=function(ret){
     if(ret && ret.indexOf('<OK>')>=0){
       if(onoff){
         row.className='';
         obj.title='下架';
         obj.src='images/shelfoff.png';
         obj.onclick=new Function("SwitchShelf(this,false);"); 
       }
       else{
         row.className='grayrow';
         obj.title='上架';
         obj.src='images/shelfon.png';
         obj.onclick=new Function("SwitchShelf(this,true);"); 
       }
       alert(operation+'成功！');
     }
     else if(ret)alert(ret);
   }
   AsyncPost('selectid='+pid,'?mode='+((onoff)?'forward':'withdraw'),OnPostReturn);
 }
}*/

<?php if($keyname) echo 'ProSearchAutoSelect("'.$keyname.'","'.$keyvalue.'");';?>
</script>
</body>
</html><?php db_close();?>
