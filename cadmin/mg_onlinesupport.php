<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$mode=@$_GET['mode'];
if($mode=='sysupdate'){
   $ObjFilePath=@$_SERVER['DOCUMENT_ROOT'].WEB_ROOT.'include/qqservice.js';
   $file_pattern='includes/qqservice_pat.js'; 
   $outtext=file_get_contents($file_pattern);
   if(empty($outtext))  pagereturn($file_pattern.' is empty');

   $row=$conn->query('select idnumber,qq from mg_admins where ordercoordinator order by idnumber',PDO::FETCH_ASSOC);
   $QQService='';
   $jishu=0;
   foreach($row as $row){
     $QQ_Arrays=explode(',',$row['qq']);
     $ArrayBound=count($QQ_Arrays);
     for($i=0;$i<$ArrayBound;$i++){
        $qqNumber=trim($QQ_Arrays[$i]);
        $QQNick=$row['idnumber']+$i;
        if($QQService) $QQService.=',';
        $QQService.="'$QQNick','$qqNumber'";
     }
   }

   $outtext=preg_replace('/var OurQQs=new Array\([^\)]+\)/', 'var OurQQs=new Array('.$QQService.')', $outtext); 

   if(file_put_contents($ObjFilePath,$outtext))PageReturn('更新完毕！');
   else PageReturn('写入失败!');
}
else if($mode=='modify'){
   $admin=FilterText(trim(@$_POST['admin']));
   $IDNumber=FilterText(trim(@$_POST['idnumber']));
   if(is_numeric($IDNumber) && $IDNumber>0){
      $IDNumber2=$IDNumber;
      $QQList=trim(@$_POST['qq']);
      if($QQList){
   	$QQ_Arrays=explode(',',$QQList);
        $ArrayBound=count($QQ_Arrays);
        for($i=0;$i<$ArrayBound;$i++){
          if(!is_numeric(trim($QQ_Arrays[$i]))){ 
              PageReturn('无效的QQ序列！');
          }
        }
        $IDNumber2+=$ArrayBound;
      }
      $row=$conn->query("select * from mg_admins where username='$admin'",PDO::FETCH_ASSOC)->fetch(); 
      if($row){
        if($row['idnumber']!=$IDNumber || $row['idnumber2']!=$IDNumber2){
       	  for($i=$IDNumber;$i<=$IDNumber2;$i++){
            $row2=$conn->query("select * from mg_admins where username<>'$admin' and idnumber<=$i and idnumber2>=".$i,PDO::FETCH_ASSOC)->fetch();
            if($row2)PageReturn('工号冲突！');
          }
        }
        $onduty=@$_GET['onduty'];
        if($onduty!='1')$onduty='0';
        if($conn->exec("update mg_admins set qq='$QQList',idnumber=$IDNumber,idnumber2=$IDNumber2,ordercoordinator=$onduty where username='$admin'")) PageReturn('操作成功！');
        else PageReturn('没有改变！');
      }
   }
   else PageReturn('工号必须是大于0的数字！');
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
.input_text{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
	text-decoration: none;
	font-size: 12px;
	width:100%;
	text-align:center;
	border: 0px solid #CCCCCC;
	background-color:transparent
}
-->
</style>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif">
  	<table width="100%" border=0 cellpadding="0" cellspacing="0">
  	<tr><td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>QQ客服管理</font></b></td>
  		  <td align="right"><input type="button" value="更新输出客服QQ" onclick="UpdateQQArray()"></td>
  	</tr>
    </table>
  </td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  <?php
    $res=$conn->query('select * from mg_admins where idverified  order by idnumber',PDO::FETCH_ASSOC);
    $row=$res->fetch();
    if(empty($row)){
      echo '<p align=center>没有设置QQ在线客服！</p>';
    }
    else {?>	
     <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="25" bgcolor="#F7F7F7"> 
       <td align="center"><?php
       $table='';
       do{
       	 if($row['qq']){
       	   $QQ_Arrays=explode(',',$row['qq']);
       	   $ArrayBound=count($QQ_Arrays);
           for($i=0;$i<$ArrayBound;$i++){
             $qqNumber=trim($QQ_Arrays[$i]);
             $QQNick=$row['idnumber']+$i;
             echo "<A title='QQ在线客服:$qqNumber' href='http://wpa.qq.com/msgrd?V=1&Uin=$qqNumber&Menu=yes' target='_blank'><IMG src='http://wpa.qq.com/pa?p=1:$qqNumber:4' border=0>$QQNick</A>&nbsp;"; 
           }
         }

         if($row['ordercoordinator']) $table.='<form  method="post">
       <tr height="25" align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
         <td><input name="idnumber"  maxlength="32" type="text" value="'.$row['idnumber'].'" class="input_text"></td>
         <td><input name="admin" readOnly value="'.$row['username'].'" class="input_text"></td>
         <td><input name="qq" maxlength="80"  type="text" value="'.$row['qq'].'" class="input_text"></td>
         <td><input type="button" value="修改" onclick="ModifyUserInfo(true,this.form)"> &nbsp; <input type="button" value="下台" onclick="ModifyUserInfo(false,this.form)"></td>
       </tr></form>';
       }while(($row=$res->fetch()));?>
       </td>
     </tr>
     </table>
     <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr align="center"  height="25" height="20" bgcolor="#F7F7F7"> 
       <td WIDTH="20%" background="images/topbg.gif"><strong>工号</strong></td>
       <td WIDTH="20%" background="images/topbg.gif"><strong>用户名</strong></td>
       <td WIDTH="35%" background="images/topbg.gif"><strong>QQ号</strong></td>
       <td WIDTH="25%" background="images/topbg.gif"><strong>操作</strong></td>
     </tr><?php
       echo $table.'</table>';
     }?>
  </td>
</tr>
</table>

<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif" bgcolor="#F7F7F7"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>候补QQ客服</font></b></td>
  </tr>
  <tr> 
    <td height="60" bgcolor="#FFFFFF"> 
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr align="center" height="20" bgcolor="#F7F7F7"> 
       <td WIDTH="20%" background="images/topbg.gif"><strong>工号</strong></td>
       <td WIDTH="20%" background="images/topbg.gif"><strong>用户名</strong></td>
       <td WIDTH="35%" background="images/topbg.gif"><strong>QQ号</strong></td>
       <td WIDTH="25%" background="images/topbg.gif"><strong>操作</strong></td>
      </tr><?php
$res=$conn->query('select * from mg_admins where idverified and not ordercoordinator order by idnUmber',PDO::FETCH_ASSOC);
foreach($res as $row){?>
  <form method="post">
  <tr height="25" align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
     <td><input name="idnumber" maxlength="32" type="text" value="<?php echo $row['idnumber'];?>" style="width:100%;text-align:center;border:0px;background-color:transparent"></td>
     <td><input name="admin" readOnly value="<?php echo $row['username'];?>" style="width:100%;text-align:center;border:0px;background-color:transparent"></td>
     <td><input name="qq" maxlength="80"  type="text" value="<?php echo $row['qq'];?>" style="width:100%;text-align:center;border:0px;background-color:transparent"></td>
     <td><input type="button" value="修改" onclick="ModifyUserInfo(false,this.form)"> &nbsp; <input type="button" value="上台" onclick="ModifyUserInfo(true,this.form)"></td>
   </tr></form><?php
}?>   
  </table>
  </td>
  </tr>
</table>
<br>
<table width="100%" id="MyPageBottom" align="center" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
  <td width="100%"><span><div></div></span><br><input type="button" value="刷新显示" onclick="self.location.reload();"></td>
</tr>
</table>
<form name="MyTestForm" id="MyTestForm" method="post" style="display:none"><input type="hidden" name="newValue"></form>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/qqservice.js" type="text/javascript"></SCRIPT> 
<script language=javascript>
function UpdateQQArray(){
  MyTestForm.action="?mode=sysupdate";
  MyTestForm.submit();
} 
function  ModifyUserInfo(onduty,myform){
  myform.action="?mode=modify&onduty="+((onduty)?"1":"0");
  myform.submit();
}
</script>
</body>
</html><?php
db_close();?>
