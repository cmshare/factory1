<?php require('includes/dbconn.php');


CheckLogin();
db_open();

require('config_usergrade.php');

$mode=@$_GET['mode'];

if($mode){
  switch($mode){
    case 'save':do_save();break;
    case 'checkgrade':checkgrade();break;
    case 'restraintobigclient':restraintobigclient();break;
  }
  db_close();
  exit(0);
}

function do_save(){
 global $conn;
 $UserID=@$_POST['userid'];
 $NewGrade=@$_POST['grade'];
 $NewLockgrade=@$_POST['lockgrade'];
 $ret_msg='';
 if(is_numeric($UserID) && $UserID>0 && is_numeric($NewGrade) && $NewGrade>0){
   $row=$conn->query('select property,grade from mg_users where id='.$UserID,PDO::FETCH_ASSOC)->fetch();
   if($row){
     $OriginGrade=$row['grade'];
     $property=$row['property'];
     $UserGradeLocked=($property&(1<<UP_LOCKGRADE))?'1':'0';
     if(($NewLockgrade==='0' || $NewLockgrade==='1') && CheckPopedom('MANAGE')){
       if($UserGradeLocked!=$NewLockgrade){
         if($NewLockgrade)$property|=(1<<UP_LOCKGRADE);
         else  $property-=(1<<UP_LOCKGRADE);
         if($conn->exec('update mg_users set property='.$property.' where id='.$UserID)){
 	   if($NewLockgrade)$ret_msg='会员等级成功锁定！\n';
           else $ret_msg='会员等级成功解锁！\n';
           $UserGradeLocked=$NewLockgrade;
         }
       }
     }
     if($NewGrade!=$OriginGrade){
       if($UserGradeLocked) $ret_msg.='会员等级被锁无法修改！';
       else{
         if($NewGrade>=4) $user_allow_grade=UserPermitGrade($UserID);
         else $user_allow_grade=$NewGrade; 	
         if($NewGrade<=$user_allow_grade){ 
           if(ChangeUserGrade($UserID,$NewGrade)) $ret_msg.='会员等级修改成功！';
         }
         else $ret_msg.='升级条件不足！';
       }
     }
   }
 }
 echo '<script>parent.window.closeDialog("'.$ret_msg.'");</script>';	
}


function checkgrade(){
  $UserID=@$_POST['userid'];
  $UserGrade=@$_POST['grade'];
  if(is_numeric($UserID) && $UserID>0 && is_numeric($UserGrade) && $UserGrade>0){
     $user_allow_grade=UserPermitGrade($UserID);
     if($UserGrade!=$user_allow_grade) echo ChangeUserGrade($UserID,$user_allow_grade);
  }
}

function restraintobigclient(){
  global $conn;
  $UserName=FilterText(trim($_POST['username']));
  if($UserName){
    if(CheckPopedom('MANAGE')){
      $row=$conn->query('select id,grade from mg_users where username=\''.$UserName.'\'',PDO::FETCH_ASSOC)->fetch();
      if($row){
 	  if($row['grade']==4) echo '['.$UserName.']已经是大客户了,无须升级！';
          else if(ChangeUserGrade($row['id'],4)) echo '['.$UserName.']已经强制升级为大客户！';
          else echo '升级失败';
      }else echo '此用户名不存在！'; 
    }else echo '权限错误';
  }else echo '参数无效';
}

$UserGrade=0;
$UserID=@$_GET['userid'];
if(is_numeric($UserID) && $UserID>0){ 
   $row=$conn->query('select username,grade,property from mg_users where id='.$UserID,PDO::FETCH_NUM)->fetch();
   if($row){
     $UserName=$row[0];
     $UserGrade=$row[1]; 
     $UserGradeLocked=$row[2]&(1<<UP_LOCKGRADE);
   }
}
if($UserGrade==0){
  echo '<p align=center>参数错误</p>';
  db_close();
  exit(0);
}
$own_popedomManage=CheckPopedom('MANAGE');?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>会员等级管理</title>
</head>
<body leftmargin="0" topmargin="0" onload="document.forms[0].CloseBtn.focus();">
<script language="javascript">
function ChangeUserGrade(myform){
  var i,usergrade=0,lockchanged=false;
  var gradecount=myform.grade.length;
  for(i=0;i<gradecount;i++){
    if(myform.grade[i].checked)usergrade=myform.grade[i].value;
    else myform.grade[i].disabled=true;
  }
  if(myform.dsplockgrade.checked!=<?php echo ($UserGradeLocked)?'true':'false';?>){
    myform.lockgrade.value=(myform.dsplockgrade.checked)?"1":"0";
    lockchanged=true;
  }
  else myform.lockgrade.value="";
  if(usergrade>0 && (usergrade!=<?php echo $UserGrade;?> || lockchanged)){
    var obj=document.getElementById("ControlPanel");
    if(obj)obj.innerHTML="正在处理，请稍候...";
    else myform.confirmbutton.disabled=true;
    myform.submit();
  }else myform.CloseBtn.click();
}
</script>
<form method="post" action="?mode=save" targets="dummyframe" style="margin:0px">
<table width="100%" height="100%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
<tr>
  <td height="25" width="20%" align="center" bgcolor="#F7F7F7" background="images/topbg.gif"><strong>会员名称</strong></td>
  <td height="25" width="60%" bgcolor="#FFFFFF">&nbsp; <input type="hidden" name="userid" value="<?php echo $UserID;?>"><?php echo $UserName;?></td>
  <td height="25" width="20%" bgcolor="#FFFFFF" nowrap align="center"><input type="hidden" name="lockgrade" value=""><input type="checkbox" name="dsplockgrade" <?php
    if($UserGradeLocked) echo 'checked ';
    if(!$own_popedomManage) echo 'disabled ';?>><b><font color="#FF0000">等级锁定</font></b></td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7" background="images/topbg.gif"><strong>会员等级</strong></td>
  <td height="25" bgcolor="#FFFFFF" colspan="3" ><?php
  $res=$conn->query('select * from mg_usrgrade order by id asc',PDO::FETCH_ASSOC); 
  foreach($res as $row){
     echo '<input type="radio" value="'.$row['id'].'" name="grade" ';
     if($row['id']==$UserGrade) echo 'checked ';
     echo '>'.$row['title'].'&nbsp; ';    
  }?></td>
</tr>
<tr>
  <td id="ControlPanel" height="30" colspan="3" align="center" bgcolor="#F7F7F7" background="images/topbg.gif">
    <input name="confirmbutton" type="button"  value=" 确定 " <?php if(!$own_popedomManage && $UserGradeLocked) echo 'disabled';?> onclick="ChangeUserGrade(this.form)"> &nbsp; 
    <input name="CloseBtn" type="button" value=" 取消 " onclick="parent.window.closeDialog()">
  </td>
</tr>
</table></form>
<iframe name="dummyframe" style="width:100%; height:1px;" scrolling="no" Frameborder="no" marginwidth=0 marginheight=0></iframe>   
</body>
</html><?php
db_close();?>
