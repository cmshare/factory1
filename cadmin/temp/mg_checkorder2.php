<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
/*
-8:已存档的内部订单
-4:已完成的内部订单
-3: 完成入库审核
-2: 完成出库审核       
-1: 新建内部订单（未锁定）
0:已删除订单
1:新建订单（未锁定）
2:正在处理的订单（客服确认锁定）
3:已配货待发货
4:已发货待收款结算
5:已发货待客户确认收货
6:已完成订单（客户确认收货）
7:Reserved
8:存档订单
*/

$mode=@$_GET['mode'];
if($mode){
  switch($mode){
    case 'amount':ChangeAmount();break;
    case 'remark':ChangeRemark();break;
    case 'score' :ChangeScore();break;
    case 'audit' :ConfirmAudit();break;
    case 'price' :ChangePrice();break;
    case 'delete':DeleteOrder();break;
    case 'adjust':ChangeAdjust();break;
    case 'deliveryfee':ChangerDdeliveryfee();break;
    case 'orderinfo':UpdateOrderinfo();break;
    case 'orderstate':ChangeOrderState(false);break;
    case 'batch_settlement':ChangeOrderState(true);break;
  }
  CloseDB();
  exit(0);
}

function OwnPopedomFinance(){
  global $OwnFinance;
  if(!isset($OwnFinance)){
    $OwnFinance=CheckPopedom('FINANCE');
  } 
  return $OwnFinance;
}

function UpdateOrderGoods($id,$field,$value){
  return $GLOBALS['conn']->exec('update mg_ordergoods inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername set mg_ordergoods.'.$field.'='.$value.' where mg_ordergoods.id='.$id.' and mg_orders.state<3 && mg_orders.state>0');
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

function ConfirmAudit(){
  $selectid=trim($_POST['selectid']);
  if(is_numeric($selectid) && $selectid>0){
    if(OwnPopedomFinance()){ 
       if(UpdateOrderGoods($selectid,'audit','1'))echo '<OK>';
    }
  }
}

function ChangePrice(){
  $OrderName=FilterText(trim($_POST['ordername']));
  $selectid=trim($_POST['selectid']);
  $newvalue=trim($_POST['newvalue']);
  if($OrderName && is_numeric($selectid) && $selectid>0 && is_numeric($newvalue) && $newvalue>=0){
    global $conn;
    $row=$conn->query('select mg_users.grade,mg_orders.state from mg_users inner join mg_orders on mg_orders.username=mg_users.username where mg_orders.ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row){
      $OrderState=$row['state'];
      if($OrderState>0 && ($OrderState<3 ||($OrderState<5 && CheckPopdom('FINANCE')))){
        $PriceUser='price'.$row['grade'];
        $newvalue=round($newvalue,2);
  	if($conn->exec('update (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) set mg_ordergoods.audit=(mg_product.'.$PriceUser.'='.$newvalue.'),mg_ordergoods.price='.$newvalue.' where mg_ordergoods.id='.$selectid))echo '<OK>';
      }
    }
  }
}
function DeleteOrder(){
  $OrderName=FilterText(trim($_POST['ordername']));
  if($OrderName){
     global $conn,$AdminUsername;
     $row=$conn->query('select id,operator,state from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($row['state']==1){
         if($row['operator']==$AdminUsername || OwnPopedomFinance()){
           try{//事务管理
             $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
             $conn->beginTransaction();//事务开始
             if($conn->exec('update mg_orders set state=0 where id='.$row['id'].' and state=1')){
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

function ChangerDdeliveryfee(){
  $OrderName=FilterText(trim(@$_POST['ordername']));
  $newvalue=trim($_POST['newvalue']);
  if($OrderName && is_numeric($newvalue) && $newvalue>=0){
    //修改deliverfee后要置totalprice为null（mark dirty to trigger update)
    if($GLOBALS['conn']->exec('update mg_orders set deliveryfee='.round($newvalue,2).',totalprice=null where ordername=\''.$OrderName.'\' and state<5'))echo '<OK>';
  }
}
  
function ChangeAdjust(){
  $OrderName=FilterText(trim(@$_POST['ordername']));
  $newvalue=trim($_POST['newvalue']);
  if($OrderName && is_numeric($newvalue)){
    //修改deliverfee后要置totalprice为null（mark dirty to trigger update)
    if($GLOBALS['conn']->exec('update mg_orders set adjust='.round($newvalue,2).',totalprice=null where ordername=\''.$OrderName.'\' and state<5'))echo '<OK>';
  }
}

function UpdateOrderinfo(){
  global $conn;
  $OrderName=FilterText(trim($_POST['ordername']));
  if($OrderName){
    $row=$conn->query('select id,state from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row)$OrderState=$row['state'];
    else $OrderState=0;
  }else $OrderState=0;
  if($OrderState>=1 && $OrderState<=6){
    $OrderID=$row['id'];
    $AdminRemark=FilterText(trim($_POST['adminremark']));
    if(strlen($AdminRemark)>255) $AdminRemark=substr($AdminRemark,0,250).'...';
    $sql="update mg_orders set adminremark='$AdminRemark'";      

    if($OrderState<5){
      $DeliveryMethod=FilterText(trim($_POST['deliverymethod']));
      $DeliveryCode=FilterText(trim($_POST['deliverycode']));
      $OrderWeight=FilterText(trim($_POST['orderweight']));
      $sql.=",deliverymethod='$DeliveryMethod',deliverycode='$DeliveryCode'";      
      if(is_numeric($OrderWeight)) $sql.=',weight='.round($OrderWeight);
      if($DeliveryCode){
	$matchrule=$conn->query('select matchrule from mg_delivery where subject=\''.$DeliveryMethod.'\'')->fetchColumn(0);
	if($matchrule){
	  if(!preg_match('/'.$matchrule.'/',$DeliveryCode))$WarningMsg='\r\n运单号码格式不正确！';
	}
      }
      if($OrderState<3){
	  $sql.=',receipt=\''.FilterText(trim($_POST['receipt'])).'\'';
	  $sql.=',address=\''.FilterText(trim($_POST['address'])).'\'';
	  $sql.=',usertel=\''.FilterText(trim($_POST['usertel'])).'\'';
	  $sql.=',paymethod=\''.FilterText(trim($_POST['paymethod'])).'\'';
      }
    }
    $conn->exec($sql.' where id='.$OrderID);
    PageReturn('订单配送信息修改成功！'.@$WarningMsg);
  }
}

function ChangeOrderState($batch_settlement){
  global $conn;
  $OrderName=FilterText(trim($_POST['ordername']));
  $UserName=FilterText(trim($_POST['username']));
  $newstate=trim($_POST['newstate']);
  if(empty($OrderName) || empty($UserName) || !is_wholenumber($newstate) || $newstate<0 || $newstate>5)PageReturn('参数错误！！'.$newstate);
    	
  $row=$conn->query('select totalprice,totalscore,state,support,deliverycode,exporter from mg_orders where ordername=\''.$OrderName.'\' and username=\''.$UserName.'\' and state>0 and exporter>0 and importer<=0',PDO::FETCH_ASSOC)->fetch();
  if(empty($row))PageReturn('没有订单!');
  else $OrderState=$row['state'];
  if($OrderState==2) $allowchange=($newstate==1 || $newstate==3);  
  else if($OrderState==3) $allowchange=($newstate==2 || $newstate==4);
  else $allowchange=($newstate==$OrderState+1);
	  
  if($allowchange && $newstate>4 && !OwnPopedomFinance())$allowchange=false;
	  	
  if(!$allowchange) PageReturn('您没有权限执行该操作!',-1);
  else if($newstate==4){
    $OrderStockName='stock'.$row['exporter'];
    $DeliveryCode=$row['deliverycode'];
    if(empty($DeliveryCode))PageReturn('请先提交货单号！',-1);

    try {//事务管理
      //设置使用抛出异常错误模式，默认为silent 则只是记录errorCode，发生错误时仍然继续往下执行。
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();//事务开始
      $sql_1='update ((mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid) inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername) set mg_product.stock0=mg_product.stock0-mg_ordergoods.amount,mg_product.'.$OrderStockName.'=mg_product.'.$OrderStockName.'-mg_ordergoods.amount,mg_product.solded=mg_product.solded+mg_ordergoods.amount where mg_orders.ordername=\''.$OrderName.'\''; 
      $sql_2='update mg_orders set state='.$newstate.',actiontime=unix_timestamp() where ordername=\''.$OrderName.'\' and state='.$OrderState;
      if(!$conn->exec($sql_1) || !$conn->exec($sql_2)) throw new PDOException('操作失败,数据库异常！');  
      $conn->commit();//事务完成
    }
    catch(PDOException $ex){ 
      $conn->rollBack();  //事务回滚 
      PageReturn($ex->getMessage(),-1);
    } 
  }
  else if($newstate==5){
    if(!$batch_settlement){
      $audit_supend=$conn->query('select count(*) from mg_ordergoods where ordername=\''.$OrderName.'\' and amount>0 and audit=0')->fetchColumn(0);
      if($audit_supend)PageReturn('该订单中还有商品未审核！',-1);
    }
    $TotalPrice=$row['totalprice'];
    $TotalScore=$row['totalscore']; 
    //订单总价不能为负，但是总积分可以为负（总积分为负表示不是增加积分而是消耗积分)
    if(is_null($TotalPrice)||is_null($TotalScore))PageReturn('请先刷新订单!'); 
   
    $row=$conn->query('select deposit,score from mg_users where username=\''.$UserName.'\'',PDO::FETCH_NUM)->fetch();
    if($row){
      $UserDeposit=$row[0];
      $UserScore=$row[1];
    }
    else PageReturn('参数错误，用户名不存在！',-1);	

    if($TotalPrice-$UserDeposit>100 && !$_SESSION['arrearage'] && !$batch_settlement) PageReturn('该用户的余存款余额不足，货款无法缴扣！',-1);
    $AdminIDNumber=GetAdminIDNumber();
 
    try{//事务管理
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();//事务开始
      if($TotalPrice || $TotalScore){
        if($conn->exec('update mg_users set deposit=deposit-'.$TotalPrice.',score=score+'.$TotalScore.' where username=\''.$UserName.'\'')) echo '<script>alert("已自动从用户预存款帐户中缴扣货款'.$TotalPrice.'元！获得积分'.$TotalScore.'分！");</script>';
        else throw new PDOException('操作失败,数据库异常！');  

        if($TotalPrice>0){
      	  $UserDeposit-=$TotalPrice;
          if(!$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remarks,actiontime) values(\''.$UserName.'\',\''.$AdminIDNumber.'\',2,'.(-$TotalPrice).','.$UserDeposit.',\'订单（'.$OrderName.'）付款\',unix_timestamp())')) throw new PDOException('财务日志异常！');  
        }
        if($TotalScore!=0){
          $UserScore+=$TotalScore;
	  if(!$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remarks,actiontime) values(\''.$UserName.'\',\''.$AdminIDNumber.'\',1,'.$TotalScore.','.$UserScore.',\'订单（'.$OrderName.'）积分\',unix_timestamp())'))throw new PDOException('积分日志异常！');
        } 
      }
      $conn->exec('update mg_ordergoods set ordername=null where amount=0 and ordername=\''.$OrderName.'\'');          
      if(!$conn->exec('update mg_orders set state='.$newstate.' where ordername=\''.$OrderName.'\' and state='.$OrderState)) throw new PDOException('订单状态修改时发生异常！'); 
      else $conn->commit();//事务完成

    }
    catch(PDOException $ex){ 
      $conn->rollBack();  //事务回滚 
      PageReturn($ex->getMessage(),-1);
    } 
  }
  else if($newstate==2 && $row['support']==0) $conn->exec('update mg_orders set support='.GetAdminIDNumber().',operator=\''.$AdminUsername.'\',state='.$newstate.' where ordername=\''.$OrderName.'\' and state='.$OrderState);
  else $conn->exec('update mg_orders set state='.$newstate.' where ordername=\''.$OrderName.'\' and state='.$OrderState);

  PageReturn('订单状态修改成功！');
}  

$OrderName=FilterText(@$_GET['ordername']);
if(empty($OrderName))PageReturn('参数无效！',-1); 
	 
$row=$conn->query('select mg_orders.*,mg_users.grade,mg_users.deposit,mg_users.score,mg_usrgrade.title,mg_depot.depotname from (((mg_orders inner join mg_users on mg_orders.username=mg_users.username) inner join mg_depot on mg_orders.exporter=mg_depot.id) inner join mg_usrgrade on mg_usrgrade.id=mg_users.grade) where mg_orders.ordername=\''.$OrderName.'\' and mg_orders.state>0 and mg_orders.exporter>0 and mg_orders.importer<=0',PDO::FETCH_ASSOC)->fetch();
if($row){
  $UserName=$row['username'];
  $UserGrade=$row['grade'];
  $PriceUser='price'.$UserGrade;
  $UserGradeTitle=$row['title'];
  $UserDeposit=$row['deposit'];
  $UserScore=$row['score'];
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $DeliveryFee=$row['deliveryfee'];
  $OrderWeight=$row['weight'];
  $Order_State=$row['state'];
  $Order_Exporter=$row['exporter'];
  $Order_DepotName=$row['depotname'];
  $Order_UserTel=$row['usertel'];
  $Order_DeliveryMethod=$row['deliverymethod'];
  $Order_PayMethod=$row['paymethod'];
  $Order_Adjust=$row['adjust'];
  $Order_ActionTime=$row['actiontime'];
  $Order_DeliveryCode=$row['deliverycode'];
  $Order_Receipt=$row['receipt'];
  $Order_Address=$row['address'];
  $Order_UserRemark=$row['userremark'];
  $Order_AdminRemark=$row['adminremark'];
  $Order_IDNumber=$row['support'];
  $Order_Operator=$row['operator'];

  $UserFund=$conn->query('select sum(amount) from mg_accountlog where username=\''.$UserName.'\' and (operation=5 or operation=6)')->fetchColumn(0);
  if(!is_numeric($UserFund))$UserFund=0;   
}
else{
 PageReturn('<br><br><p align=center>该订单不存在或无效！<br><br><a href="mg_orders.php">[返回订单管理]</a></p>',0);
}

$IsOrderManager=($Order_Operator==$AdminUsername || OwnPopedomFinance() || $Order_Exporter==GetAdminDepot());//临时授权public处理所有订单

if($Order_State<3 and $IsOrderManager) $BaseInputStyle='style="width:95%"';	
else $BaseInputStyle='style="width:95%;border:0px;background-color:transparent" readOnly';	

$res=$conn->query('select mg_ordergoods.id,mg_ordergoods.productid,mg_ordergoods.price,mg_ordergoods.amount,mg_ordergoods.remark,mg_ordergoods.audit,mg_ordergoods.productname,mg_ordergoods.score,mg_product.stock'.$Order_Exporter.' as stock,mg_product.'.$PriceUser.' from (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) inner join mg_brand on mg_brand.id=mg_product.brand where mg_ordergoods.ordername=\''.$OrderName.'\' order by mg_brand.sortindex,mg_ordergoods.productname',PDO::FETCH_ASSOC);
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
    	<td nowrap>
    	 <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="mg_orders.php">客户订单管理</a> -> <font color=#FF0000>订单明细</font></b>
      </td>
      <td align="right"><a href="mg_downorder.php?ordername=<?php echo $OrderName;?>&handle=<?php echo time();?>"><img src="images/save.gif" border="0" width="15" height="16" align=absMiddle> 生成Excel表格...</a>
      </td>
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
    		      <td width="70%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" />订单号：<b><font color="#FF0000"><?php echo $OrderName;?></font></b> ，<img src="images/pic18.gif" width="17" height="15" align="absmiddle" />客户：<a href="javascript:CheckUserInfo()"><b><?php echo $UserName;?></b></a>，<?php echo $UserGradeTitle;?>，<a href="b2b_jfrz.asp?username=<?php echo $UserName;?>&mode=6">预存款<font color="#FF0000"><?php echo FormatPrice($UserDeposit);?></font>元<?php if($UserFund) echo '，<b>待审款<font color=#00AA00>'.FormatPrice($UserFund).'</font>元</b>';?></a>，积分<font color="#FF6600"><?php echo $UserScore;?></font>分</td>
    		      <td width="30%" nowrap align="right"> <img src="images/pic19.gif" width="18" height="15" align="absmiddle" />客服:<font color="#FF6600"><?php echo $Order_Operator.'#'.$Order_IDNumber;?></font> </td>
    		    </tr>
    		    </table>
          </td>
        </tr>
      </table>
      
      <div id="OrderStatePanel"></div>
      <table id="MyTableID" width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="60%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单件积分</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
      </tr><?php

$TotalPrice=0;  //价格总计
$TotalScore=0;
$TotalRecord=0;//rs.recordcount  '商品总项目
$TotalProduct=0;   //商品总件数

foreach($res as $row){
  $TotalRecord++;
  $Amount=$row['amount'];
  $Price=$row['price'];
  $Score=$row['score'];
  if($Amount>0){
    $TotalProduct+=$Amount;
    $TotalScore+=$Amount*$Score;
    $TotalPrice+=$Amount*$Price;
  }
  $Remark=$row['remark'];					            
  if($Remark) $Remark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" style="cursor:pointer" width=100% scrollAmount=2 scrollDelay=100>'.$Remark.'</MARQUEE>';
  else $Remark='&nbsp;';
  $audit_attr=($row['audit']==0)?('audit="'.$row[$PriceUser].'"'):'';
  echo '<TR align="center" '.$audit_attr.' stock="'.$row['stock'].'" goodsID="'.$row['id'].'" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
  <TD><a href="mg_stocklog.php?id='.$row['productid'].'">'.$row['productid'].'</a></td>
  <TD class="ProName"><a href="'.WEB_ROOT.'products/'.$row['productid'].'.htm" target="_blank">'.$row['productname'].'</a></TD> 
  <TD>'.$Amount.'</td>
  <TD>'.FormatPrice($Price).'</td>		
  <TD>'.$Score.'</td>
  <TD>'.$Remark.'</td>
  </TR>';
}?> 
  <tr height="25" align="center" id="OrderStatRow"> 
    <td colspan="2" background="images/topbg.gif"><b>合计</b></td>
    <td background="images/topbg.gif"><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $TotalRecord;?></td>
    <td background="images/topbg.gif"><b><?php echo FormatPrice($TotalPrice);?></b></td>
    <td background="images/topbg.gif"><?php echo $TotalScore;?></td>
    <td background="images/topbg.gif">&nbsp;</td>
  </tr>
  </table>
     
  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr><td align="center" background="images/topbg.gif"> 从<font color="#FF0000"><?php echo $Order_DepotName;?></font>出货&nbsp; &nbsp;<input id="checkweightbtn" type="button" onclick="window.open('mg_orderweight.php?ordername=<?php echo $OrderName;?>')" style="border:0px;cursor:pointer;TEXT-DECORATION: underline;BACKGROUND-COLOR:transparent;color:#0000FF;" value="...订单重量...">&nbsp; &nbsp;<?php 
  $Order_Adjust_signed=FormatPrice($Order_Adjust);
  if($Order_Adjust>0) $Order_Adjust_signed='+'.$Order_Adjust_signed;
  if(($IsOrderManager && $Order_State<3) || ($Order_State<5 && OwnPopedomFinance())) echo '配送费<span style="cursor:pointer;color:#FF0000" title="点击修改" onclick="ChangeDeliveryFee(this)"><u>+'.FormatPrice($DeliveryFee).'</u></span>元&nbsp;&nbsp; 折扣调整<span style="cursor:pointer;color:#FF0000" title="点击修改" onclick="ChangeAdjustFee(this)"><u>'.$Order_Adjust_signed.'</u></span>元&nbsp;&nbsp;';
  else echo '配送费<font color="#FF0000">'.FormatPrice($DeliveryFee).'</font>元&nbsp; &nbsp; 折扣调整<font color="#FF0000">'.$Order_Adjust_signed.'</font>元&nbsp; &nbsp;';
  $TotalPrice=FormatPrice($TotalPrice+$DeliveryFee+$Order_Adjust);
  if($TotalPrice!=$OriginOrderTotalPrice || $TotalScore!=$OriginOrderTotalScore){
    if($Order_State<5){//没有收款时可以调整
       $conn->exec('update mg_orders set totalprice='.$TotalPrice.',totalscore='.$TotalScore.' where ordername=\''.$OrderName.'\' and username=\''.$UserName.'\' and state<5');
    }
    else{
       $TotalPrice=$OriginOrderTotalPrice;
       $TotalScore=$OriginOrderTotalScore;
    }
  }?>
      	<span style='font-weight:bold;font-size:16pt'>→</span> &nbsp;  订单总额：<span id="TotalPriceCounter">￥<B><FONT color="#FF0000"><?php echo $TotalPrice;?></font></B>元</span>
        </td></tr>
      </table>

    </td>
 
  </tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif">
    	<b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />订单附加信息</b>
    </td>
  </tr>  
  <tr>
    <td>

      <form method="post" action="?mode=orderinfo" onsubmit="if(CheckOrderInfo(this))this.confirmbutton.disabled=true;else return false;" style="margin:0px"><input type="hidden" name="ordername" value="<?php echo $OrderName;?>">
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7">
        <td WIDTH="100" height="25" align="right" background="images/topbg.gif"><strong>收 货 人：</strong></td>
        <td> &nbsp; <input type="text" name="receipt" value="<?php echo $Order_Receipt;?>" maxlength=16 <?php echo $BaseInputStyle;?>></td>
        <td width="40%" rowspan="8" valign="top">
           <table width="100%" height="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
           <tr>
             <td height="20" align="center" background="images/topbg.gif"><strong>客户留言</strong></td>
           </tr>
           <tr>
             <td height="100%"  bgcolor="#F7F7F7" valign="top"><textarea  readOnly wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080"><?php echo $Order_UserRemark;?></textarea></td>
           </tr>
           </table>		
          </td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>收货地址：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="address" maxlength=100  value="<?php echo $Order_Address;?>" <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>联系电话：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="usertel" maxlength=50 value="<?php echo $Order_UserTel;?>"  <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>支付方式：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="paymethod" maxlength=10 value="<?php echo $Order_PayMethod;?>"  <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>配送方式：</strong></td>
          <td height="25"> &nbsp; 
          	 <input type="text" name="deliverymethod" value="<?php echo $Order_DeliveryMethod;?>" maxlength=10 <?php if($Order_State<5)echo 'style="width:75%"'; else echo 'style="width:95%" disabled';?>/><?php
if($Order_State<5){
  echo '<select onchange="var newmethod=this.options[this.selectedIndex].value;this.selectedIndex=0;this.form.deliverymethod.value=newmethod;if(newmethod.indexOf(\'上门\')>=0) this.form.deliverycode.value=\'0\';" style="width:20%"><option value="选择方式" >&nbsp;&nbsp;&nbsp;&nbsp;...</option>';  
  $res=$conn->query('select * from mg_delivery where method=2 order by sortorder',PDO::FETCH_ASSOC);
  foreach($res as $row){
    echo  '<option value="'.$row['subject'].'">'.$row['subject'].'</option>';            	   	
  }
  echo '<option value="其他方式" >其他方式</option></select>';
}?>
          </td>        
      </tr> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>包裹重量：</strong></td>
          <td height="25">
          	<table border=0 cellpadding="0" cellspacing="0" width="95%" height="25">
            <tr>
            	<td width="25%" height="25">&nbsp; <input type="text" name="orderweight" value="<?php echo round($OrderWeight);?>" <?php if($Order_State>4) echo 'disabled';?> style="width:50px;text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo');">千克</td>
            	<td width="20%" nowrap align="right">&nbsp;|&nbsp;<b>运单号码</b></td>
            	<td width="45%">&nbsp;<input type="text" name="deliverycode" maxlength=16 value="<?php echo $Order_DeliveryCode;?>" <?php if($Order_State>4) echo 'disabled';?> style="width:100%"></td>
            	<td width="10%" align="right"><input type="button" name="ordertrack" value="追踪" onclick="DeliveryTrack(this.form)"></td>
            </tr>
            </table>
          </td>
      </tr> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>客服备注：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="adminremark" value="<?php echo $Order_AdminRemark;?>" style="width:95%"></td>
      </tr>               
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong><?php echo (($Order_State<4)?'下单':'发货');?>时间：</strong></td>
          <td height="25">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
            	<td><?php echo date('Y-m-d H-i-s',$Order_ActionTime);?></td>
            	<td align="right"><input type="submit" name="confirmbutton" value="保存订单信息"></td>
            </tr>
            </table>
          </td>
      </tr>        
      </table></form>
    </td>
  </tr>
</table>
<script><?php
  echo 'InitMyOrder("'.$OrderName.'","'.$UserName.'",'.$Order_State.','.(($IsOrderManager)?'1':'0').','.((OwnPopedomFinance())?'1':'0').');';
//if($UserGrade==4 && $order_state==5)echo ' SyncPost("userid='.$userid.'&grade=4","change_usergrade.asp?mode=checkgrade");';
?></script>
</body>
</html><?php CloseDB();?>
