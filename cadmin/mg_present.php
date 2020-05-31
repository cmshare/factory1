<?php require('includes/dbconn.php');
require('includes/mg_comm.php');
CheckLogin();
db_open();
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body leftmargin="0" topmargin="0">
<?php
$mode=@$_GET['mode'];

if($mode){
  if(!CheckPopedom('PRODUCT'))PageReturn('非法访问！',-1);
  if($mode=='score'){ 
    $newvalue=$_POST['newvalue'];
    if(is_numeric($newvalue)){
      $selectid=$_POST['selectid'];
      if($selectid>0){
   	if($conn->exec('update mg_present set score ='.$newvalue.' where productid='.$selectid)) PageReturn('赠品兑购分数修改成功！');
      } 
    }
  }
  else if($mode=='available'){
    $newvalue=$_POST['newvalue'];
    if(is_numeric($newvalue)){
      $selectid=@$_POST['selectid'];
      if($selectid>0){
   	if($conn->exec('update mg_present set available ='.$newvalue.' where productid='.$selectid))PageReturn('剩余赠品数量修改成功！');
      }
    }
  }
  else if($mode=='remark'){
    $newvalue=FilterText(trim(@$_POST['newvalue']));
    $selectid=$_POST['selectid'];
    if($selectid>0){
      $conn->exec("update mg_present set remark='$newvalue' where productid=$selectid");
      PageReturn('赠品备注修改成功！');
    }
  }
 else if($mode=='delete'){
    $selectid=@$_POST['selectid'];
    if(is_array($selectid)){
      $idlist=implode(',',$selectid);
      if($conn->exec('update mg_present set productid=0 where productid in ('.$idlist.')')) PageReturn('所选产品删除成功！');
    }
    else PageReturn("没有选择操作对象！",-1);
  }
  else if($mode=='addsave'){
    $productid=@$_POST['productid'];
    if(is_numeric($productid) && $productid>0){
      $existed=$conn->query('select id from mg_present where productid='.$productid)->fetchColumn(0);
      if($existed){
        echo $errmsg='添加失败，赠品已存在！';
      }
      else{
        $productscore=@$_POST['score'];
        $remark=FilterText(@$_POST['remark']);
        $sql="set score=$productscore,remark='$remark',productid=$productid,addtime=unix_timestamp()";
        if($conn->exec('update mg_present '.$sql.' where productid=0 limit 1')||$conn->exec('insert into mg_present '.$sql))$errmsg='赠品添加成功！'; 
        else $errmsg='Failt to add new present!';
      }
      echo '<br><br><br><br><br><p align=center><font color=#FF0000>'.$errmsg.'</font></p><br><p align=center onclick="parent.location.reload();parent.close();" style="cursor:pointer;color:#0000FF"><u>[确定]</u></p>';
    }
  }
  else if($mode=='addnewpresent'){
     $productid=@$_GET['productid'];
     if(is_numeric($productid) && $productid>0){
	$row=$conn->query('select id,name,stock0,price3,score from mg_product where id='.$productid,PDO::FETCH_ASSOC)->fetch();
	if($row){?>
        <form action="?mode=addsave" method="post" style="margin:0px"><tr bgcolor="#F7F7F7" align="center"> 
  	<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <td height="25" colspan="2" background="images/topbg.gif"><input type="hidden" name="productid" value="<?php echo $productid;?>"><strong>添加赠品</td>
          </tr>
        <tr>
          <td height="25" width="20%" align="center" bgcolor="#F7F7F7"><strong>商品编号</strong></td>
          <td height="25" width="80%" bgcolor="#FFFFFF">&nbsp;
           <font color="#FF0000"><?php echo $productid;?></font>  
          </td>
        </tr>
        <tr>
          <td height="25" width="20%" align="center" bgcolor="#F7F7F7"><strong>商品名称</strong></td>
          <td height="25" width="80%" bgcolor="#FFFFFF">&nbsp;
           <font color="#FF0000"><?php echo $row['name'];?></font>  
          </td>
        </tr>
        <tr>
          <td height="25" width="20%" align="center" bgcolor="#F7F7F7"><strong>兑购积分</strong></td>
          <td height="25" width="80%" bgcolor="#FFFFFF">&nbsp;
           <input name="score" type="text" class="input_sr" onMouseOver="this.focus()"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')" value="<?php echo $row['price3']*35;?>" maxlength=6>分 
          </td>
        </tr> 
        <tr>
          <td height="25" align="center" bgcolor="#F7F7F7"><strong>附加说明</strong></td>
          <td height="25" bgcolor="#FFFFFF"><textarea name="remark" cols="50" rows="4" class="input_sr" style="width:100%;"></textarea></td>
        </tr>
        <tr>
          <td height="25" colspan="2" align="center" bgcolor="#F7F7F7">
            <input name="Submit" type="submit" class="input_bot" value=" 保 存 ">
          </td>
        </tr>
        </table></form><?php
       }
       else{
         echo '<br><br><br><br><br><p align=center><font color=#FF0000>该商品编号不存在！</font></p><br><p align=center onclick="self.window.close()" style="cursor:pointer;color:#0000FF"><u>[确定]</u></p>';
       }
    }
    echo '</body></html>';
  }
  db_close();
  exit(0);
}?>


<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>  
    <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>赠品兑购区管理</font></b></td>
  </tr>
  <tr> 
    <form name="form1" method="post" action="">
      <td height="100" align="center" bgcolor="#FFFFFF"><?php
         $res=page_query('select mg_product.id,mg_product.name,mg_product.stock0,mg_present.available,mg_present.score,mg_present.remark','from mg_product inner join mg_present on mg_product.id=mg_present.productid','','order by mg_present.addtime desc',20);
	 if(empty($res)){			
       	    echo '<p align="center" class="contents"> 数据库中暂时无数据！</p>';
            echo '<p align="center"><input type="button"  onclick="AddNewPresent();" value=" 新 增... " /></p>';
         }
         else{?>
        <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr align="center" bgcolor="#F7F7F7" height="20"> 
            <td width="4%"  background="images/topbg.gif" height="25"><input type="checkbox" onClick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
            <td width="8%"  background="images/topbg.gif"><strong>编号</strong></td>
            <td width="50%" background="images/topbg.gif"><strong>名称</strong></td>
            <td width="6%"  background="images/topbg.gif"><strong>库存总数</strong></td>
            <td width="6%"  background="images/topbg.gif"><strong>赠品数量</strong></td>
            <td width="6%"  background="images/topbg.gif"><strong>兑购分</strong></td>
            <td width="20%"  background="images/topbg.gif"><strong>附加说明</strong></td>
           </tr><?php
           foreach($res as $row){
	     if($row['available']>$row['stock0']) $PresentAvailable=$row['stock0']; else $PresentAvailable=$row['available'];?>
          <tr bgcolor="#FFFFFF"  onMouseOut=mOut(this,"#FFFFFF"); onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
            <td height="25" align="center"><input name="selectid[]" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>
            <td height="25" align="center" nowrap><a href="mg_stocklog.php?id=<?php echo $row['id'];?>"><?php echo GenProductCode($row['id']);?></a></td>
            <td height="25" align="left"><a href="<?php echo GenProductLink($row['id']);?>" target="_blank"><?php echo $row['name'];?></a></td>
            <td height="25" align="center"><?php echo $row['stock0'];?></td>
            <td height="25" align="center"><span style="cursor:hand;" title="点击修改" onclick="ChangePresentStock(<?php echo $row['id'];?>,<?php echo $PresentAvailable;?>)"><u><?php echo $PresentAvailable;?></u></span></td>
            <td height="25" align="center"><span style="cursor:hand" title="点击修改" onclick="ChangeScore(<?php echo $row['id'];?>,<?php echo $row['score'];?>)"><u><?php echo $row['score'];?></u></span></td>
            <td height="25" align="center" nowrap style="cursor:hand" title="点击修改" onclick="ChangeRemark(<?php echo $row['id'];?>,this.innerText)">
            <?php if($row['remark']) echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$row['remark'].'</MARQUEE>';
                  else echo '&nbsp;';?>
            </td>
          </tr><?php
          }?>  
          <tr bgcolor="#FFFFFF">
            <td height="30" colspan="7" align="center">
         	<input type="button"  onclick="BatchDelete();" value=" 删 除 " />&nbsp;<input type="button"  onclick="AddNewPresent();" value=" 新 增... " />&nbsp;&nbsp; 
            </td>
          </tr>
        </table>
        <br>
        <script language="javascript">  
            GeneratePageGuider("",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);
        </script><?php
        }?>
  </td>
    </form>
  </tr>
</table>
<form name="MyTestForm" id="MyTestForm" method="post"><input type="hidden" name="selectid"><input type="hidden" name="newvalue"></form>

<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td width="100%" height="20" background="images/topbg.gif">
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
      	<td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>赠品兑换说明</font></b>
        </td>
        <td align="right">
           <input type="button" value=" 编辑 " onclick="ModifyRemark();"> &nbsp; &nbsp;
        </td>
      </tr>
      </table>
    </td>
  </tr>
  <tr>
     <td>
       <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF">
       <tr> 
         <td><?php
             $remark=$conn->query('select aboutpresent from mg_configs')->fetchColumn(0);
             if($remark)echo $remark;?>
         </td>
       </tr>
       </table>
    </td> 
 </tr>
 

 
</table>

<script language=javascript>
function BatchDelete()
{ var selcount=Checkbox_SelectedCount("selectid[]");
	if(selcount==0)
	{ alert("没有选择操作对象！");
	}
  else 
  { if(confirm("确定要删除所选的"+selcount+"件赠品吗？"))
  	{ document.form1.action = "?mode=delete";
      document.form1.submit();
  	}
  }
}

function AddNewPresent(){
   var OnGetProductID=function(productid){
      if(productid && !isNaN(productid)){
        AsyncDialog('添加赠品',"mg_present.php?mode=addnewpresent&productid="+productid,500,230);
        return true;
      }
   }
   AsyncPrompt('添加赠品','请输入赠品所对应的商品编号:',OnGetProductID,'',8);
}

function ChangeScore(productID,defValue)
{ var newvalue=window.prompt("请重新设定该赠品的兑购积分:\n\n", defValue);
	if(newvalue)
	{ if(isNaN(newvalue) || newvalue<1)
		{ alert('兑购积分无效！');
	  }
	  else
	  { newvalue=parseInt(newvalue);
		  if(newvalue!=defValue)
  	  { MyTestForm.action="?mode=score";
  	  	MyTestForm.selectid.value=productID; 
  	  	MyTestForm.newvalue.value=newvalue;
  	  	MyTestForm.submit();
	    } 
	  }
	}  
}

function ChangePresentStock(productID,defValue)
{ var newvalue=window.prompt("请重新设可用赠品数量:\n\n", defValue);
	if(newvalue)
	{ if(isNaN(newvalue) || newvalue<0)
		{ alert('数字无效！');
	  }
	  else
	  { newvalue=parseInt(newvalue);
		  if(newvalue!=defValue)
  	  { MyTestForm.action="?mode=available";
  	  	MyTestForm.selectid.value=productID; 
  	  	MyTestForm.newvalue.value=newvalue;
  	  	MyTestForm.submit();
	    } 
	  }
	}  
}

function ChangeRemark(OrderGoodsID,defValue)
{ var newvalue=window.prompt("请重新设定商品附加说明:\n\n", defValue);
	if(newvalue!=null && newvalue!=defValue)
  { MyTestForm.action="?mode=remark";
  	MyTestForm.selectid.value=OrderGoodsID; 
  	MyTestForm.newvalue.value=newvalue;
  	MyTestForm.submit();
	} 
} 

function ModifyRemark(){
  var OnReturn=function(ret){
     if(ret=='<OK>'){
       alert('保存成功');
       self.location.reload();
     }
     else if(ret)alert(ret);
     return true;
  }
  AsyncDialog('修改赠品兑换说明','mg_dbmemo.php?params=<?php echo simpleEncode('tb=mg_configs&field=aboutpresent&id=0');?>',780,300,OnReturn);
}

</script>
</body>
</html><?php db_close();?>
