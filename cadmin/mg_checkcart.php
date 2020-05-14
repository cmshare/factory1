<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();


$mode=@$_POST['mode'];
if($mode){
  if($mode=='save'){
    $productid=$_POST['productid'];
    $amount=$_POST['amount'];
    $userid=$_POST['userid'];
    $bExist=0;
    if(is_numeric($productid) && $productid>0 && is_numeric($amount) && is_numeric($userid) && $userid>0){
      $AdminDepotIndex=GetAdminDepot();
      $UserGrade=$conn->query('select grade from mg_users where username=\''.$AdminUsername.'\'')->fetchColumn(0);
      if($AdminDepotIndex && $UserGrade){
        $UserPriceName='price'.$UserGrade;
        $row=$conn->query('select id,barcode,stock'.$AdminDepotIndex.' as stock,name,'.$UserPriceName.',score from mg_product where id='.$productid,PDO::FETCH_ASSOC)->fetch();
        if($row){
          $retcode=$row['id'].'|'.$row['barcode'].'|'.$row['stock'].'|'.$row[$UserPriceName].'|'.$row['score'].'|'.$amount.'|'.$row['name'];
          $row=$conn->query('select id,amount,state from mg_favorites where userid='.$userid.' and productid='.$productid,PDO::FETCH_ASSOC)->fetch();
          if($row){
            $sql=($row['amount']!=$amount)?'amount='.$amount:'';
  	    if(!($row['state']&0x2)){
              if($sql)$sql.=',state=state+2';
              else $sql='state=state+2';
            }else $bExist=1;
            if($sql)$conn->exec('update mg_favorites set '.$sql.' where id='.$row['id']);          
          }
          else{
             $sql='mg_favorites set userid='.$userid.',productid='.$productid.',amount='.$amount.',remark=null,state=2';
             if(!$conn->exec('update '.$sql.' where state=0 limit 1'))$conn->exec('insert into '.$sql);
          }
  	  echo '|'.$bExist.'|'.$retcode;
        }
      }
    }
  }
  else if($mode=='search'){
    $SelLimit=100;
    $sql='select id,barcode,name from mg_product where recommend>0';
    $keyword=trim(@$_POST['barcode']);
    if($keyword){
      if(is_numeric($keyword)){
         if(strlen($keyword)>8)$res=$conn->query($sql.' and barcode=\''.$keyword.'\' order by recommend desc limit '.$SelLimit,PDO::FETCH_ASSOC);
         else if($keyword>0)goto label_search_by_id;
         else $res=null;
      }
      else{
        goto label_search_by_name;
      }
    }
    else{
      $keyword=trim(@$_POST['productcode']);
      if(is_numeric($keyword) && $keyword>0){
        label_search_by_id:
  	$res=$conn->query($sql.' and id='.$keyword,PDO::FETCH_ASSOC);
      } 
      else{
  	$keyword=FilterText(trim(@$_POST['productname']));
        if($keyword){ 
          label_search_by_name:
	  if(strpos($keyword,' ')>0){
	    $key_list=explode(' ',$keyword);
	    for($i=0;$i<count($key_list);$i++){
	      $subkey=trim($key_list[$i]);
	      if($subkey){ 
		$sql.=' and name like \'%'.$subkey.'%\'';
	      }
	    }
	  }
	  else{
	    $sql.=' and name like \'%'.$keyword.'%\'';
	  }
          $res=$conn->query($sql.' order by name desc limit '.$SelLimit,PDO::FETCH_ASSOC);
        }
        else $res=null;
      }
    }
    if($res){
      $ret='';
      foreach($res as $row){
        $ret.='|'.$row['id'].'|'.$row['barcode'].'|'.$row['name'];
      }
      if($ret)echo $ret;
      else{
         echo '<NONE>';
      }
    }else echo '参数无效!';
  }
  else if($mode=='amount'){
    $productid=$_POST['productid'];
    $amount=$_POST['amount'];
    $userid=@$_POST['userid'];
    if(is_numeric($productid)&& $productid>0 && is_numeric($amount)&& is_numeric($userid) && $userid>0){
      if($conn->exec('update mg_favorites set amount='.$amount.' where userid='.$userid.' and productid='.$productid.' and (state&0x2)')) echo '商品数量修改成功！<OK>';
      else echo 'ok';
    }
  }
  else if($mode=='remark'){
    $productid=$_POST['productid'];
    $userid=@$_POST['userid'];
    if(is_numeric($productid) && $productid>0 && is_numeric($userid) && $userid>0){
      $remark=FilterText(trim($_POST['remark']));
      if(strlen($remark)>200) $remark=substr($remark,0,197).'...';
      if($conn->exec('update mg_favorites set remark=\''.$remark.'\' where userid='.$userid.' and (state&0x2) and productid='.$productid)) echo '商品备注修改成功！<OK>';
    } 
  }
  else if($mode=='delete'){
    $selectid=FilterText($_POST['selectid']);
    $userid=FilterText($_POST['userid']);
    if($selectid && is_numeric($userid)){
      if($conn->exec('update mg_favorites set state=state-2 where userid='.$userid.' and productid in ('.$selectid.') and (state&0x2)'))echo '<OK>';
    }
  }
          
  CloseDB();
  exit(0);
}

$UserID=@$_GET['id'];
if(is_numeric($UserID)) $UserID=(int)$UserID; else $UserID=0;
if($UserID==0){
  $UserName=FilterText(trim(@$_GET['username']));
  if(empty($UserName))$UserName=$AdminUsername;
}
$AdminDepotIndex=GetAdminDepot();

$sql='select mg_users.id,mg_users.username,mg_users.grade,mg_users.deposit,mg_users.lastlogin,mg_usrgrade.title from mg_users inner join mg_usrgrade on mg_users.grade=mg_usrgrade.id where mg_users.'.(($UserID>0)?'id='.$UserID:'username=\''.$UserName.'\'');
$row=$conn->query($sql,PDO::FETCH_ASSOC)->fetch();
if($row){
  $UserID=$row['id'];
  $UserName=$row['username'];
  $UserGrade=$row['grade'];
  $UserDeposit=$row['deposit'];
  $UserPriceName='price'.$UserGrade;
  $UserTitle=$row['title'];
}
else{
  PageReturn('参数错误！',0);
}

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.GrayRow {color:#BFBFBF;}
.GrayRow A:link{color:#BFBFBF;}
.GrayRow A:visited{color:#BFBFBF;}
-->
</style>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="checkcart.js" type="text/javascript"></SCRIPT>
</head>
<body topmargin="0" leftmargin="0" onload="InitCodetype();document.forms[0].codetext.focus();"><?php  

$sql='select mg_favorites.id,mg_favorites.productid,mg_favorites.amount,mg_favorites.remark,mg_product.name,mg_product.barcode,mg_product.price0,mg_product.'.$UserPriceName.',mg_product.score,mg_product.stock'.$AdminDepotIndex.' as stock,mg_product.onsale from (mg_favorites inner join mg_product on  mg_favorites.productid=mg_product.id) inner join mg_category on mg_category.id=mg_product.brand where mg_favorites.userid='.$UserID.' and (mg_favorites.state&0x2) order by mg_category.sortindex,mg_product.name';
$res=$conn->query($sql,PDO::FETCH_ASSOC);
?>	
<form style="margin:0px" onsubmit="return false">
  <table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td background="images/topbg.gif" height=30>
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<tr>
    	  <td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>购物车管理</font></b> &nbsp; （<a style="color:#00AAFF;text-decoration:underline;cursor:pointer" href="mg_usrinfo.php?id=<?php echo $UserID;?>"><?php echo $UserName;?></a>，<font color="#FF6600"><?php echo $UserTitle;?></font>，预存款<font color=#FF0000><?php echo FormatPrice($UserDeposit);?></font>元）</td>
    	  <td align="right" nowrap><input type="hidden" name="userid" value="<?php echo $UserID;?>"><input type="button" value="移除商品" onclick="DeleteFromMycart(this.form)"> <input type="button" value="递交订单" onclick="SubmitCart(this.form)"> <input type="button" value="下载清单" onclick="window.open('mg_downcart.php?userid=<?php echo $UserID;?>&handle='+Math.random())">&nbsp;</td>
    	</tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td height="100%" valign="top" bgcolor="#FFFFFF">
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
          <td align="left" valign="middle" width="50%" nowrap background="images/topbg.gif">&nbsp;
          <strong>按商品<select name="codetype" onchange="setCookie('codetype',this.selectedIndex)"><option value="0">条码</option><option value="1">编号</option><option value="2">名称</option></select><input type="text" name="codetext" style="width:120px" onFocus="this.select()" onkeyup="if(event.keyCode==13)this.form.confirmbtn.click();"></strong><input type="button"  name="confirmbtn" value="确定" onclick="AddProductToCart(this.form);">&nbsp; &nbsp; <span id="retstatus"></span><strong id="addpreview" style="display:none"><input type="hidden" name="productcode"><input type="hidden" name="barcode"><input type="text" name="productname" readOnly style="width:300px;color:#FF6600;border:0px;background:transparent;">&nbsp; &nbsp;数量：<input type="text" name="productamount" style='width:38px;text-align:center' onkeyup="if(event.keyCode==13)this.form.confirmbtn.click();else if(event.keyCode==27)QuitSave(this.form);" maxlength=5>&nbsp; <input type="button" name="savebtn" value="保存" style="color:#FF0000;" onclick="SaveToCart(this.form)"><input type="button" name="cancelbtn" value="取消" onclick="QuitSave(this.form)"></strong>
          </td>
        </tr>
      </table>

      <table id="DummyTable" style="display:none" border=0>
      <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
        <td><input type="checkbox" name="selectid[]" value="0" onclick="mChk(this)"></TD>
        <td></td>
        <td></td>
        <td align="left" style="padding-left:6px"></td>
        <td style="cursor:pointer;text-decoration:underline" title="点击修改" onclick="ChangeAmount(this)"></td>
        <td></td>
        <td></td>
        <td style="cursor:pointer;text-decoration:underline" title="点击修改" onclick="ChangeRemark(this)">&nbsp;</td> 
       </tr>
       </table>
			 
       <table id="mytable" width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
       <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="5%"  height="25" align="center" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
          <td WIDTH="6%"  height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="10%"  height="25" align="center" background="images/topbg.gif"><strong><strong>条码</strong></strong></td>
          <td WIDTH="50%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="7%"  height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="7%"  height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="7%"  height="25" align="center" background="images/topbg.gif"><strong>积分</strong></td>
          <td WIDTH="8%"  height="25" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
         </tr><?php
$TotalRecords=0;
$TotalCount=0; //商品总数
$TotalPrice=0;  //总价
$TotalScore=0;
$PIDArray='';
$TotalPrice='';
$PriceArray='';
$ScoreArray='';
$AmountArray='';
$StockArray='';

foreach($res as $row){
  $ProductName=$row['name'];
  $Amount=$row['amount'];
  $remark=$row['remark'];
  //if remark<>"" then remark=Server.HtmlEncode(remark)
  $TotalRecords++;
  $TotalCount+=$Amount;	
  $TotalScore+=$Amount*$row['score'];
  $myprice=$row[$UserPriceName];
  if(($row['onsale']&0xf)>0 && $UserGrade>2){
    if($row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
  }
  $productid=$row['productid'];
  $ProductCode=GenProductCode($productid);
  $PIDArray.=$productid.',';
  $TotalPrice+=$Amount*$myprice;
  $PriceArray.=$myprice.',';
  $ScoreArray.=$row['score'].',';
  $AmountArray.=$row['amount'].',';
  $StockArray.=$row['stock'].',';?>
  <tr id="<?php echo $ProductCode;?>" align="center" <?php if($Amount==0) echo 'class="GrayRow"';?> bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
    <td align="center"><input type="checkbox" name="selectid[]" value="<?php echo $productid;?>" onclick="mChk(this)"></td>
    <td align="center"><a href="mg_stocklog.php?id=<?php echo $productid;?>" title="本地库存<?php echo $row['stock'];?>件，点击查看详情..."><?php echo $ProductCode;?></a></td>
    <td align="center"><?php echo $row['barcode'];?></td>
    <td align="left" style="padding-left:6px"><?php
       if($Amount>$row['stock']) echo '<a href="'.GenProductLink($productid).'" target="_blank" style="color:#FF0000;text-decoration:line-through">'.$ProductName.'</a><img src="images/lack.gif" border=0 width=16 height=16>';
       else echo '<a href="'.GenProductLink($productid).'"  target="_blank">'.$ProductName.'</a>';?></td>
    <td align="center" style="cursor:pointer;text-decoration:underline" title="点击修改" onclick="ChangeAmount(this)"><?php echo $Amount;?></td>
    <td align="center"><?php echo FormatPrice($myprice);?></td>
    <td align="center"><?php echo $row['score'];?></td>
    <td align="center" style="cursor:pointer;text-decoration:underline" title="点击修改" onclick="ChangeRemark(this)">
    <?php if($remark) echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$remark.'</MARQUEE>';
          else echo '&nbsp;';?></td> 
  </tr><?php
}
if($TotalRecords){
   echo '<script>
   PIDArray=new Array('.$PIDArray.'0);
   PriceArray=new Array('.$PriceArray.'0);
   ScoreArray=new Array('.$ScoreArray.'0);
   AmountArray=new Array('.$AmountArray.'0);
   StockArray=new Array('.$StockArray.'0);
   ProductCounter='.$TotalRecords.';                    
  </script>';
}
else echo '<tr bgcolor="#FFFFFF" id="dummyrow"><td align="center" colspan=8><br>购物车为空！<br><br></td><p align=center></tr>';?>
      <tr height="20" bgcolor="#FFFFFF"> 
         <td height="25" align="center" colspan="4" background="images/topbg.gif"><b>合计</b></td>
         <td height="25" align="center" background="images/topbg.gif" id="TotalCount"><?php echo $TotalCount;?></td>
         <td height="25" align="center" background="images/topbg.gif" id="TotalPrice"><?php echo FormatPrice($TotalPrice);?></td>
         <td height="25" align="center" background="images/topbg.gif" id="TotalScore"><?php echo $TotalScore;?></td>
         <td height="25" align="center" background="images/topbg.gif">&nbsp;</td>
       </tr>
       </table>
    </td>

  </tr>
</table>
</form>
</body>
</html><?php CloseDB();?>
