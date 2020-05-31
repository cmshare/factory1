<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$OwnPopedomManage=CheckPopedom('MANAGE');
$userid=@$_GET['userid'];
if($userid && !(is_numeric($userid) && $OwnPopedomManage)) $userid=0;
if($userid)$sql='select * from mg_admins where id='.$userid;
else $sql='select * from mg_admins where username=\''.$AdminUsername.'\'';
$row=$conn->query($sql,PDO::FETCH_ASSOC)->fetch();
if(empty($row))PageReturn('参数错误');
else if(!$userid)$userid=$row['id'];

if(@$_GET['action']=='save'){
    if(!$OwnPopedomManage && $row['idverified']) PageReturn('无权限修改！');
    $admin_old=$row['username'];
    $admin_new=FilterText(trim($_POST['admin']));
    if($admin_new && $admin_new!=$admin_old){
      if(!$OwnPopedomManage) PageReturn('您无权修改用户名！');
      $existid=$conn->query('select id from mg_users where username=\''.$admin_new.'\'')->fetchColumn(0);
      if($existid) PageReturn('该用户名已经存在！');
      else if($conn->exec("update mg_users set username='$admin_new' where username='$admin_old'")){
        $conn->exec("update mg_users set username='$admin_new' where username='$admin_old'");
        $conn->exec("update mg_orders set operator='$admin_new' where operator='$admin_old' and state<4"); 
        $conn->exec("update mg_message set sendto='$admin_new' where sendto='$admin_old'");
      }
    }
    $realname=FilterText(trim($_POST['realname']));
    $identity=FilterText(trim($_POST['identity']));
    $telephone=FilterText(trim($_POST['telephone']));
    $mobilephone=FilterText(trim($_POST['mobilephone']));
    $familyaddress=FilterText(trim($_POST['familyaddress']));
    $contactaddress=FilterText(trim($_POST['contactaddress']));
    $sql="update mg_admins set realname='$realname',identity='$identity',telephone='$telephone',mobilephone='$mobilephone',familyaddress='$familyaddress',contactaddress='$contactaddress'";
    if($OwnPopedomManage){
      $RegDate=trim($_POST['hiredate']);
      if($RegDate){
         $RegDate=strtotime($RegDate); 
         if($RegDate<0)$RegDate=0;
         if($RegDate) $sql.=',hiredate='.$RegDate;
      }
      $NewDepot=$_POST['depot'];
      if(is_numeric($NewDepot) && $NewDepot>0) $sql.=',depot='.$NewDepot;

      //$PhyAddr=strtoupper(trim($_POST['phyaddr']));
      //$PhyAddr2=strtoupper(trim($_POST['phyaddr2']));
      $remark=FilterText(trim($_POST['remark']));
      $IDVerified=($_POST['idverified']=='1')?'1':'0';
      $sql.=",remark='$remark',idverified=$IDVerified";
    }

    //PageReturn('$sql');
    $sql.=' where id='.$userid;
    $conn->exec($sql); 
    PageReturn('保存成功！');
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>修改个人资料</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="22" background="images/topbg.gif" width="100%">
    	<table border=0 height="22" cellpadding="0" cellspacing="0" width="100%">
    	<tr><td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是：<a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>员工个人资料</font></b></td>
      	<td align="center"><A href="admlogout.php">[<u>注销退出</u>]</td>
      	<td nowrap align="right"><select <?php if(!$OwnPopedomManage)echo 'disabled';?> onChange="self.location.href='?userid='+this.value;"><?php
         $admins=$conn->query('select mg_admins.id,mg_admins.username from mg_admins inner join mg_users on mg_admins.username=mg_users.username where mg_users.popedom is not null order by mg_admins.username',PDO::FETCH_NUM);
         foreach($admins as $adminrow){
           $selected=($adminrow[0]==$userid)?' selected':''; 
      	   echo '<option value="'.$adminrow[0].'"'.$selected.'>'.$adminrow[1].'</option>';
         }?></select>
      	</td>
      </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> 

	     <TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="4" CELLSPACING="1" bgcolor="#ffffff">
                      <TR><FORM NAME="MyForm" id="MyForm" METHOD="post" ACTION="?action=save&userid=<?php echo $userid;?>">
                        <TD height="25" bgcolor="#f7f7f7" align="right" >员工编号：</TD>
                        <TD height="25"><INPUT NAME="idnumber" type="text" class="input_sr" size="20" disabled value="<?php echo $row['idnumber'];?>" disabled></TD>
                      </TR>
                      <TR>
                        <TD width="20%" height="25" align="right" bgcolor="#f7f7f7">用 户 名：</TD>
                        <TD width="80%" height="25"><INPUT NAME="admin" TYPE="text" class="input_sr" size="20" maxlength="16" value="<?php echo $row['username'];?>" <?php if(!$OwnPopedomManage)echo 'readOnly';?>/><span class="style1">*</span> 联系管理员修改 </TD>
                      </TR><?php
                      if(!$row['idverified'] || $OwnPopedomManage){?>
                      <TR>
                        <TD height="25" align="right" bgcolor="#f7f7f7">真实姓名： </TD>
                        <TD height="25"><INPUT NAME="realname" TYPE="text" class="input_sr" size="20" maxlength="4" value="<?php echo $row['realname'];?>"> <span class="style1">*</span></TD>
                      </TR>

                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">证件号码： </div></TD>
                        <TD height="25"><INPUT NAME="identity" type="text" class="input_sr" size="25" maxlength="22" value="<?php echo $row['identity'];?>">
                            <span class="style1">*</span> 填身份证号码</TD>
                      </TR>
                     <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">固定电话： </div></TD>
                        <TD height="25"><INPUT NAME="telephone" TYPE="text" class="input_sr" size="25" maxlength="16" value="<?php echo $row['telephone'];?>">
                            <span class="style1">*</span> </TD>
                      </TR>
                      </TR>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">移动电话： </div></TD>
                        <TD height="25"><INPUT NAME="mobilephone" TYPE="text" class="input_sr" size="25" maxlength="16" value="<?php echo $row['mobilephone'];?>">
                            <span class="style1">*</span> </TD>
                      </TR>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">家庭住址： </div></TD>
                        <TD height="25"><INPUT NAME="familyaddress" TYPE="text" class="input_sr" size="50" maxlength="50"  value="<?php echo $row['familyaddress'];?>">
                            <span class="style1">*</span> 家庭居住地址</TD>
                      </TR>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">联系地址： </div></TD>
                        <TD height="25"><INPUT NAME="contactaddress" TYPE="text" class="input_sr" size="50" maxlength="50" value="<?php echo $row['contactaddress'];?>">
                            <span class="style1">*</span> 目前联系地址。 </TD>
                      </TR><?php
                      }
                      if($OwnPopedomManage){?>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">入职日期： </div></TD>
                        <TD height="25"><INPUT NAME="hiredate" type="text" class="input_sr" size="25" maxlength="22" value="<?php echo date('Y-m-d',$row['hiredate']);?>">
                        &nbsp; 绑定<select name="depot"><option value="0">--选择场所--</option><?php
                        $res_depot=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
                        foreach($res_depot as $row_depot){
                          $selected=($row_depot[0]==$row['depot'])?' selected':'';
                      	  echo '<option value="'.$row_depot[0].'"'.$selected.'>'.$row_depot[1].'</option>';
                        }?></select> &nbsp; <span class="style1">*</span> 入职时间与库房绑定。 
                        </TD>
                      </TR>
                      <!--TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">网络地址： </div></TD>
                        <TD height="25"><INPUT NAME="PhyAddr" TYPE="text" class="input_sr" size="50" maxlength="50" value="<?php echo $row['PhyAddr'];?>">
                            <span class="style1">*</span> 电脑主机唯一物理地址。 </TD>
                      </TR>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">备选地址： </div></TD>
                        <TD height="25"><INPUT NAME="PhyAddr2" TYPE="text" class="input_sr" size="50" maxlength="50" value="<?php echo $row['PhyAddr2'];?>">
                            ～备选网络地址，以允许用户有第二登录点。 </TD>
                      </TR-->
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7" align="right" valign="top">备注信息：</TD>
                        <TD height="25">
                          <textarea name="remark" cols="50" rows="5"><?php if($row['remark']) echo $row['remark'];?></textarea>
                        </TD>
                      </TR>
                      <TR>
                        <TD height="25" bgcolor="#f7f7f7"><div align="right">资料审核： </div></TD>
                        <TD height="25"><INPUT NAME="idverified" TYPE="checkbox" class="input_sr" value="1" <?php if($row['idverified'])echo 'checked';?>/>
                            <span class="style1">*</span> 资料审核后，用户无法自行更改。 </TD>
                      </TR><?php
                      }?>
                      <TR>
                       <TD>    	<?php if(!$OwnPopedomManage){ 
    	    if(!$row['idverified']) echo '<p align=center><font color=#FF0000>资料未审核，可以修改！</font></p>';
    	    else echo '<p align=center><font color=#FF0000>资料已审核，不可以修改！</font></p>';
                       }?></TD>
                        <TD height="30"><INPUT TYPE="button" class="input_bot" VALUE=" 递交修改 "  onclick="FormCheck(this.form)" <?php if(!$OwnPopedomManage && $row['idverified']) echo 'disabled';?>/></TD>
                      </TR>
                    </FORM>
                  </TABLE>
	     </td>
  </tr>
</table>

<script LANGUAGE="javascript">

function FormCheck(myform)
{	
	if(!myform.admin.value.trim())
	{ alert("用户名不能为空！");
		return;
	}
	
	if(myform.depot && myform.depot.selectedIndex==0)
	{ alert("请选择该员工的绑定场所！");
		return;
	}
	
	if(myform.PhyAddr && myform.PhyAddr.value.trim())
	{ var re = new RegExp("[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}","ig");
		if(!re.test(myform.PhyAddr.value.trim()))
		{  alert("网络地址(MAC)格式不对！");
			 return;
		}
		
	}
	
	myform.identity.value=myform.identity.value.trim();
	if(myform.identity.value)
	{ var idcode=CheckIdentity(myform.identity.value);
	  if (idcode!=myform.identity.value)
	 	{ alert(idcode);
	 	  return;
	 	}
	}
  myform.submit();
}
</script> 
</body>
</html><?php
db_close();?>
