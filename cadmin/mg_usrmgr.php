<?php require('includes/dbconn.php');
 CheckLogin();
 OpenDB();
 CheckMenu('客户资料管理');

 if(@$_GET['mode']=='delete'){
    $selectid=@$_POST['userid'];
    if(empty($selectid)) PageReturn("没有选择操作对象！",-1);
    else{
      $idlist=implode(',',$selectid);
      $conn->exec('update mg_favorites set state=0 where userid in ('.$idlist.')'); 
      $conn->exec('update mg_users set grade=0 where id in ('.$idlist.')'); 
      PageReturn("所选用户删除成功！");
    }
 }
 
 $grade=@$_GET['grade'];
 if(!is_numeric($grade)) $grade=0;  
 $sort_name=@$_COOKIE['sort_name'];
 if($sort_name!='deposit' && $sort_name!='score' && $sort_name!='logincount' && $sort_name!='lastlogin')$sort_name='addtime';
 $sort_order=@$_COOKIE['sort_order'];
 if($sort_order!='asc') $sort_order='desc';

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
    <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="?"><font color=#FF0000>客户管理</font></a></b> -&gt; <select name="select" onchange="var jmpURL=this.options[this.selectedIndex].value ; if(jmpURL!='') {window.location=jmpURL;} else {this.selectedIndex=0 ;}" >
              <option value="mg_usrmgr.php" >所有会员</option><base target=Right><?php
$MaxUserGrade=count($UserTitles);
for($i=1;$i<$MaxUserGrade;$i++){
  $selectionCode=($i==$grade)?'selected':'';
  echo '<option value="mg_usrmgr.php?grade='.$i.'" '.$selectionCode.'>'.$UserTitles[$i].'</option>';
}?></select>
    </td>
  </tr>
  <tr> 
    <td align="center" valign="top" bgcolor="#FFFFFF"><form  method="post" style="margin:0px"><?php
 $keyvalue=FilterText(trim(@$_GET['kv']));
 if($keyvalue){
   $keyname=FilterText(trim(@$_GET['kn']));
   if($keyname=='username')$CN_Keyname='用户名';
   else if($keyname=='realname')$CN_Keyname='真实性名';
   else if($keyname=='vipno')$CN_Keyname='会员卡号';
   else goto label_no_key;
   if(@$_GET['blur']=='1' && $keyname!='vipno'){
     $blursearch=1;
     $CN_BlurSearch='模糊';
     $where='where '.$keyname.' like \'%'.$keyvalue.'%\''; 
   }
   else{
     $blursearch=0;
     $CN_BlurSearch='精确';
     $where='where '.$keyname.'=\''.$keyvalue.'\'';
   }
   echo '<b>根据<font color="#FF6600">'.$CN_Keyname.'</font>搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
 }
 else{
    label_no_key:
    $keyname='';
    $blursearch=1;
    if($grade==0)$where='';
    else $where='where grade='.$grade;
 }?>
  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr height="20" bgcolor="#F7F7F7"> 
     <td WIDTH="5%" height="25" align="center" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('userid[]',this.checked)" /></td>
     <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>会员名</strong></td>
     <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>真实姓名</strong></td>
     <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>会员级别</strong></td>
     <td WIDTH="7%" height="25" align="center" background="images/topbg.gif" title="点击排序" onclick="TableResort('score')" style='cursor:pointer'><strong>积分</strong><?php if($sort_name=='score') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td WIDTH="7%" height="25" align="center" background="images/topbg.gif" title="点击排序" onclick="TableResort('deposit')" style='cursor:pointer'><strong>预存款</strong><?php if($sort_name=='deposit') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td WIDTH="6%" height="25" align="center" background="images/topbg.gif"><strong> 购物车</strong></td>
     <td WIDTH="6%" height="25" align="center" background="images/topbg.gif" title="点击排序" onclick="TableResort('logincount')" style='cursor:pointer'><strong>登录次数</strong><?php if($sort_name=='logincount') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td WIDTH="15%" height="25" align="center" background="images/topbg.gif" title="点击排序" onclick="TableResort('lastlogin')" style='cursor:pointer'><strong>最后登录</strong><?php if($sort_name=='lastlogin') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
     <td WIDTH="15%" height="25" align="center" background="images/topbg.gif" title="点击排序" onclick="TableResort('addtime')" style='cursor:pointer'><strong>注册时间</strong><?php if($sort_name=='addtime') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
   </tr><?php
    $orderby="order by $sort_name $sort_order";
    $res=page_query('select id,username,realname,grade,score,deposit,logincount,lastlogin,addtime','from mg_users',$where,$orderby,20);
    if(empty($res)) echo '<tr bgcolor="#FFFFFF"><td colspan="10" align="center"> 对不起，找不到相关记录！</td></tr>';
    else{
      foreach($res as $row){
         $amount=$conn->query('select sum(amount) from mg_favorites where userid='.$row['id'].' and state>1')->fetchColumn(0);?>
  <tr height="25" align="center" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
     <td><input name="userid[]" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>
     <td><a href="mg_usrinfo.php?id=<?php echo $row['id'];?>"><?php echo $row['username'];?></a></td>
     <td><?php echo $row['realname'];?></td>
     <td><?php
        if($row['grade']>0) echo '<span style="cursor:pointer" title="点击修改" onclick="ChangeUserGrade('.$row['id'].')"><u>'.$UserTitles[$row['grade']].'</u></span>';
        else echo '<font color=#FF0000>删除中...</font>';?></td>
     <td><?php echo $row['score'];?></td>
     <td><?php echo round($row['deposit'],2);?></td>
     <td><a href="mg_checkcart.php?id=<?php echo $row['id'];?>"><?php
       if(empty($amount))echo'<img src="images/cart_empty.gif" width=16 height=16 border=0 align="absMiddle" alt="购物车为空">';
       else echo '<img src="images/icon_buy.gif" width=16 height=16 border=0 align="absMiddle" alt="该购物车上有'.$amount.'件商品">';?></a></td>
     <td height="25"><?php echo $row['logincount'];?>次</td>
     <td height="25"><?php echo date('Y-m-d H:i',$row['lastlogin']);?></td>
     <td height="25"><?php echo date('Y-m-d H:i',$row['addtime']);?></td>
  </tr><?php
     }
     echo '<tr bgcolor="#FFFFFF"><td  colspan="2" align="center">&nbsp;<input type="button" value="发送信息" onclick="BatchSendMessage()">';
     if(CheckPopedom('SYSTEM')) echo '<input type="button" onClick="BatchDeleteUser(this.form)" value="删除所选用户" >';
     echo '</td><td colspan="8" align="center"><script language="javascript">GeneratePageGuider("kv='.$keyvalue.'&kn='.$keyname.'&grade='.$grade.'&blur='.$blursearch.'",'.$total_records.','.$page.','.$total_pages.');</script></td></tr>';
   }?>
      </table></form>
      </td>
  </tr>
</table>
<script>
function TableResort(sort_name)
{	if(sort_name=="<?php echo $sort_name;?>")
	{ if(getCookie("sort_order")=="asc")
		  setCookie("sort_order","desc")
		else
		  setCookie("sort_order","asc")  
	}
	else
	{ setCookie("sort_name",sort_name)
		setCookie("sort_order","desc")
	}
	self.location.reload();
}

function BatchSendMessage()
{ var selcount= Checkbox_SelectedCount("userid[]");
	if(selcount>0)
	{  var a = document.getElementsByName("userid");
	   var selections="";
     for (var i=0; i<a.length; i++)
     { if (a[i].type == "checkbox" && a[i].checked)
       { if(!selections)selections=a[i].parentNode.parentNode.cells[1].innerText;
       	 else selections=selections+","+ a[i].parentNode.parentNode.cells[1].innerText;
       }
     }
     self.location.href="usermesr.php?action=new&sendto="+selections+"&mode=2";
	}
  else
  { alert("没有选择操作对象！");
  }
}
function BatchDeleteUser(myform)
{ var selcount= Checkbox_SelectedCount("userid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else if(confirm("确定要将所选的"+selcount+"个用户删除吗？")){
    myform.action = "?mode=delete";
    myform.submit();
  }
}

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
    按<select name="kn" ><option value="username">会 员 名</option><option <?php if($keyname=='realname') echo 'selected';?> value="realname">真实姓名</option><option <?php if($keyname=='vipno') echo 'selected';?> value="vipno">会员卡号</option></select><input name="kv" type="text" size="16"> &nbsp; <input name="blur" type="checkbox" value="1"<?php if($blursearch)echo' checked';?>>模糊查询 <input type="submit" value="查 询"></form></td>
</tr>
</table>
</body>
</html><?php
CloseDB();?>
