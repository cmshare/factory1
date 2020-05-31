<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();

$mode=@$_GET['mode'];
if($mode){
  if($mode=='rndextend'){
    $selectid=$_POST['selectid'];
    if(is_array($selectid)){
      $idlist=implode(',',$selectid);
      $timeadd=rand(7,15)*24*60*60;
      $conn->exec('update mg_product set onsale=(onsale+'.$timeadd.')&~0xf|(onsale&0xf) where (onsale&0xf)>0 and id in('.$idlist.') and onsale>=unix_timestamp()');
      $conn->exec('update mg_product set onsale=(unix_timestamp()+'.$timeadd.')&~0xf|(onsale&0xf) where (onsale&0xf)>0 and id in('.$idlist.') and onsale<unix_timestamp()');
      PageReturn('特价商品活动期延长成功！');
    }
  }
  else if($mode=='batchdeadline'){
    $newvalue=$_GET['newvalue'];
    $selectid=$_POST['selectid'];
    if(is_array($selectid) && is_numeric($newvalue)){
      $idlist=implode(',',$selectid);
      $newvalue=(time()+$newvalue*24*60*60)&~0xf;
      if($conn->exec('update mg_product set onsale='.$newvalue.'|(onsale&0xf) where (onsale&0xf)>0 and id in ('.$idlist.')')) PageReturn('批量修改特价商品剩余天数成功！');
    }
    else PageReturn('参数错误！');
  }
  else if($mode=='changetejia'){
    $newvalue=$_POST['newvalue'];
    $selectid=$_POST['selectid'];
    if(is_numeric($newvalue) && is_numeric($selectid) && $selectid>0){
     if($conn->exec('update mg_product set price0='.$newvalue.' where (onsale&0xf)>0 and id='.$selectid))echo '<OK>';
    }
  }
  else if($mode=='changedeadline'){
    $newvalue=$_POST['newvalue'];
    $selectid=$_POST['selectid'];
    if(is_numeric($newvalue) && is_numeric($selectid)&& $selectid>0){ 
      $newvalue=(time()+$newvalue*24*60*60)&~0xf;
      if($conn->exec('update mg_product set onsale='.$newvalue.'|(onsale&0xf) where (onsale&0xf)>0 and id='.$selectid))echo '<OK>';
    }
  }
  db_close();
  exit(0);
}?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="checkproduct.js"></script>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/category.js" type="text/javascript"></SCRIPT><?php
$sort_name=@$_COOKIE['sort_name'];	  
if($sort_name){
  if($sort_name!='deadline' && $sort_name!='price0')
    if($sort_name!='stock0' && $sort_name!='onsale' && $sort_name!='id')
     if($sort_name!='name' && $sort_name!='price3' && $sort_name!='price4')$sort_name='deadline';
}
else $sort_name='deadline';

$sort_order=@$_COOKIE['sort_order'];
if($sort_name=='deadline')$sql_sort_code='(onsale&~0xf)';
else if($sort_name=='onsale')$sql_sort_code='(onsale & 0xf)';
else $sql_sort_code=$sort_name;
$sql_sort_code='order by '.$sql_sort_code.' '.(($sort_order=='asc')?'asc':'desc');


$cid=@$_GET['cid'];
if(is_numeric($cid) && $cid>0){
  $strCat = 'and cids like \'%,'.$cid.',%\' ';
}
else{
  $cid=0;
  $strCat = '';
}
?>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
 <tr>
    <td height="20" align="right" background="images/topbg.gif" bgcolor="#F2F2F2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>特价商品管理</font></a></b></td>
        <td width="45%"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><script language="javascript">CreateCategorySelection("brand",<?php echo $cid;?>,"--------商品分类过滤--------","self.location.href='?cid='+this.value;"); </script> </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr> 
  <tr> 
    <form  method="post" action="?">
      <td bgcolor="#FFFFFF" align="center" valign="top"><?php
$res=page_query('select id,name,recommend,onsale,stock0,price0,price3,price4','from mg_product','where (onsale&0xf)>0 and recommend>0 '.$strCat,$sql_sort_code,15);
if(!$res) echo '<p align="center" class="contents"> 数据库中暂时无数据！</p>';
else{?>
   <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
   <tr align="center" bgcolor="#F7F7F7" height="20"> 
     <td width="4%"  background="images/topbg.gif" height="25"><input type="checkbox" onClick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
     <td width="8%"  background="images/topbg.gif" title="点击排序" onclick="ProductResort('id')" style='cursor:pointer'><strong>编号</strong><?php if($sort_name=='id')echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="45%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('name')" style='cursor:pointer'><strong>名称</strong><?php if($sort_name=='name') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="6%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('stock0')" style='cursor:pointer'><strong>库存</strong><?php if($sort_name=='stock0')echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="7%"  background="images/topbg.gif" title="点击排序" onclick="ProductResort('onsale')" style='cursor:pointer'><strong>特价指数</strong><?php if($sort_name=='onsale')echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>            
     <td width="7%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('price3')" style='cursor:pointer'><strong>批发价</strong><?php if($sort_name=='price3') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="7%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('price4')" style='cursor:pointer'><strong>大客户价</strong><?php if($sort_name=='price4')echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="8%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('price0')" style='cursor:pointer'><strong>限时特价</strong><?php if($sort_name=='price0')echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td width="8%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('deadline')" style="cursor:pointer"><strong>剩余天数</strong><?php if($sort_name=='deadline') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
    </tr><?php
    
foreach($res as $row){
  $productid=$row['id'];
  echo '<tr bgcolor="#FFFFFF" align="center" height="25" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">';
  echo '<td><input name="selectid[]" type="checkbox" value="'.$productid.'" onclick="mChk(this)"></td>';
  echo '<td><a href="mg_stocklog.php?id='.$productid.'">'.GenProductCode($productid).'</a></td>';
  echo '<td align="left"><a href="'.GenProductLink($productid).'" target="_blank">'.$row['name'].'</a></td>';
  echo '<td>'.$row['stock0'].'</td>';
  echo '<td style="color:#0000EE;cursor:pointer;TEXT-DECORATION:underline;" onclick="ChangeOnsale(this,'.$productid.',1)">'.($row['onsale']&0xf).'</td>';
  echo '<td>'.FormatPrice($row['price3']).'</td>';
  echo '<td>'.FormatPrice($row['price4']).'</td>';
  echo '<td style="color:#0000EE;cursor:pointer;TEXT-DECORATION:underline;" onclick="ChangeTejia('.$row['id'].',this)">'.FormatPrice($row['price0']).'</td>';
  $lifetime=($row['onsale']-time())/86400;
  if($lifetime>1)$lifetime=round($lifetime);
  else if($lifetime>0)$lifetime=round($lifetime,1);
  else $lifetime=0; 
  $timecolor=($lifetime>0)?'#00AA00':'#EE0000';
  echo '<td style="color:'.$timecolor.';cursor:pointer;TEXT-DECORATION: underline;" onclick="ChangeDeadline('.$productid.',this)">'.$lifetime.'</td></tr>';
}?>
  <tr bgcolor="#FFFFFF"> 
    <td height="30" colspan="10" align="right">
      <input type="button"  onclick="BatchOnsale(this.form,true);" value="设置特价指数" title="批量修改商品特价指数"/>
              &nbsp;
      <input type="button"  onclick="BatchOnsale(this.form,false);" value="取消特价商品" title="批量设置为非特价商品"/>
              &nbsp;
      <input type="button"  onclick="BatchSetDeadline(this.form);" value="设置剩余天数" title="批量修改特价商品活动结束剩余时间"/>
              &nbsp;
      <input type="button"  onclick="DeadlineExtend(this.form);" value="随机延长7~15天" title="批量修改特价商品活动结束剩余时间">
              &nbsp;&nbsp; 
      </td>
          </tr>
        </table>
        <script language="javascript">  
            <?php echo "GeneratePageGuider('',$total_records,$page,$total_pages);";?>
        </script><?php
}?>  
   </td>
    </form>
  </tr>
</table>
<script>
function DeadlineExtend(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else if(confirm("确定要将所选的"+selcount+"件特价商品随机延长7~15天？")){
    myform.action = "?mode=rndextend";
    myform.submit();
  }
}

function BatchSetDeadline(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else{
    var newvalue=window.prompt("批量设置限时特价商品的剩余时间（单位：天）:\n\n", "");
    if(!isNaN(newvalue) && confirm("确定要将所选的"+selcount+"件特价商品剩余天数批量设置为"+newvalue+"天？")){
      myform.action = "?mode=batchdeadline&newvalue="+newvalue;
      myform.submit();
    }
  }  
}

function ChangeTejia(productID,tdCell){
  var defValue=tdCell.innerHTML.trim();
  var newvalue=window.prompt("请重新设该商品特价（单位：元）:\n\n", defValue);
  if(newvalue && newvalue!=defValue){
    if(isNaN(newvalue) || newvalue<=0) alert("价格无效！");
    else{
      var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"?mode=changetejia");
      if(ret && ret.indexOf("<OK>")>=0){
        tdCell.innerText=newvalue;
       }
     }
  }  
}

function ChangeDeadline(productID,tdCell){
   var defValue=tdCell.innerHTML.trim();
   var newvalue=window.prompt("请重新设活动剩余时间（单位：天）:\n\n", defValue);
   if(newvalue!=null && newvalue!=defValue && !isNaN(newvalue)){
     var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"?mode=changedeadline");
     if(ret && ret.indexOf("<OK>")>=0){
        tdCell.innerText=newvalue;
     }
   }  
}

</script>

</body>
</html><?php db_close();?>
