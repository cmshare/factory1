<?php require('includes/dbconn.php');
 CheckLogin();
 OpenDB();
 $AllowModifyUserInfo=CheckPopedom('MANAGE');

 $mode=@$_GET['mode'];
 if($mode && $AllowModifyUserInfo){
    $userid=@$_POST['userid'];
    if(is_numeric($userid) && $userid>0){
      if($mode=='save'){
        $UserPassword=FilterText(trim($_POST['userpassword']));
        if($UserPassword && strlen($UserPassword)<=3) PageReturn('错误：登录密码长度必须大于3！',-1);
        $UserAnswer=FilterText(trim($_POST['useranswer']));
        if($UserAnswer && strlen($UserAnswer)<=2) PageReturn('错误：密码答案长度必须大于2！',-1);
        $realname=FilterText(trim($_POST['realname']));
        $usermail=FilterText(trim($_POST['usermail']));
        $usermobile=FilterText(trim($_POST['usermobile']));
        $userqq=FilterText(trim($_POST['userqq']));
        $userquestion=FilterText(trim($_POST['userquestion']));
        $receipt=FilterText(trim($_POST['receipt']));
        $usersex=@$_POST['usersex'];
        $district=@$_POST['district'];
        $address=FilterText(trim($_POST['address']));
        $usertel=FilterText(trim($_POST['usertel']));
        $remark=FilterText(trim($_POST['remark']));
        $sql="update mg_users set realname='$realname',usermail='$usermail',usermobile='$usermobile',userqq='$userqq',userquestion='$userquestion',receipt='$receipt',usersex=$usersex,district=$district,address='$address',usertel='$usertel',remark='$remark'";
        if($UserPassword)$sql.=",password=md5('$UserPassword')";
        if($UserAnswer) $sql.=",useranswer='$UserAnswer'";
        $sql.=' where  id='.$userid;
        if($conn->exec($sql))PageReturn('会员信息修改成功！');
        else PageReturn('会员信息没有修改！');
      }
      else if($mode=='setvipno'){
        $VIPNO=@$_POST['vipno'];
        if(is_numeric($VIPNO)){
          if($VIPNO>0){
            $existid=$conn->query('select id from mg_users where vipno='.$VIPNO)->fetchColumn(0);
            if(!$existid) $conn->exec('update mg_users set vipno='.$VIPNO.' where id='.$userid);
            else if($existid!=$userid)$err='此卡号已经被占用，操作失败!';
          }
          else $conn->exec('update mg_users set vipno=0 where id='.$userid); 
        }
        else $err='卡号无效！';
        echo ($err)?$err:'OK';
        CloseDB();
        exit(0);
      }
    }
 }

  
  $userid=@$_GET['id'];
  if(is_numeric($userid) && $userid>0) $sql='mg_users.id='.$userid;
  else{
    $username=FilterText(trim($_GET['user']));
    if($username)$sql='mg_users.username=\''.$username.'\'';
  }
  
  if($sql){
    $sql='select mg_users.*,mg_usrgrade.title from mg_users inner join mg_usrgrade on mg_users.grade=mg_usrgrade.id where '.$sql;
    $row=$conn->query($sql,PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($userid) $username=$row['username'];
      else $userid=$row['id'];
    }
    else PageReturn('用户不存在！');
  }else PageReturn('参数错误！');
  $EncUsername=rawurlencode($username);
  $VIPNO=($row['vipno'])?substr('00000'.$row['vipno'],-6):'';
  $Own_popedomFinance=CheckPopedom('FINANCE');?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>user/district.js" type="text/javascript"></SCRIPT>
<script>var UserInfoChanged=false;</script>
<body leftmargin="0" topmargin="0" onunload="if(opener && UserInfoChanged) opener.location.reload();">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>              
    <td background="images/topbg.gif" height="35"><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_users.php">客户管理</a> -&gt; <font color=#FF0000>客户详细资料</font></b></td>
  </tr>             
  <tr>              
   <form name="userinfo" method="post" action="?mode=save">
	<td valign="top" bgcolor="#FFFFFF">
	<table width="95%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF" height="25"> 
            <td width="20%" align="right" background="images/topbg.gif" bgcolor="#F7F7F7">会员名称：<input type="hidden" name="userid" value="<?php echo $userid;?>"></td>
            <td width="80%"><font color=#FF6600><?php echo $username;?></font></td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25" > 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">真实姓名：</td>
            <td><input name="realname" type="text" class="input_sr" value="<?php echo $row['realname'];?>" size="30" maxlength="16"> | 会员卡<font color="#006600" <?php if($AllowModifyUserInfo) echo 'style="text-decoration: underline;cursor:pointer"  onclick="ChangeUserVIPNO();" ';?>><?php echo ($VIPNO)?$VIPNO:'&nbsp;无&nbsp;';?></font></td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">会员性别：</td>
            <td><input type="radio" value="0" name="usersex" <?php if($row['usersex']==0) echo 'checked';?>/>男 <input type="radio" value="1" name="usersex"  <?php if($row['usersex']==1) echo 'checked';?>/>女</font></td>
          </tr> 
                 
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">所在地区：</td>
            <td><select name="provincelist" size="1"><option value="0" selected>请选择省份……</option></select>
                <select size="1" name="district"><option value="<?php echo $row['district'];?>">请选择城市……</option></select>
            </td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">联系电话：</td>
            <td><INPUT NAME="usermobile" TYPE="text" class="input_sr" VALUE="<?php echo $row['usermobile'];?>" SIZE="30"></td>
          </tr>           
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">电子邮件：</td>
            <td><input name="usermail" type="text" class="input_sr" value="<?php echo $row['usermail'];?>" size="30"></td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">腾讯ＱＱ：</td>
            <td><INPUT NAME="userqq" TYPE="text" class="input_sr" VALUE="<?php echo $row['userqq'];?>" SIZE="30"></td>
          </tr>          
	     
          <?php if($AllowModifyUserInfo){?>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">登录密码：</td>
            <td><input name="userpassword" type="password" class="input_sr" size="30"> *不改密码请为空!</td>
          </tr>          
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">密码提问：</td>
            <td><input name="userquestion" type="text" class="input_sr" value="<?php echo $row['userquestion'];?>" size="30"></td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">密码答案：</td>
            <td><input name="useranswer" type="text" class="input_sr" size="30"> *不改答案请为空! </td>
          </tr><?php
          }?>
 
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">收货人姓名：</td>
            <td><input name="receipt" type="text" class="input_sr" value="<?php echo $row['receipt'];?>" size="30"></td>
          </tr>
          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">收货人电话：</td>
            <td><input name="usertel" type="text" class="input_sr" value="<?php echo $row['usertel'];?>" size="45"></td>
          </tr>
           <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">收货人地址：</td>
            <td><input name="address" type="text" class="input_sr" value="<?php echo $row['address'];?>" size="60"></td>
          </tr>

          <tr bgcolor="#FFFFFF" height="25"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7">管理员备注：</td>
            <td><input name="remark" type="text" class="input_sr" value="<?php echo $row['remark'];?>" size="79"></td>
          </tr>        
          <tr bgcolor="#FFFFFF" height="25">
            <td nowrap align="right" background="images/topbg.gif" bgcolor="#F7F7F7"><a href="mg_accountlog.php?user=<?php echo $EncUsername;?>" style="color:#0000FF">会员账务信息</a>：</td>
            <td nowrap>&nbsp;会员级别<font color="#FF0000" style="cursor:pointer;" title="调整等级..." onclick="ChangeUserGrade(<?php echo $userid;?>)"><u><?php echo $row['title'];?></u></font>，预存款<font color="#FF0000" title="修改..." style="cursor:pointer;text-decoration:underline"  onclick="UserInfoChanged=true;modal_recharge(<?php echo $userid;?>,2);"><?php echo round($row['deposit'],1);?></font>元，<?php
            	$MyFund=$conn->query('select sum(amount) from mg_accountlog where username=\''.$username.'\' and (operation=5 or operation=6)')->fetchColumn(0);
                if($MyFund) echo '<b>待审款</b><font color="#00AA00"><b>'.round($MyFund,2).'</b></font>元，';?>可用积分<font color="#FF0000"  title="修改..." style="cursor:pointer;text-decoration:underline" onclick="UserInfoChanged=true;modal_recharge(<?php echo $userid;?>,1);"><?php echo $row['score'];?></font>分，已下订单<?php
     $totalorders=$conn->query('select count(*) from mg_orders where username=\''.$username.'\' and state>0')->fetchColumn(0);
     if($totalorders)echo '<a href="mg_orders.php?kn=username&kv='.$EncUsername.'" title="查看订单..." style="color:#FF0000"><u>'.$totalorders.'</u></a>';
     else echo '<font color=#FF0000>0</font>';?>笔，累计消费<a href="mg_statmonthsales.php?user=<?php echo $EncUsername;?>" title="查看明细..."><u><font color=#FF0000><?php
     $totalconsume=$conn->query('select sum(totalprice) from mg_orders where username=\''.$username.'\' and state>3')->fetchColumn(0);
     echo ($totalconsume)?round($totalconsume,1):'0';?></font></u></a>元，购物车中有<a href="mg_checkcart.php?id=<?php echo $userid;?>" title="查看购物车..." style="color:#FF0000"><u><?php
     $productInCart=$conn->query('select sum(amount) from mg_favorites where userid='.$userid.' and state>1')->fetchColumn(0);
     echo ($productInCart)?$productInCart:'0';?></u></a>件商品.
            </td>
          </tr>

          <tr bgcolor="#FFFFFF" height="25"> 
            <td nowrap align="right" background="images/topbg.gif" bgcolor="#F7F7F7" >网站登录统计：</td>
            <td nowrap>&nbsp;注册于<?php echo date('Y-m-d H:i:s',$row['addtime']);?>，共登录<?php echo $row['logincount'];?>次，最后登录于<?php echo date('Y-m-d H:i:s',$row['lastlogin']);?>&nbsp;@<?php echo $row['lastip'];?></td>
          </tr>


          <?php if($AllowModifyUserInfo){?>
          <tr> 
            <td height="28" colspan="2"  background="images/topbg.gif" align="center"><input type="button"  value="保存基本资料" onclick="UserInfoChanged=true;this.form.submit()"> &nbsp; &nbsp; &nbsp; &nbsp;</td>
          </tr><?php
          }?>
        </table>
        <br>
	</td></form>
  </tr>
</table>
<script>
var obj=document.forms["userinfo"];
if(obj) InitDistrictSelection(obj);
function ChangeUserGrade(userid){
  var onChangeGrade=function(ret){
   if(ret){
      alert(ret);
      self.location.reload();
      return true;
    }
  }
  AsyncDialog("修改会员等级","change_usergrade.php?userid="+userid+"&handle="+Math.random(),450,100,onChangeGrade);
}	

function ChangeUserVIPNO(){
  var on_get_vipno=function(ret){
    if(ret && !isNaN(ret)){
      ret=SyncPost("userid=<?php echo $userid;?>&vipno="+ret,"?mode=setvipno");
      if(ret=='OK'){
        alert('修改成功！');
        self.location.reload();
      }
      else if(ret)alert(ret);
    }
    else alert('请输入有效的卡号效！');
  }
  AsyncPrompt('用户会员卡绑定','输入会员卡号:',on_get_vipno,"<?php echo $VIPNO;?>",6);
}

function modal_recharge(userid,operation){
  var OnDialogReturn=function(ret){
    if(ret){
     alert('操作成功！');
     self.location.reload();
    }
    return true;  
  }
  AsyncDialog("用户充值","mg_recharge.php?userid="+userid+"&operation="+operation,500,420,OnDialogReturn);
}
</script>
</body>
</html><?php
CloseDB();?>
