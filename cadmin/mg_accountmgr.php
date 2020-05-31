<?php require('includes/dbconn.php');
CheckLogin();
db_open();
CheckMenu('财务权限管理');

$mode=@$_POST['mode'];
if($mode=='arrearage'){
   session_start();
   $_SESSION['arrearage']=!@$_SESSION['arrearage'];
   PageReturn('操作成功！');
}
else if($mode=='SwitchAccountant'){
  $row=$conn->query('select accountant,accountantdate,accountantheir,accountantheirdate from mg_configs',PDO::FETCH_ASSOC)->fetch();
  if($row['accountant']==$AdminUsername){ 
    $conn->exec('update mg_configs set accountant=null');
    $popedom=$conn->query('select popedom from mg_users where username=\''.$AdminUsername.'\'')->fetchColumn(0);
    $popedom=ConfigPopedom('FINANCE',0,$popedom);
    $conn->exec('update mg_users set popedom=\''.$popedom.'\' where username=\''.$AdminUsername.'\'');
  }
  else if($row['accountantheir']!=$AdminUsername){
    $AccountantHeirDate=(empty($row['accountant'])|| CheckPopedom('MANAGE'))?time()+60:time()+30*60;
    $conn->exec('update mg_configs set accountantheir=\''.$AdminUsername.'\',accountantheirdate='.$AccountantHeirDate);
  }
  PageReturn('操作成功！,请重新登录',-2);
}
else if($mode=='GetPopedom'){
  $row=$conn->query('select accountant,accountantdate,accountantheir,accountantheirdate from mg_configs',PDO::FETCH_ASSOC)->fetch();
  if($row['accountant']!=$AdminUsername && $row['accountantheir']==$AdminUsername && $row['accountantheirdate']<time()){
    $LastAccountant=$row['accountant'];
    $conn->exec('update mg_configs set accountant=\''.$AdminUsername.'\', accountantheir=null,accountantdate=unix_timestamp()');
    if($LastAccountant){
      $popedom=$conn->query('select popedom from mg_users where username=\''.$LastAccountant.'\'')->fetchColumn(0);
      $popedom=ConfigPopedom('FINANCE',0,$popedom);
      $conn->exec('update mg_uers set popedom=\''.$popedom.'\' where username=\''.$LastAccountant.'\'');
    }
    $popedom=$conn->query('select popedom from mg_users where username=\''.$AdminUsername.'\'')->fetchColumn(0);
    $popedom=ConfigPopedom('FINANCE',1,$popedom);
    if($conn->exec('update mg_users set popedom=\''.$popedom.'\' where username=\''.$AdminUsername.'\'')){
      PageReturn('操作成功！,请重新登录',-2);
    }
 
  }
  PageReturn("未知错误！");
}

function ConfigPopedom($index,$value,$popedoms){
  if(!is_numeric($index))$index=PopedomIndex($index);
  if($index>0 && $popedoms && is_string($popedoms)){
     $index-=1;
     $byteOffset=$index>>2;
     $bitOffset=$index&0x03;
     $d=ord($popedoms[$byteOffset]);
     if($d>=ord('A')&& $d<=ord('F'))$d=$d-ord('A')+10;
     else if($d>=ord('0') && $d<=ord('9'))$d=$d-ord('0');
     else return FALSE;
     if(($d>>$bitOffset)&0x01){
       if($value)return $popedoms;
       else $d-=(1<<$bitOffset);
     }
     else{
       if($value) $d|=(1<<$bitOffset);
       else return $popedoms;
     }
     $popedoms[$byteOffset]=chr(($d>=10)?ord('A')+$d-10:ord('0')+$d);
     return $popedoms;
  }
  return FALSE;
}

$row=$conn->query('select accountant,accountantdate,accountantheir,accountantheirdate from mg_configs',PDO::FETCH_ASSOC)->fetch();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>

</head>
<body topmargin="0" leftmargin="0">
<script>
function SwitchArrearage()
{ MyTestForm.mode.value="arrearage";
	MyTestForm.action="?";
	MyTestForm.submit();
}	
function SwitchAccountant()
{ MyTestForm.mode.value="SwitchAccountant";
	MyTestForm.action="?";
	MyTestForm.submit();
}	
<?php if($row['accountantheir'] && $row['accountantheir']!=$row['accountant']){?>
var remains=<?php echo $row['accountantheirdate']-time();?>;
var seconds=remains % 60;
remains=Math.floor(remains/60);
var Kill_ID=setInterval("UpdateTimeDisplay();",1000);
function UpdateTimeDisplay()
{var timeText;
 if(seconds>0)seconds--;
 else if(remains>0)
 {remains--;
 	seconds=59;
 }
 else
 { <?php if($row['accountantheir']==$AdminUsername){?>
   var btn=document.getElementById("BtnGetPopedom");
 	 if(btn)
 	 { btn.disabled=false;
 	   btn.value="完成";
   }<?php
   }?>
   remains=seconds=0;
   clearInterval(Kill_ID);
 }
 if(remains<10)timeText="0"+remains;
 else timeText=remains; 
 if(seconds<10)timeText=timeText+":0"+seconds;
 else timeText=timeText+":"+seconds;
 timeLabel.innerHTML=timeText; 
}

function GetPopedom()
{ MyTestForm.mode.value="GetPopedom";
	MyTestForm.action="?";
	MyTestForm.submit();
}<?php
}?>

</script>
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif" height=22><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>财务权限管理</font></b></td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF"><strong>【当前财务管理员】</strong><br><br> &nbsp;● <?php
    if(empty($row['accountant'])) echo '<font color="#FF0000">空缺</font>';
    else echo '<font color="#FF0000">'.$row['accountant'].'</font>&nbsp; &nbsp; 仲裁时间：<font color=#000088>'.date('Y-m-d H:i:s',$row['accountantdate']).'</font>&nbsp; &nbsp; &nbsp; &nbsp; ';
    if($row['accountant']==$AdminUsername) echo '&nbsp; &nbsp; <input type="button" value="弃权" onclick="SwitchAccountant()">';
    else if($row['accountantheir']!=$AdminUsername)echo '&nbsp; &nbsp; <input type="button" value="抢占" onclick="SwitchAccountant()">'; ?>
  </td>
</tr>
<?php if($row['accountantheir']){?>
<tr> 
  <td valign="top" bgcolor="#FFFFFF" nowrap >
   正在申请的财务管理员：<font color="#FF0000"><?php echo $row['accountantheir'];?></font>
    &nbsp; &nbsp; 倒计时 <span id="timeLabel" style="color:#008800">##:##</span>&nbsp; &nbsp; &nbsp; <input type="button" value="仲裁中..." disabled id="BtnGetPopedom" onclick="GetPopedom()">
  </td>
</tr><?php
}

if($row['accountant']==$AdminUsername){
  session_start();?>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
    <strong>【财务功能相关设置】</strong><br><br>
    &nbsp;● 客户欠费额度限制：<input type="button" onclick="SwitchArrearage()" value=" <?php echo (@$_SESSION['arrearage'])?'无':'有';?>限额 " style="color:#FF0000">
  </td>
</tr><?php
}?>
</table>
<form name="MyTestForm" id="MyTestForm" method="post"><input type="hidden" name="mode"><input type="hidden" name="newValue"></form>
</body>
</html><?php
db_close();?>
