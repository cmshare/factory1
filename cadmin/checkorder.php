<?php
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

function HandleAction($mode){
  switch($mode){
    case 'amount':ChangeAmount();break;
    case 'score' :ChangeScore();break;
    case 'cost': ChangeCost();break;
    case 'audit' :ConfirmAudit();break;
    case 'price' :ChangePrice();break;
    case 'delete':DeleteOrder();break;
    case 'adjust':ChangeAdjust();break;
    case 'copy':CopyProducts();break;
    case 'remove':RemoveProduct();break;
    case 'migrate':MigrateProducts();break;
    case 'remark':ChangeRemark();break;
    case 'userremark':ChangeUserRemark();break;
    case 'adminremark':ChangeAdminRemark();break; 
    case 'deliveryfee':ChangerDdeliveryfee();break;
    case 'orderinfo':UpdateOrderinfo();break;
    case 'changeexporter':ChangePorter('exporter');break;
    case 'changeimporter':ChangePorter('importer');break;
    case 'orderstate':ChangeOrderState(false);break;
    case 'batchsettlement':ChangeOrderState(true);break;
  }
}

function UpdateOrderGoods($id,$field,$value,$states){
  return $GLOBALS['conn']->exec('update mg_ordergoods inner join mg_orders on mg_orders.ordername=mg_ordergoods.ordername set mg_ordergoods.'.$field.'='.$value.' where mg_ordergoods.id='.$id.' and mg_orders.state in ('.$states.')');
}

function ChangeAmount(){
  $selectid=trim($_POST['selectid']);
  $amount=trim($_POST['newvalue']);
  if(is_numeric($selectid) && $selectid>0 && is_numeric($amount)){
    if(UpdateOrderGoods($selectid,'amount',$amount,'-1,1,2'))echo '<OK>';
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

function ChangeRemark(){
  $selectid=trim($_POST['selectid']);
  if(is_numeric($selectid)&& $selectid>0){
    $remark=FilterText(trim($_POST['newvalue']));
    if(strlen($remark)>255) $remark=substr($remark,0,250).'...';
    if(UpdateOrderGoods($selectid,'remark',"'$remark'",'-1,1,2'))echo '<OK>';
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

function ChangeScore(){
  $selectid=trim($_POST['selectid']);
  $score=trim($_POST['newvalue']);
  if(is_numeric($selectid) && $selectid>0 && is_numeric($score)){
     $row=$GLOBALS['conn']->query('select mg_ordergoods.score as curscore,mg_product.score as maxscore from mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id where mg_ordergoods.id='.$selectid,PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($score!=$row['curscore']){
         if($score<=$row['maxscore']){
            if(UpdateOrderGoods($selectid,'score',$score,'-1,1,2'))echo '<OK>';
         }
         else echo '该商品单件最大积分不能超过'.$row['maxscore'].'分！';
       }
     }
   }
}

function ConfirmAudit(){
  $selectid=trim($_POST['selectid']);
  if(is_numeric($selectid) && $selectid>0){
    if(CheckPopedom('FINANCE')){ 
       if(UpdateOrderGoods($selectid,'audit','1','-1,1,2,3,4'))echo '<OK>';
       else echo '未知写入错误!'.$selectid;
    }
    else echo '权限错误';
  }
  else echo '参数错误';

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
      if($OrderState==1 || $OrderState==-1 || $OrderState==2 ||(($OrderState==3 || $OrderState==4) && CheckPopedom('FINANCE'))){
        $PriceUser='price'.$row['grade'];
        $newvalue=round($newvalue,2);
  	if($conn->exec('update (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) set mg_ordergoods.audit=(round(mg_product.'.$PriceUser.',2)='.$newvalue.'),mg_ordergoods.price='.$newvalue.' where mg_ordergoods.id='.$selectid))echo '<OK>';
      }
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

function DeleteOrder(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  if($OrderName){
    try{//事务管理
      $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();//事务开始
      $row=$conn->query('select id,username,operator,state from mg_orders where ordername=\''.$OrderName.'\' and (state=1 or state=-1) for update',PDO::FETCH_ASSOC)->fetch();
      if($row && ($row['operator']==$AdminUsername ||$row['username']==$AdminUsername || CheckPopedom('FINANCE'))){
         if($conn->exec('update mg_ordergoods set ordername=null where ordername=\''.$OrderName.'\'')){
           if($conn->exec('update mg_orders set state=0 where id='.$row['id'].' and state='.$row['state'])){
             $conn->commit();//事务完成
             echo '订单删除成功！<OK>';
           }
         }
      } 
    }
    catch(PDOException $ex){ 
      $conn->rollBack();  //事务回滚 
      echo  $ex->getMessage();
    } 
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
      if(is_numeric($OrderWeight)) $sql.=',weight='.round($OrderWeight*1000);
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

function CopyProducts(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $desorder=FilterText(trim($_POST['desorder']));
  $selectid=FilterText(trim($_POST['selectid']));
  if($OrderName && $desorder && $OrderName!=$desorder && $selectid){
    $addcount=0;
    $conn->exec('lock tables mg_orders write,mg_ordergoods write');         
    $row=$conn->query('select state,username,operator from mg_orders where ordername=\''.$desorder.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row && ($row['state']==-1||$row['state']==1||$row['state']==2) && ($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE'))){
      $res=$conn->query('select * from mg_ordergoods where id in ('.$selectid.') and ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC);
      foreach($res as $row){
	$sql="mg_ordergoods set productname='{$row['productname']}',price={$row['price']},score={$row['score']},amount={$row['amount']},remark='{$row['remark']}',audit={$row['audit']}";
        $existid=$conn->query('select id from mg_ordergoods where ordername=\''.$desorder.'\' and productid='.$row['productid'])->fetchColumn(0);
        if($existid){ 
	  $ret=$conn->exec("update $sql where id=$existid");
          if($ret)$addcount++;
          else if($ret===false)return false; 
        }
	else{
	  $sql.=",ordername='$desorder',productid={$row['productid']}";
	  if($conn->exec("update $sql where ordername is null limit 1") || $conn->exec("insert into $sql")) $addcount++;
          else return false;  
        }
      }
      if($addcount){
        $row=$conn->query('select sum(amount*price),sum(amount*score) from mg_ordergoods where ordername=\''.$desorder.'\'',PDO::FETCH_NUM)->fetch();
        if(!$row || $conn->exec("update mg_orders set totalprice={$row[0]},totalscore={$row[1]} where ordername='$desorder'")===false) return false;
      }
      echo '选定的商品已经成功复制到订单'.$desorder;
    }
    $conn->exec('unlock tables');         
  }
  else echo '参数错误！';
}
 
function MigrateProducts(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $desorder=FilterText(trim($_POST['desorder']));
  $selectid=FilterText(trim($_POST['selectid']));
  if($OrderName && $desorder && $OrderName!=$desorder && $selectid){
    $conn->exec('lock tables mg_orders write,mg_ordergoods write');         
    $row=$conn->query('select username,operator from mg_orders where ordername=\''.$OrderName.'\' and (state=-1 or state=1)',PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE')){
	$row=$conn->query('select state,username,operator from mg_orders where ordername=\''.$desorder.'\'',PDO::FETCH_ASSOC)->fetch();
	if($row){
	  if(($row['state']==-1||$row['state']==1||$row['state']==2) && ($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopedom('MANAGE')) ){
            $res=$conn->query("select id,productid from mg_ordergoods where id in ($selectid)",PDO::FETCH_ASSOC);
            foreach($res as $row){
              $existid=$conn->query("select id from mg_ordergoods where ordername='$desorder' and productid={$row['productid']}")->fetchColumn(0);
              if($existid)$conn->exec('update mg_ordergoods set ordername=null where id='.$existid);
              $conn->exec("update mg_ordergoods set ordername='$desorder' where id={$row['id']}");
            } 
	    $row=$conn->query('select sum(amount*price),sum(amount*score) from mg_ordergoods where ordername=\''.$desorder.'\'',PDO::FETCH_NUM)->fetch();
	    if($row)$conn->exec("update mg_orders set totalprice={$row[0]},totalscore={$row[1]} where ordername='$desorder' and (state=-1 or state=1)");
	    echo '选定的商品已经成功移动到订单'.$desorder.'！<OK>';
	  }
	  else echo '目标订单被拒绝添加！';
	}
	else echo '目标订单不存在！';
      }
    }
    $conn->exec('unlock tables');
  }
}

function RemoveProduct(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $selectid=trim($_POST['selectid']);
  if($OrderName && $selectid){
    $row=$conn->query('select username,operator from mg_orders where ordername=\''.$OrderName.'\' and (state=-1 or state=1)',PDO::FETCH_ASSOC)->fetch();
    if($row){
       if($row['username']==$AdminUsername || $row['operator']==$AdminUsername || CheckPopodeom('MANAGE')){
         if($conn->exec('update mg_ordergoods set ordername=null where id in ('.$selectid.') and ordername=\''.$OrderName.'\'')){
           echo '选定的商品已经成功从该订单中删除！<OK>';
         }
       }
    }
  }
}

function ChangeOrderState($batch_settlement){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['ordername']));
  $UserName=FilterText(trim($_POST['username']));
  $newstate=trim($_POST['newstate']);
  if(empty($OrderName) || empty($UserName) || !is_wholenumber($newstate) || $newstate==0 || $newstate<-4 || $newstate>5)PageReturn('参数错误！！'.$newstate);
  else if($newstate<0 && !CheckPopedom('STOCK'))PageReturn('用户权限错误！');
  try {//事务管理
      //设置使用抛出异常错误模式，默认为silent 则只是记录errorCode，发生错误时仍然继续往下执行。
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();//事务开始
    $row=$conn->query('select id,totalprice,totalscore,state,support,userremark,deliverycode,importer,exporter from mg_orders where ordername=\''.$OrderName.'\' and username=\''.$UserName.'\' for update',PDO::FETCH_ASSOC)->fetch();
    if(empty($row))PageReturn('没有订单!');
    $Order_State=$row['state'];
    $Order_Exporter=$row['exporter'];
    $Order_Importer=$row['importer'];

    $allowchange=false;
    switch($Order_State){
      case  1: $allowchange=($newstate==2);break;
      case  2: $allowchange=($newstate==1 || $newstate==3);break;  
      case  3: $allowchange=($newstate==2 || ($newstate==4 && $Order_Exporter>0 && $Order_Importer==0));break;
      case  4: $allowchange=($newstate==5 && CheckPopedom('FINANCE'));break;
      case -1: if($newstate==-2) {
		 if(!$row['userremark']) PageReturn('订单备注不能为空!');
		 if($Order_Exporter==$Order_Importer)PageReturn('出库与入库单位不能相同！');
		 $RecordCount=$conn->query('select count(*) from mg_ordergoods where ordername=\''.$OrderName.'\' and amount>0')->fetchColumn(0);
		 if(!$RecordCount) PageReturn('订单不能为空!');
		 if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Exporter) $allowchange=true;
	       }
	       break;
      case -2: if($newstate==-1){
		 if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Exporter) $allowchange=true;
	       }
	       else if($newstate==-3){
		 if($Order_Exporter==$Order_Importer)PageReturn('出库与入库单位不能相同！');
		 if(CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Importer) $allowchange=true;
	       }
	       break;
      case -3: if($newstate==-2 && (CheckPopedom('MANAGE')||GetAdminDepot()==$Order_Importer)) $allowchange=true;
	       else if($newstate==-4 && (CheckPopedom('MANAGE') && $Order_Importer!=$Order_Exporter)) $allowchange=true;
	       break;
    }
    if(!$allowchange) PageReturn('您没有权限执行该操作!',-1);
    $sql_state='update mg_orders set state='.$newstate.(($newstate==-4 || $newstate==4)?',actiontime=unix_timestamp()':'')." where ordername='$OrderName' and state=$Order_State";
    if($newstate==4){
      $OrderStockName='stock'.$row['exporter'];
      $DeliveryCode=$row['deliverycode'];
      if(empty($DeliveryCode) && $DeliveryCode!=='0')PageReturn('请先提交货单号！',-1);
      $sql2='update (mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid) set mg_product.stock0=mg_product.stock0-mg_ordergoods.amount,mg_product.'.$OrderStockName.'=mg_product.'.$OrderStockName.'-mg_ordergoods.amount,mg_product.solded=mg_product.solded+mg_ordergoods.amount where mg_ordergoods.ordername=\''.$OrderName.'\''; 
      if($conn->exec($sql2) && $conn->exec($sql_state)){
	$conn->exec('update mg_ordergoods set ordername=null where amount=0 and ordername=\''.$OrderName.'\'');          
	$conn->commit();//事务完成
	PageReturn('订单状态修改成功！');
      }
      else throw new PDOException('操作失败,数据库异常！');  
    }
    else if($newstate==5){
      if(!$batch_settlement){
	$audit_supend=$conn->query('select count(*) from mg_ordergoods where ordername=\''.$OrderName.'\' and amount>0 and audit=0')->fetchColumn(0);
	if($audit_supend)PageReturn('该订单中还有商品未审核！',-1);
      }
      $TotalPrice=$row['totalprice'];
      $TotalScore=$row['totalscore']; 
      if(is_null($TotalPrice)||is_null($TotalScore))PageReturn('请先刷新订单!'); 
     
      $row=$conn->query('select deposit,score from mg_users where username=\''.$UserName.'\' for update',PDO::FETCH_NUM)->fetch();
      if($row){
	$UserDeposit=$row[0];
	$UserScore=$row[1];
      }
      else PageReturn('参数错误，用户名不存在！',-1);	

      if($TotalPrice-$UserDeposit>100 && !$batch_settlement){
         session_start();
         if(!$_SESSION['arrearage']) PageReturn('该用户的余存款余额不足，货款无法缴扣！',-1);
      }
      $AdminIDNumber=GetAdminIDNumber();
   
      if($TotalPrice || $TotalScore){
	$UserScore+=$TotalScore;//积分允许为负（扣积分）
	$UserDeposit-=$TotalPrice;
	if($conn->exec('update mg_users set deposit='.$UserDeposit.',score='.$UserScore.' where username=\''.$UserName.'\'')) echo '<script>alert("已自动从用户预存款帐户中缴扣货款'.$TotalPrice.'元！获得'.$TotalScore.'积分！");</script>';
	else throw new PDOException('操作失败,数据库异常！');  
	if($TotalPrice>0 && !$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remark,actiontime) values(\''.$UserName.'\',\''.$AdminIDNumber.'\',2,'.(-$TotalPrice).','.$UserDeposit.',\'订单（'.$OrderName.'）付款\',unix_timestamp())')) throw new PDOException('财务日志异常！');  
	if($TotalScore!=0 && !$conn->exec('insert into mg_accountlog(username,adminuser,operation,amount,surplus,remark,actiontime) values(\''.$UserName.'\',\''.$AdminIDNumber.'\',1,'.$TotalScore.','.$UserScore.',\'订单（'.$OrderName.'）积分\',unix_timestamp())'))throw new PDOException('积分日志异常！');
      }
      if($conn->exec($sql_state)){
	  $conn->commit();//事务完成
	  PageReturn('订单状态修改成功！');
      }
      else throw new PDOException('订单状态修改时发生异常！'); 
    }
    else if($newstate==2 && $row['support']==0){
       if($conn->exec('update mg_orders set support='.GetAdminIDNumber().',operator=\''.$AdminUsername.'\',state='.$newstate.' where ordername=\''.$OrderName.'\' and state='.$Order_State)){
	 $conn->commit();//事务完成
	 PageReturn('订单状态修改成功！');
       }
    }
    else if($newstate==-4){
       if($Order_Exporter==0 && $Order_Importer>0) $sql2='mg_product.stock'.$Order_Importer.'=mg_product.stock'.$Order_Importer.'+mg_ordergoods.amount,mg_product.stock0=mg_product.stock0+mg_ordergoods.amount';
       else if($Order_Exporter>0 && $Order_Importer==0) $sql2='mg_product.stock'.$Order_Exporter.'=mg_product.stock'.$Order_Exporter.'-mg_ordergoods.amount,mg_product.stock0=mg_product.stock0-mg_ordergoods.amount';
       else if($Order_Exporter>0 && $Order_Importer>0) $sql2='mg_product.stock'.$Order_Exporter.'=mg_product.stock'.$Order_Exporter.'-mg_ordergoods.amount,mg_product.stock'.$Order_Importer.'=mg_product.stock'.$Order_Importer.'+mg_ordergoods.amount';	
       else PageReturn('参数无效');
       $sql2='update (mg_product inner join mg_ordergoods on mg_product.id=mg_ordergoods.productid) set '.$sql2.' where mg_ordergoods.ordername=\''.$OrderName.'\''; 
       if($conn->exec($sql2) && $conn->exec($sql_state)){
	  $conn->exec('update mg_ordergoods set ordername=null where amount=0 and ordername=\''.$OrderName.'\'');
	  $conn->commit();//事务完成
	  PageReturn('订单状态修改成功！');
	}
	else throw new PDOException('操作失败,数据库异常！');  
    }
    else if($conn->exec($sql_state)){
      $conn->commit();//事务完成
      PageReturn('订单状态修改成功！');
    }
  }  
  catch(PDOException $ex){ 
    $conn->rollBack();  //事务回滚 
    echo  $ex->getMessage();
  } 
}

?>
