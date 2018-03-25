<?php require("includes/dbconn.php");
CheckLogin();
OpenDB();
CheckMenu('超级用户管理');
if(($mode=@$_GET["mode"])) {
  if($mode=="modify") {
    $userid=@$_POST["userid"];
    $username=FilterText(trim(@$_POST["username"]));
    if($username && is_numeric($userid) and $userid>0){
      $popedom=trim(@$_POST["popedom"]);
      if($conn->exec("update `mg_users` set popedom='$popedom' where id=$userid")){
        if($username==$AdminUsername) setcookie('meray[popedom]',$popedom);
        PageReturn("修改成功！");
      }else PageReturn("没有修改！");
    }
  }
  else if($mode=="add"){
    $username=FilterText(trim(@$_POST["username"]));
    $popedom=FilterText(trim(@$_POST["popedom"]));
    if($username && $popedom){
      $row=$conn->query("select popedom from `mg_users` where username='$username'")->fetch(PDO::FETCH_NUM);
      if(empty($row))PageReturn('该用户名尚未注册！');
      else if($row[0]) pagereturn("该用户已经是管理员名，不能重复添加！",-1);
      else{
        if($conn->exec("update `mg_users` set popedom='$popedom' where username='$username'")){
          $adminid=$conn->query("select id from mg_admins where username='$username'")->fetchColumn(0);
          if(empty($adminid))$conn->exec("insert into mg_admins set username='$username'");
          PageReturn("添加成功！");
        }
      }  
    }
  }
  else if($mode=="delete") {
    $userid=$_POST["userid"];
    if(is_numeric($userid) && $userid>1){ //禁止删除userid为1的超级管理员
      if($conn->exec("update mg_users inner join mg_admins on mg_users.username=mg_admins.username set mg_users.popedom=null,mg_admins.idnumber=0,mg_admins.idverified=0,mg_admins.ordercoordinator=0 where mg_users.id=$userid"))PageReturn("删除成功！");	
    }

  }
  PageReturn("参数错误！");     
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>超级用户管理</title>
<script>
function SaveUserInfo(frmButton){
  var formInputs=frmButton.parentNode.parentNode.getElementsByTagName('input');
  MyTestForm.userid.value=formInputs[0].value.trim();
  MyTestForm.username.value=formInputs[1].value.trim();
  MyTestForm.popedom.value=formInputs[2].value.trim();
  if(MyTestForm.username.value==""  || MyTestForm.popedom.value==""){
    alert("用户信息填写不完整！");
    return;
  }
  else{
    formInputs[0].checked=true;
    MyTestForm.submit();
  }
}
	
function DeleteAdmin(frmButton){
  if(confirm("确定要删除该用户？")){
    var userid=frmButton.parentNode.parentNode.getElementsByTagName('input')[0].value;
    MyTestForm.action="?mode=delete";
    MyTestForm.userid.value=userid;
    MyTestForm.submit();
  }  
}

function AddNewUser(frmButton){
   MyTestForm.action="?mode=add";
   SaveUserInfo(frmButton);
}
	
function ModifyUserInfo(frmButton){
  MyTestForm.action="?mode=modify";
  SaveUserInfo(frmButton);
}
 
function CheckPopedom(strPopedomID,bitIndex){
  var byteOffset=(bitIndex-1)>>2,bitOffset=(bitIndex-1)&0x03;
  var tagByte=strPopedomID.charCodeAt(byteOffset);
  if(tagByte>=65 && tagByte<=70)tagByte-=55;
  else if(tagByte>=48 && tagByte<=57)tagByte-=48;
  else return false;
  return (tagByte>>bitOffset)&0x01;
}
 
function SetPopedom(strPopedomID,bitIndex,bitValue){
  var byteOffset=(bitIndex-1)>>2,bitOffset=(bitIndex-1)&0x03;
  var tagByte=strPopedomID.charCodeAt(byteOffset);
  if(tagByte>=65)tagByte-=55;
  else if(tagByte>=48 && tagByte<=57)tagByte-=48;
  else return;
  if(bitValue)tagByte|=(1<<bitOffset);
  else tagByte&=(15-(1<<bitOffset));
  if(tagByte<10)tagByte=String.fromCharCode(tagByte+48);
  else tagByte=String.fromCharCode(tagByte+55) 	
  strPopedomID=strPopedomID.substring(0,byteOffset)+tagByte+strPopedomID.substring(byteOffset+1,strPopedomID.length);
  return strPopedomID; 
}

var objCurrentPopedom=null;
function RefreshPopedomDisplay(sender){
  var formInputs=sender.parentNode.parentNode.getElementsByTagName('input');
  var i,pid,popedomObj=formInputs[2];
  if(objCurrentPopedom!=popedomObj){
    objCurrentPopedom=popedomObj;
    formInputs[0].checked=true;
  }else return;
  //document.getElementById("userinfo").innerHTML="（"+formInputs[1].value+"＠"+formInputs[2].value+" &nbsp; 分组ID:"+formInputs[0].value+"）";
  for(i=0;i<PopedomDisplay.length;i++){
    pid=PopedomDisplay.elements[i].name;
    pid=parseInt(pid.substring(7,pid.length));
    PopedomDisplay.elements[i].checked=CheckPopedom(objCurrentPopedom.value,pid);
  }
}
	
function UpdateUserPopedom(objCheckBox){
  if(objCurrentPopedom){
    var strPopedomID=objCurrentPopedom.value;
    var pid=objCheckBox.name;
    pid=parseInt(pid.substring(7,pid.length));
    strPopedomID=SetPopedom(strPopedomID,pid,objCheckBox.checked);
    objCurrentPopedom.value=strPopedomID;
    return true;
  }
  else{
    alert("没有选择用户对象！");
    return false;
  }
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>超级用户管理</font></b></td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="20" bgcolor="#F7F7F7"><form method="post" action="?mode=modify"> 
       <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>&nbsp;</strong></td>
       <td WIDTH="25%" height="25" align="center" background="images/topbg.gif"><strong>用户名</strong></td>
       <td WIDTH="40%" height="25" align="center" background="images/topbg.gif"><strong>权限</strong></td>
       <td WIDTH="25%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
     </tr>
    <?php
    $res=$conn->query("select id,username,popedom from `mg_users` where popedom is not null order by username",PDO::FETCH_ASSOC);
    $row_inex=0;
    foreach($res as $row){?>
     <tr align="center" height="20" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this);"> 
       <td height="25"><input name="userid" type="radio" value="<?php echo $row["id"];?>" onclick="RefreshPopedomDisplay(this)" /></td>
       <td height="25"><input name="username" value="<?php echo $row["username"];?>" maxlength="16" class="input_text"></td>
       <td height="25"><input name="popedom"  maxlength="38" type="text" readOnly value="<?php echo $row["popedom"];?>" class="input_text"></td>
       <td height="25"><input type="button" value="保存" onclick="ModifyUserInfo(this)"> &nbsp; <input type="button" value="删除" onclick="DeleteAdmin(this)"></td>
     </tr>
     <?php
    }?> 
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
        <td height="25"><input name="userid" type="radio" value="0" onclick="RefreshPopedomDisplay(this)"></td>
        <td height="25"><input name="username"  maxlength="16" value="" class="input_text"</td>
        <td height="25"><input name="popedom" maxlength="38" type="text" readOnly  value="00000000000000000000000000000000" style="width:100%;text-align:center;border:0px;background-color:transparent" ></td>
        <td height="25"><input type="button" value="添加管理员" onclick="AddNewUser(this)"></td>
      </tr></form>		 
     </table>

  </td>
</tr>
</table>

<br><form method="post" name="PopedomDisplay"> 
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif" bgcolor="#F7F7F7"><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>用户操作权限设置</b></td>
  </tr>
  <tr> 
    <td height="60" bgcolor="#FFFFFF">
  	  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('SYSTEM');?>"  onclick="return UpdateUserPopedom(this)"><b>系统管理</b><img src="images/memo.gif" width=16 height=16 alt="后台系统重要参数设置"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('FINANCE');?>" onclick="return UpdateUserPopedom(this)"><b>财务管理</b><img src="images/memo.gif" width=16 height=16 alt="积分预存款、财务日志等删改"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('PRODUCT');?>" onclick="return UpdateUserPopedom(this)"><b>商品管理</b><img src="images/memo.gif" width=16 height=16 alt="商品信息添改"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('STOCK');?>"   onclick="return UpdateUserPopedom(this)"><b>库存管理</b><img src="images/memo.gif" width=16 height=16 alt="商品库存维护"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('INFOMATION');?>" onclick="return UpdateUserPopedom(this)"><b>信息管理</b><img src="images/memo.gif" width=16 height=16 alt="新闻、公告、留言、评论发布/编辑"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('MANAGE');?>"  onclick="return UpdateUserPopedom(this)"><b>人事管理</b><img src="images/memo.gif" width=16 height=16 alt="公司人事管理"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('ORDER');?>"   onclick="return UpdateUserPopedom(this)"><b>订单管理</b><img src="images/memo.gif" width=16 height=16 alt="订单管理"></td>
        <td>&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo PopedomIndex('SHARE');?>"    onclick="return UpdateUserPopedom(this)"><b>合作伙伴</b><img src="images/memo.gif" width=16 height=16 alt="合作伙伴"></td>
       </tr>
      </table>
  </td>
  </tr>
</table>
<br> 
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif" bgcolor="#F7F7F7" width="100%"><img src="images/pic5.gif" width="28" height="22" align="absmiddle"><b>用户菜单权限设置</b> &nbsp; <span id="userinfo" style="color:#FE0000"></span></td>
  </tr>
  <tr> 
    <td height="60" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC"><?php
    $res=$conn->query("select * from `mg_popedom` where parent=0 order by sort",PDO::FETCH_ASSOC);
    foreach($res as $row_menu){?>
    <tr bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
      <td width="33%">&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo $row_menu["id"];?>" onclick="return UpdateUserPopedom(this)"><b><?php echo $row_menu["title"];?></b></td>
    </tr><?php
    $res_sub=$conn->query("select * from `mg_popedom` where parent={$row_menu['id']} order by sort",PDO::FETCH_ASSOC);
    foreach($res_sub as $row_sub){?>
    <tr bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
      <td width="33%" style="padding-left:20px">&nbsp;&nbsp;<input type="checkbox" name="Popedom<?php echo $row_sub["id"];?>" onclick="return UpdateUserPopedom(this)"><?php echo $row_sub["title"];?>
      <?php if($row_sub["remark"]) echo "&nbsp;&nbsp;<img src=\"images/memo.gif\" width=16 height=16 alt=\"{$row_sub['remark']}\">";?>
      </td>
    </tr><?php
     }
    }
    CloseDB();
    ?>
    </table></form>
  </td>
  </tr>
</table>

<form name="MyTestForm" id="MyTestForm" method="post"><input type="hidden" name="userid"><input type="hidden" name="username"><input type="hidden" name="nickname"><input type="hidden" name="password"><input type="hidden" name="popedom"></form>
</body>
</html>
