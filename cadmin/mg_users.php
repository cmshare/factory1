<?php require('includes/dbconn.php');

 CheckLogin();
 
 OpenDB();

 $UserTitles=array();

 $res=$conn->query('select id,title from mg_usrgrade',PDO::FETCH_NUM);
 foreach($res as $row){
  $UserTitles[$row[0]]=$row[1];
 }?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif">
           	 <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="?"><font color=#FF0000>会员信息查询</font></a></b>
    </td>
  </tr>
  <tr> 
    <form  method="post">
      <td height="200" valign="top" bgcolor="#FFFFFF"><?php
$keyvalue=FilterText(trim(@$_GET['kv']));
if(empty($keyvalue)){
  echo ' &nbsp; <b><font color="#FF6600">最近一周注册的新用户：</font></b><br>';
  $keyname='';
  $sql_where='where addtime>unix_timestamp()-700*24*60*60 and grade>0 ';
}
else{
  $keyname=FilterText(trim($_GET['kn']));
  if($keyname=='username') echo '&nbsp; <b>您搜索的关键词是</b>【用户名】<b><font color="#FF0000">'.$keyvalue.'</font></b><br>';
  else if($keyname=='realname')echo '&nbsp; <b>您搜索的关键词是</b>【真实姓名】<font color="#FF0000">'.$keyvalue.'</font><br>';
  else echo '&nbsp; <b>您搜索的关键词是</b>【VIP卡号】<font color="#FF0000">'.$keyvalue.'</font><br>';
  if($keyname=='realname' || $keyname=='username'){
    if(strlen($keyvalue)<2) PageReturn('&nbsp; <b><font color=""#FF0000"">查询的关键词太短！</font></b>',1);
    $sql_where='where '.$keyname.' like \'%'.$keyvalue.'%\' and grade>0';
  }
  else if($keyname=='vipno' && is_numeric($keyvalue)){
    $sql_where='where vipno='.$keyvalue.' and grade>0';
  }
  else{
    PageReturn('&nbsp; <b><font color=""#FF0000"">参数无效！</font></b>',1);
  }
}
$res=page_query('select id,username,realname,grade,addtime','from mg_users',$sql_where,'order by addtime desc',20);
if(empty($res)) echo '<p align=center> 对不起，找不到相关记录！</p>';
else {?>
          <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr height="20" bgcolor="#F7F7F7"> 
            <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('userid',this.checked)" /></td>
            <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>会员名</strong></td>
            <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>真实姓名</strong></td>
            <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>会员级别</strong></td>
            <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>购物车</strong></td>
            <td WIDTH="30%" height="25" align="center" background="images/topbg.gif"><strong>注册时间</strong></td>
          </tr><?php
foreach($res as $row){
   $amount=$conn->query('select sum(amount) from mg_favorites where userid='.$row['id'].' and state>1')->fetchColumn(0);?>
          <tr height="25" align="center" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
            <td><input name="userid" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>
            <td><a href="mg_usrinfo.php?id=<?php echo $row['id'];?>"><?php echo $row['username'];?></a></td>
            <td><?php echo $row['realname'];?></td>
            <td><span style="cursor:pointer" title="点击修改" onclick="ChangeUserGrade(<?php echo $row['id'];?>)"><u><?php echo $UserTitles[$row['grade']];?></u></span></td>
            <td><a href="mg_checkcart.php?id=<?php echo $row['id'];?>"><?php
            if(empty($amount)) echo '<img src="images/cart_empty.gif" width=16 height=16 border=0 align="absMiddle" alt="购物车为空">';
            else echo '<img src="images/icon_buy.gif" width=16 height=16 border=0 align="absMiddle" alt="该购物车上有'.$amount.'件商品">';?></a></td>
            <td height="25"><?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
          </tr><?php
}?>
         <tr bgcolor="#FFFFFF">
          <td colspan="6" align="center"><script language="javascript">GeneratePageGuider("kv=<?php echo $keyvalue;?>&kn=<?php echo $keyname;?>",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);</script></td></tr>
      </table><?php
}?>
      </td>
    </form>
  </tr>
</table>
<script>
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
</script>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td align="center" bgcolor="#FFFFFF"><form method="get" style="margin:0px">
     按<select name="kn" ><option value="username">会 员 名</option><option <?php if($keyname=='realname') echo 'selected';?> value="realname">真实姓名</option><option <?php if($keyname=='vipno') echo 'selected';?> value="vipno">会员卡号</option></select>查找: <input name="kv" type="text" size="16">
     <input type="submit" class="input_bot" value="查 询"></form></td>
  </tr>
</table>
</body>
</html><?php
CloseDB();?>
