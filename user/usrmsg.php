<?php require('../include/conn.php');

if(!CheckLogin(0)){
  echo '<br><br><br><p align="center">请先登录</p><br><br><br>';
  exit(0);
} 

db_open();

$action=@$_GET['action'];
if($action=='delete'){
  $msgid=$_POST['id'];
  if(is_numeric($msgid) && $msgid>0){
     $row=$conn->query('select * from `mg_message` where id='.$msgid.' and property>0',PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($row['sendto']==$LoginUserName || $row['sendfrom']==$LoginUserName){
          $conn->exec('update `mg_message` set property=0 where id='.$msgid);
       }
       else{
         $msg_del_list=$_COOKIE[$LoginUserName]['msgdelete'];
         if(empty($msg_del_list)) $msg_del_list=$msgid;
         else if(strpos(','.$msg_del_list.',',','.$msgid.',')===FALSE) $msg_del_list.=','.$msgid;
         echo $msg_del_list;
         setcookie($LoginUserName.'[msgdelete]',$msg_del_list);
       }
       echo 'OK'; 
     } 
  } 
  db_close();
  exit(0);
}
else if($action=='new'){
  $msg_content=FilterText(rtrim($_POST['content']));
  if($msg_content){
    $sql="mg_message set title=null,content='$msg_content',sendfrom='$LoginUserName',sendto='adm',reply=null,property=1,addtime=unix_timestamp()";
    if($conn->exec('update '.$sql.' where property=0 limit 1') || $conn->exec('insert into '.$sql))PageReturn('留言发表成功，请耐心等待管理员回复！');
  }
  PageReturn('有错误发生！'.$sql);
}

echo '<center><img src="'.WEB_ROOT.'images/kubars/kubar_message.gif" width="778" height="40"></center>';
$page_size=8; ###每页显示条数
$msg_read_list=FilterText(@$_COOKIE[$LoginUserName]['msgread']);
$msg_del_list=FilterText(@$_COOKIE[$LoginUserName]['msgdelete']);
if($msg_del_list) $sql_count="(sendto='all' and id not in ($msg_del_list))";
else $sql_count="sendto='all'";
$sql_count="from `mg_message` where (sendfrom='$LoginUserName' or sendto='$LoginUserName' or $sql_count) and property>0"; 
$total_records=$conn->query('select count(*) '.$sql_count,PDO::FETCH_NUM)->fetchColumn(0); 
if(empty($total_records)){
  echo '<p align="center">没有消息!</p>';
}
else{
  ob_start(); 
  $sql_query="select * $sql_count order by addtime desc";
  $total_pages=(int)(($total_records+$page_size-1)/$page_size);
  $page=$_GET['page'];
  if(is_numeric($page)){
    if($page<1)$page=1;
    else if($page>$total_pages)$page=$total_pages;
  }else $page=1;
  $res=$conn->query($sql_query." limit ".($page_size*($page-1)).",$page_size",PDO::FETCH_ASSOC); 
  foreach($res as $row){
    if($row['sendto']==$LoginUserName) $msgDisposed=($row['property']==2);
    else if($row['sendto']=='all') $msgDisposed = (strpos(','.$msg_read_list.',',','.$row['id'].',')!==FALSE);
    else $msgDisposed=TRUE;?>		 
<table align="center" cellSpacing="0" cellPadding="0" width=" 775" style="background-color: #f8f9f9; border: 1px solid #b4c9e7;padding-left:10px;margin-bottom:10px">
  <tr>
    <td height="25" colspan="2" width="100%">
      <table cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
      <tr>
      	<td width="5%" nowrap><?php echo ($row['sendfrom']==$LoginUserName)?'<b>我的留言:</b>':'<b>系统消息:</b>';?></td>
        <td width="70%" style="FILTER: glow(Color=yellow,Strength=3);<?php if(!$msgDisposed) echo 'font-weight:bold;';?>" >&nbsp;<?php echo $row['title'];?></td>
        <td width="25%" nowrap align="right">
          <img src="<?php echo WEB_ROOT;?>images/posttime.gif" width="11" height="11" align="absMiddle">
          <font color="#006633">：<?php echo date('Y-m-d H:i:s',$row['addtime']);?></font>&nbsp;
          <a href="javascript:msg_delete(<?php echo $row['id'];?>)"><img src="<?php echo WEB_ROOT;?>images/del.gif" width="45" height="16" border="0" align="absMiddle"></a>
        </td>
      </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="100%"  style="margin-left:11px;margin-right:9px;word-wrap:break-word;word-break:break-all;" ><?php
    echo $row['content'];

    if($row['sendfrom']==$LoginUserName){
      echo '<p><img border="0" src="book/img/dot.gif" WIDTH="10" HEIGHT="10">&nbsp;<font color="#FF8000"><u><b>管理员回复</b></u>：</font>';
      if($row['reply']) echo '<br>'.$row['reply'];
      else echo '等待回复中...';
    }?>
    </td>
  </tr>
</table><?php
     if($row['sendto']==$LoginUserName){
       if($row['property']==1)$conn->exec('update `mg_message` set property=2 where id='.$row['id']); 
     }
     else if($row['sendto']=='all'){
       if (strpos(','.$msg_read_list.',',','.$row['id'].',')===FALSE){ 

         echo '<hr>';
      	 if($msg_read_list) $msg_read_list.=','.$row['id'];
         else $msg_read_list=$row['id'];
         echo $msg_read_list;
  	 setcookie($LoginUserName.'[msgread]',$msg_read_list);
       }	
     }
  } 
		  
		  
  #重新计算未读消息的数量，并更新到本地cookie中去:
  $msg_filter=$msg_read_list.$msg_del_list;
  if($msg_filter)$msg_sql="(sendto='all' and id not in ($msg_filter))";
  else $msg_sql="sendto='all'";
  $msg_sql="select count(*)  from `mg_message` where property=1 and (sendto='$LoginUserName' or $msg_sql)";
  $GetUnreadMsgCount=$conn->query($msg_sql)->fetchColumn(0);
  if($GetUnreadMsgCount===FALSE)$GetUnreadMsgCount=0;
  setcookie("cmshop[unreadmsg]",$GetUnreadMsgCount,time()+3600,'/');
  echo '<TABLE width=" 775" border="0" cellSpacing="0" cellPadding="0" align="center" style="background-color: #f8f9f9; border: 1px solid #b4c9e7;"><tr><td colspan="3" align="center"><form style="margin:0px">共 <b>';
  echo $total_records.'</b> 条信息&nbsp;&nbsp;<font color="#888888">';
  if($page==1) echo '首页&nbsp;上一页 &nbsp;';
  else echo '<a href="#" onclick="return show_msg(1)">首页</a>&nbsp;<a href="#" onclick="return show_msg('.($page-1).')">上一页</a> &nbsp;';
  if($page==$total_pages) echo '下一页&nbsp;尾页';
  else echo '<a href="#" onclick="return show_msg('.($page+1).')">下一页</a>&nbsp;<a href="#" onclick="return show_msg('.$total_pages.')">尾页</a>';
  echo '&nbsp; </font>页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp;&nbsp;&nbsp; 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="show_msg(this.form.page.value);"></form></td></tr></table>';
}?>
  <form method="post"  action="user/usrmsg.php?action=new" onsubmit="return true;;return CheckSubmitMsg(this)">
  <table align="center" cellSpacing="0" cellPadding="0" width=" 775" style="background-color: #f8f9f9; border: 2px solid #b4c9e7;margin-top:20px">
  <tr><td width="100%" colspan="2"><textarea name="content"  rows="3" cols="20" wrap="VIRTUAL" style="width: 100%; font-size: 9pt; border: 1 solid #808080" ></textarea></td>
  </tr>
  <tr><td><img src="<?php echo WEB_ROOT;?>images/mespic.gif" width="21" height="19" align="absMiddle"><font color="#FF6600">在此发布留言信息：</font><font color="#FF0000">(24小时内回复，如有紧急问题请直接致电) </font></td><td align="right"><input type="submit" value="递交留言"></td>
  </tr>
  </table>
  </form><?php
db_close();?>
