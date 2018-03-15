<?php require('includes/dbconn.php');
/*
权限1. 订单删除、修改订单出/入库的库房、订单中商品编辑(添加/删除/复制/转移/修改数量、价格、积分)
要求：操作员是订单的下单用户或者客服，并且订单没有确认,(操作员不必具有库存管理权);

权限2. 订单状态修改/提交
要求：出/入库审核确认或取消确认时要求操作员是目标库房的客服，操作员要求具有库存管理权(own_popedomStock);
      最后一步的“订单完成”提交必须由具有popedomManage的管理员来完成。　
       
注：订单状态允许的前提下，具有popedomManage的管理员拥有所有权限。
*/
CheckLogin();
OpenDB();

$mode=@$_GET['mode'];
if($mode){
  switch($mode){
    case 'amount':ChangeAmount();break;
    case 'remark':ChangeRemark();break;
    case 'score': ChangeScore();break;
    case 'cost': ChangeCost();break;
    case 'price': ChangePrice();break;
    case 'remove':RemoveProduct();break;
    case 'delete':DeleteOrder();break;
    case 'copy':CopyProducts();break;
    case 'migrate':MigrateProducts();break;
    case 'userremark':ChangeUserRemark();break;
    case 'adminremark':ChangeAdminRemark();break; 
    case 'changeexporter':ChangePorter('exporter');break;
    case 'changeimporter':ChangePorter('importer');break;
    case 'orderstate':ChangeOrderState();break;
  }
  CloseDB();
  exit(0);
}

function UpdateOrderGoods($id,$field,$value){
  global $conn,$AdminUsername;
  $sql='update mg_ordergoods inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername set mg_ordergoods.'.$field.'='.$value.' where mg_ordergoods.id='.$id.' and mg_orders.state=-1';
  if(!CheckPopedom('MANAGE'))$sql.=" and (mg_orders.username='$AdminUsername' or mg_orders.operator='$AdminUsername')";
  return $GLOBALS['conn']->exec($sql);
}

function ChangeAmount(){
  $selectid=trim($_POST['selectid']);
  $amount=trim($_POST['newvalue']);
  if(is_numeric($selectid) && $selectid>0 && is_numeric($amount)){
    if(UpdateOrderGoods($selectid,'amount',$amount))echo '<OK>';
  }
}

function ChangeRemark(){
  $selectid=trim($_POST['selectid']);
  if(is_numeric($selectid)&& $selectid>0){
    $remark=FilterText(trim($_POST['newvalue']));
    if(strlen($remark)>255) $remark=substr($remark,0,250).'...';
    if(UpdateOrderGoods($selectid,'remark',"'$remark'"))echo '<OK>';
  }
}

function ChangeScore(){
  $selectid=trim($_POST['selectid']);
  $score=trim($_POST['newvalue']);
  if(is_numeric($selectid) && $selectid>0 && is_numeric($score)){
     $row=$GLOBALS['conn']->query('select mg_ordergoods.score as curscore,mg_product.score as maxscore from mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id where mg_ordergoods.id='.$selectid,PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($score!=$row['curscore']){
         if($score<=$row['maxscore']){
            if(UpdateOrderGoods($selectid,'score',$score))echo '<OK>';
         }
         else echo '该商品单件最大积分不能超过'.$row['maxscore'].'分！';
       }
     }
   }
}

function ChangePrice(){
  $OrderName=FilterText(trim($_POST['ordername']));
  $selectid=trim($_POST['selectid']);
  $newvalue=trim($_POST['newvalue']);
  if($OrderName && is_numeric($selectid) && $selectid>0 && is_numeric($newvalue) && $newvalue>=0){
    global $conn;
    $row=$conn->query('select mg_users.grade,mg_orders.state from mg_users inner join mg_orders on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$OrderName.'\' and state=-1',PDO::FETCH_ASSOC)->fetch();
    if($row){
      $PriceUser='price'.$row['grade'];
      $newvalue=round($newvalue,2);
      if($conn->exec('update (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) set mg_ordergoods.audit=(mg_product.'.$PriceUser.'='.$newvalue.'),mg_ordergoods.price='.$newvalue.' where mg_ordergoods.id='.$selectid))echo '<OK>';
    }
  }
}

function ChangeCost(){
  $OrderName=FilterText(trim($_POST['ordername']));
  $selectid=trim($_POST['selectid']);
  $newvalue=trim($_POST['newvalue']);
  if($OrderName && is_numeric($selectid) && $selectid>0 && is_numeric($newvalue) && $newvalue>=0){
    if(CheckPopedom('FINANCE')){
      global $conn;
      if($conn->exec('update ((mg_product inner join mg_ordergoods on mg_ordergoods.productid=mg_product.id) inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername) set mg_product.cost='.round($newvalue,2).' where mg_orders.state=-1 and mg_ordergoods.id='.$selectid))echo '<OK>';
    }else echo '权限错误！';
  }else echo '参数错误！';
}



function RemoveProduct(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $selectid=trim($_POST['selectid']);
  if($OrderName && $selectid){
    $row=$conn->query('select username,operator from mg_orders where ordername=\''.$OrderName.'\' and state=-1',PDO::FETCH_ASSOC)->fetch();
    if($row){
       if($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopodeom('MANAGE')){
         if($conn->exec('update mg_ordergoods set ordername=null where id in ('.$selectid.') and ordername=\''.$OrderName.'\'')){
           echo '选定的商品已经成功从该订单中删除！<OK>';
         }
       }
    }
  }
}

function ChangeUserRemark(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $remark=FilterText(trim($_POST['userremark']));
  if($OrderName && $remark){
    if(strlen($remark)>255) $remark=substr($remark,0,250).'...';
    $sql="update mg_orders set userremark='$remark' where ordername='$OrderName' and (state=-1 || state=-2)";
    if(!CheckPopedom('MANAGE'))$sql.=" and (username='$AdminUsername' or operator='$AdminUsername')"; 
    if($conn->exec($sql)) PageReturn('保存成功！');
    else PageReturn('没有改变！');
  }
}
   
function ChangeAdminRemark(){
  global $conn;
  $OrderName=FilterText(trim($_POST['ordername']));
  if($OrderName){
    if(CheckPopedom('MANAGE')){
      $remark=FilterText(trim($_POST['adminremark']));
      if(strlen($remark)>255) $remark=substr($remark,0,250).'...';
      if($conn->exec("update mg_orders set adminremark='$remark' where ordername='$OrderName' and state>-5 and state<0")) PageReturn("保存成功！");
      else PageReturn('没有改变！');
    }else PageReturn('没有权限！');
  }
}

function ChangePorter($porter){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $newvalue=$_POST['newvalue'];
  if($OrderName && is_wholenumber($newvalue) && $newvalue>=0){
     $sql="update mg_orders set $porter=$newvalue where ordername='$OrderName' and state=-1";
     if(!CheckPopedom('MANAGE'))$sql.=" and (username='$AdminUsername' or operator='$AdminUsername')"; 
     if($conn->exec($sql)) echo '操作成功！<OK>';
  }
  else echo '参数错误!！';
}

function DeleteOrder(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  if($OrderName){
     $row=$conn->query('select id,operator,state from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($row['state']==-1){
         if($row['operator']==$AdminUsername || CheckPopedom('FINANCE')){
           try{//事务管理
             $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
             $conn->beginTransaction();//事务开始
             if($conn->exec('update mg_orders set state=0 where id='.$row['id'].' and state=-1')){
               $conn->exec('update mg_ordergoods set ordername=null where ordername=\''.$OrderName.'\'');
               $conn->commit();//事务完成
               echo '订单删除成功！<OK>';
             }
             else  throw new PDOException('操作失败,数据库异常！');  
           }
           catch(PDOException $ex){ 
             $conn->rollBack();  //事务回滚 
             echo  $ex->getMessage();
           } 
        } else echo '无权限删除该订单！';
       }else echo '订单锁定状态，无法删除！'; 
     }else echo '订单不存在，可能已被删除！';
  }
}


function MigrateProducts(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $desorder=FilterText(trim($_POST['desorder']));
  $selectid=FilterText(trim($_POST['selectid']));
  if($OrderName && $desorder && $OrderName!=$desorder && $selectid){
    $row=$conn->query('select username,operator from mg_orders where ordername=\''.$OrderName.'\' and state=-1',PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE')){
	$row=$conn->query('select state,username,operator from mg_orders where ordername=\''.$desorder.'\'',PDO::FETCH_ASSOC)->fetch();
	if($row){
	  if($row['state']==-1 && ($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE')) ){
	    if($conn->exec("update mg_ordergoods set ordername='$desorder' where id in ($selectid) and ordername='$OrderName'")){
	      $row=$conn->query('select sum(amount*price),sum(amount*score) from mg_ordergoods where ordername=\''.$desorder.'\'',PDO::FETCH_NUM)->fetch();
	      if($row)$conn->exec("update mg_orders set totalprice={$row[0]},totalscore={$row[1]} where ordername='$desorder' and state=-1");
	      echo '选定的商品已经成功移动到订单'.$desorder.'！<OK>';
	    }
	  }
	  else echo '目标订单被拒绝添加！';
	}
	else echo '目标订单不存在！';
      }
    }
  }
}

function CopyProducts(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $desorder=FilterText(trim($_POST['desorder']));
  $selectid=FilterText(trim($_POST['selectid']));
  if($OrderName && $desorder && $OrderName!=$desorder && $selectid){
    $row=$conn->query('select state,username,operator from mg_orders where ordername=\''.$desorder.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row){
       if(($row['state']==-1||$row['state']==1) && ($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE'))){
         $res=$conn->query('select * from mg_ordergoods where id in ('.$selectid.') and ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC);
         try{//事务管理
            $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();//事务开始
            foreach($res as $row){
	      $sql="mg_ordergoods set productname='{$row['productname']}',price={$row['price']},score={$row['score']},amount={$row['amount']},remark='{$row['remark']}',audit={$row['audit']}";
	      if(!$conn->exec("update $sql where ordername='$desorder' and productid={$row['productid']}")){
		$sql.=",ordername='$desorder',productid={$row['productid']}";
		if(!$conn->exec("update $sql where ordername is null limit 1") && !$conn->exec("insert into $sql")) throw new PDOException('操作失败,数据库异常！');  
	      }
	    }
	    $row=$conn->query('select sum(amount*price),sum(amount*score) from mg_ordergoods where ordername=\''.$desorder.'\'',PDO::FETCH_NUM)->fetch();
	    if($row)$conn->exec("update mg_orders set totalprice={$row[0]},totalscore={$row[1]} where ordername='$desorder' and state=-1");
            $conn->commit();//事务完成
            echo '选定的商品已经成功复制到订单'.$desorder.'！';
         } 
	 catch(PDOException $ex){ 
	   $conn->rollBack();  //事务回滚 
	   echo  $ex->getMessage();
	 } 
       }
       else echo '目标订单被拒绝添加，请检查目标订单状态及管理权限！';
    }
    else echo '目标订单不存在！';
  }
}
 
function ChangeOrderState(){
  global $conn;
  $OrderName=FilterText(trim($_POST['ordername']));
  $newstate=trim($_POST['newstate']);
  if(empty($OrderName) || !is_wholenumber($newstate) || $newstate>-1 || $newstate<-4)PageReturn('参数错误！！'.$newstate);
  if(!CheckPopedom('STOCK'))PageReturn('用户权限错误！');
 
  $allowchange=false;
    
  $row=$conn->query('select id,state,exporter,importer,userremark,actiontime from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
  if(empty($row))PageReturn('没有订单!');
  $Order_Exporter=$row['exporter'];
  $Order_Importer=$row['importer'];
  $Order_State=$row['state'];

  if($Order_State==-1){
    if($newstate==-2) {
       if(!$row['userremark']) PageReturn('订单备注不能为空!');
       $RecordCount=$conn->query('select count(*) from mg_ordergoods where ordername=\''.$OrderName.'\' and amount>0')->fetchColumn(0);
       if(!$RecordCount) PageReturn('订单不能为空!');
       if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Exporter) $allowchange=true;
    }
  }
  else if($Order_State==-2){
    if($newstate==-1){
      if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Exporter) $allowchange=true;
    }
    else if($newstate==-3){
      if($Order_Exporter==$Order_Importer)PageReturn('出库与入库单位不能相同！');
      if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Importer) $allowchange=true;
    }
  }
  else if($Order_State==-3){
    if($newstate==-2 && (CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Importer)) $allowchange=true;
    else if($newstate==-4 && (CheckPopedom('MANAGE') && $Order_Importer!=$Order_Exporter)) $allowchange=true;
  }
  if(!$allowchange) PageReturn('权限错误！');
  $sql='update mg_orders set state='.$newstate;
  if($newstate==-4)$sql.=',actiontime=unix_timestamp()';
  $sql.=" where ordername='$OrderName' and state=$Order_State";

  if($newstate==-4){
    try{//事务管理
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();//事务开始
      if($conn->exec($sql)){
	  if($Order_Exporter==0 && $Order_Importer>0) $sql='mg_product.stock'.$Order_Importer.'=mg_product.stock'.$Order_Importer.'+mg_ordergoods.amount,mg_product.stock0=mg_product.stock0+mg_ordergoods.amount';
	  else if($Order_Exporter>0 && $Order_Importer==0) $sql='mg_product.stock'.$Order_Exporter.'=mg_product.stock'.$Order_Exporter.'-mg_ordergoods.amount,mg_product.stock0=mg_product.stock0-mg_ordergoods.amount';
	  else if($Order_Exporter>0 && $Order_Importer>0) $sql='mg_product.stock'.$Order_Exporter.'=mg_product.stock'.$Order_Exporter.'-mg_ordergoods.amount,mg_product.stock'.$Order_Importer.'=mg_product.stock'.$Order_Importer.'+mg_ordergoods.amount';	
	  else PageReturn('参数无效');
	  $sql='update ((mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid) inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername) set '.$sql.' where mg_orders.ordername=\''.$OrderName.'\' and mg_ordergoods.amount>0 and mg_orders.state='.$newstate; 
	  if($conn->exec($sql)){
	    $conn->exec('update mg_ordergoods set ordername=null where amount=0 and ordername=\''.$OrderName.'\'');
	    $conn->commit();//事务完成
            PageReturn('订单状态修改成功！');
	  }
	  else throw new PDOException('操作失败,数据库异常！');  
      } 
    }
    catch(PDOException $ex){ 
      $conn->rollBack();  //事务回滚 
      PageReturn($ex->getMessage());
    }
  }
  else{
     if($conn->exec($sql)) PageReturn('订单状态修改成功！');
  }
}
  
$OrderName=FilterText(trim(@$_GET['ordername']));
if(!$OrderName)PageReturn('参数无效!！',0);

$row=$conn->query('select * from mg_orders where ordername=\''.$OrderName.'\' and state<0',PDO::FETCH_ASSOC)->fetch();
if($row){
  $Order_Operator=$row['operator'];
  $Order_Username=$row['username'];
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $Order_State=$row['state'];
  $Order_Exporter=$row['exporter'];
  $Order_Importer=$row['importer'];
  $Order_ActionTime=$row['actiontime'];
  $Order_UserRemark=$row['userremark'];
  $Order_AdminRemark=$row['adminremark'];
}
else PageReturn('<br><br><p align=center>该订单不存在或已删除！<br><br><a href=\'mg_privateorders.php\'>[返回订单管理]</a></p>',0);

$DepotArray=array('其它单位');
$res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
foreach($res as $row)$DepotArray[$row[0]]=$row[1];

session_start();
$own_popedomFinance=CheckPopedom('FINANCE');
$own_popedomManage=CheckPopedom('MANAGE');

$CostOrScore=(@$_SESSION['showcost'] && $own_popedomFinance && $Order_State>-3)?'cost':'score';
$IsOrderManager=($Order_Username==$AdminUsername || $Order_Operator=$AdminUsername || $own_popedomManage); 
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="checkorder.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
.ProName{text-align:left;padding-left:5px;}
-->
</style>
</head>
<body topmargin="0" leftmargin="0" onload="UpdatePagePosition(1)" onunload="UpdatePagePosition(0)"> 
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td background="images/topbg.gif">
     <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr> 
    	<td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="mg_privateorders.php">内部订单管理</a> -> <font color=#FF0000>订单明细</font></b></td>
        <td align="right"><input type="button"<?php if(!$IsOrderManager || $Order_State!=-1) echo ' disabled';?> value="移除商品" onclick="RemoveProducts()">&nbsp;  <input type="button"<?php if(!$IsOrderManager || $Order_State!=-1) echo ' disabled';?> value="转移商品" onclick="MigrateProducts()">&nbsp; <input type="button" value="复制商品" onclick="CopyProducts()">&nbsp; <input type="button" value="删除订单"<?php if(!$IsOrderManager || $Order_State!=-1) echo ' disabled';?> onclick="DeleteMyOrder()">&nbsp; <input type="button" value="下载清单" onclick="window.open('mg_downorder.php?ordername=<?php echo $OrderName;?>&handle='+Math.random())"></td>
    </tr></table>
    </td>
  </tr>
  <tr> 
    <td height="200" valign="top" bgcolor="#FFFFFF">
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
          <td background="images/topbg.gif" nowrap>
          	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	      <tr>
    		      <td width="70%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" /><b>订单号</b>：<font color="#FF0000"><?php echo $OrderName;?></font> ，<img src="images/pic18.gif" width="17" height="15" align="absmiddle" /><b>下单用户</b>：<a href="javascript:CheckUserInfo()"><?php echo $Order_Username;?></a></td>
    		      <td width="30%" nowrap align="right"><span id="OrderStatePanel"></span></td>
    		    </tr>
    		    </table>
          </td>
        </tr>
      </table>
      
      <table id="MyTableID" width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong><input type="checkbox" onClick="Checkbox_SelectAll('selectid',this.checked)"><strong> 编号&nbsp; &nbsp;</strong></strong></td>
          <td WIDTH="60%" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="8%" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="8%" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="8%" align="center" background="images/topbg.gif"><strong>单件<?php echo ($CostOrScore=='cost')?'成本':'积分';?></strong></td>
          <td WIDTH="8%" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
      </tr><?php

$TotalProduct=0;   //商品总数
$TotalCost=0;
$TotalScore=0;
$TotalPrice=0;     //价格总计
$RecordCount=0;    //商品总项目 
$res=$conn->query('select mg_ordergoods.id,mg_ordergoods.productid,mg_ordergoods.price,mg_ordergoods.score,mg_ordergoods.amount,mg_ordergoods.remark,mg_ordergoods.productname,mg_product.stock'.GetAdminDepot().' as stock,mg_product.cost from (mg_ordergoods inner join mg_product on  mg_ordergoods.productid=mg_product.id) inner join mg_brand on mg_brand.id=mg_product.brand where mg_ordergoods.ordername=\''.$OrderName.'\' order by mg_brand.sortindex,mg_ordergoods.productname',PDO::FETCH_ASSOC);
foreach($res as $row){
  $RecordCount++; //商品总项目 
  $Amount=$row['amount'];
  if($Amount>0){
    $TotalProduct+=$Amount;	
    $TotalPrice+=$Amount*$row['price'];
    $TotalScore+=$Amount*$row['score'];
    $TotalCost+=$Amount*$row['cost'];
  }
  $Remark=$row['remark'];					            
  if($Remark)$Remark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" style="cursor:pointer" width=100% scrollAmount=2 scrollDelay=100>'.$Remark.'</MARQUEE>';
  else $Remarks='&nbsp;';
  echo '<tr align="center" stock="'.$row['stock'].'" goodsID="'.$row['id'].'"  bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
          <TD><input name="selectid" type="checkbox" value="'.$row['id'].'"><a href="mg_stocklog.php?id='.$row['productid'].'">'.$row['productid'].'</a></td>
          <TD class="ProName"><a href="'.WEB_ROOT.'products/'.$row['productid'].'.htm" target="_blank">'.$row['productname'].'</a></TD> 
          <TD>'.$Amount.'</td>
	  <TD>'.FormatPrice($row['price']).'</td>	
	  <TD>'.round($row[$CostOrScore],2).'</td>	
	  <TD>'.$Remark.'</td>
	  </TR>';
}
if($RecordCount==0) echo '<tr><td colspan=6 height=50 align=center bgcolor="#FFFFFF"><font color=#FF0000>此订单中还没有商品！</font></td></tr>';
if($TotalPrice!=$OriginOrderTotalPrice || $TotalScore!=$OriginOrderTotalScore){ 
  if($Order_State==-1 || $Order_State==-2)$conn->exec("update mg_orders set totalprice=$TotalPrice,totalscore=$TotalScore where ordername='$OrderName' and (state=-1 or state=-2)");
}?>
<tr height="25" align="center" id="OrderStatRow"> 
  <td  colspan="2" background="images/topbg.gif"><b>合计</b></td>
  <td background="images/topbg.gif"><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $RecordCount;?></td>
  <td background="images/topbg.gif"><font color="#FF0000"><?php echo FormatPrice($TotalPrice);?></font></td>
  <td background="images/topbg.gif"><font color="#FF0000"><?php echo ($CostOrScore=='cost')?$TotalCost:$TotalScore;?></font></td>
  <td background="images/topbg.gif">&nbsp;</td>
</tr>
</table>

    </td>
 
  </tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif">
    	<b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />订单信息</b>
    </td>
  </tr>  
  <tr>
     <td>
       
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="25" bgcolor="#F7F7F7">
          <td width=100 align="right" background="images/topbg.gif"><strong>订单备注：</strong></td>
          <td nowrap> &nbsp; <?php if($Order_State==-1 && $IsOrderManager) echo '<form style="margin:0px" method="post" action="?mode=userremark"><input type="hidden" name="ordername" value="'.$OrderName.'"><input type="text" name="userremark" maxlength=100 value="'.$Order_UserRemark.'" style="width:90%"><input type="submit" value="保存"></form>';
          else if($Order_UserRemark)echo $Order_UserRemark;
          else echo '无';?></td>
      </tr>

      <?php if($own_popedomManage){?>
      <tr height="25" bgcolor="#F7F7F7">  
          <td align="right" background="images/topbg.gif"><strong>附加说明：</strong></td>
          <td nowrap> &nbsp;<form style="margin:0px" method="post" action="?mode=adminremark"><input type="hidden" name="ordername" value="<?php echo $OrderName;?>"><textarea name="adminremark" rows="3" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:90%;font-size: 9pt; border: 1 solid #808080"><?php echo $Order_AdminRemark;?></textarea><input type="submit" value="保存"><br> &nbsp; <font color=#FF0000>250字以内，仅管理员可见.</font></form>	
          </td>
      </tr><?php
      }?>               
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong><?php echo ($Order_State>-3)?'下单日期':'入库日期';?>：</strong></td>
          <td> &nbsp; <?php echo date('Y-m-d H:i',$Order_ActionTime);?></td>
      </tr>     
      </table>
    </td>
  </tr>
</table>
 
<script>
function ChangeDepot(step){
  if(step>=CurOrderState) alert("订单已确认，无法修改！"); 
  else{
    var ExportOrImport=(step==-2);
    var OldDepot=ExportOrImport?<?php echo $Order_Exporter?>:<?php echo $Order_Importer;?>;
    var OnGetDepotSelection=function(NewDepot){
       if(NewDepot!=null && NewDepot!=OldDepot){
         var OnSaveDepot=function(ret){
           if(ret && ret.indexOf('<OK>')>=0){
             alert('操作成功！');
             self.location.reload();
           }
           else if(ret)alert(ret);
         };
         AsyncPost("ordername="+CurOrderName+"&newvalue="+NewDepot,(ExportOrImport)?"?mode=changeexporter":"?mode=changeimporter",OnSaveDepot);
       }
       return true;
    };
    var dlgHTML='<form name="selectdepot" style="margin:0px" onsubmit="closeDialog(this.depot.value);return false;"><TABLE width="100%" height="100%"  border="1"  align="center" bordercolor="#FF6600" bgcolor="#FF6600" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"> <TR><TD width="100%"><font color="#FFFFFF"><strong>目标地址选择：</strong></font></TD></TR><TR bgcolor="#f7f7f7"><TD width="100%" align="center"><select name="depot"><?php foreach($DepotArray as $depotIndex=>$depotName)echo '<option value="'.$depotIndex.'">'.$depotName.'</option>';?></select></TD></TR><TR bgcolor="#f7f7f7"><TD align="right" bgcolor="#FFCC00"><input type="submit" value=" 确定 ">&nbsp;</TD></TR></TABLE></form>';
    AsyncDialog('选择仓库',dlgHTML,200,120,OnGetDepotSelection);   
    FormSetSelect("selectdepot","depot",OldDepot);
  }
}

<?php
echo 'InitMyOrder("'.$OrderName.'","'.$Order_Username.'",'.$Order_State.','.($IsOrderManager?'true':'false').','.($own_popedomFinance?'true':'false').','.(CheckPopedom('STOCK')?'true':'false').','.(($CostOrScore=='cost')?'true':'false').');'.chr(13);
$DepotName=@$DepotArray[$Order_Exporter];
if(empty($DepotName)){
  $Order_Exporter=0;
  $DepotName=$DepotArray[0];
}
echo 'document.forms["stateform"].exporter.value="'.$Order_Exporter.'";';
echo 'document.getElementById("dsp_exporter").innerHTML="'.$DepotName.'";'; 
$DepotName=@$DepotArray[$Order_Importer];
if(empty($DepotName)){
  $Order_Importer=0;
  $DepotName=$DepotArray[0];
}
echo 'document.forms["stateform"].importer.value="'.$Order_Importer.'";';
echo 'document.getElementById("dsp_importer").innerHTML="'.$DepotName.'";'; ?>
</script>
</body>
</html><?php CloseDB();?>
