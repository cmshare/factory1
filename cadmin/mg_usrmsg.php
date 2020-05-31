<?php require('includes/dbconn.php');
#注意：向后台会员回消息时，如果改后台会员在前台没有相同名称的账号时，有可能会出现故障。
CheckLogin();
db_open();
	  
$action=@$_GET['action'];

if($action=='delete'){
  $selectids=$_POST['selectid'];
  if($selectids && is_array($selectids)){
    $selectids=implode(',',$selectids);
    if(CheckPopedom('MANAGE'))
      $conn->exec('update mg_message set property=0 where id in ('.$selectids.')');
    else{
      $conn->exec("update mg_message set property=0 where id in ($deleteids) and (sendto='$AdminUsername' or sendfrom='$AdminUsername')");
      $res=$conn->query("select * from mg_message where sendto='all' and property>0 and id in ($deleteids)",PDO::FETCH_ASSOC);
      $row=$res->fetch();
      if($row){
     	$cookie_del_list=@$_COOKIE[$AdminUsername]['msgdelete'];
        do{
     	  $new_del_msg=$row['id'].',';
          if(strpos(','.$cookie_del_list,','.$new_del_msg)===FALSE)$cookie_del_list.=$new_del_msg;
        }while(($row=$res->fetch()));
        setcookie($AdminUsername.'[msgdelete]',$cookie_del_list,time()+3600*24*365); 
      }
    }
    PageReturn('删除成功！');
  }
}
else if($action=='send'){
  $sendto_array=$_POST['sendto'];
  if($sendto_array && is_array($sendto_array)){
    $msgtitle=FilterText(trim($_POST['title']));
    if(empty($msgtitle)) $msgtitle='无标题';
    foreach($sendto_array as $each_sendto){
      $each_sendto=trim($each_sendto);
      if($each_sendto){
        $content=$_POST['content'];
        #后台消息不需回复：Reply不为NULL就表示已经回复，即使回复的是空串。
        $sql="mg_message set title='$msgtitle',content='$content',reply='',property=1,sendto='$each_sendto',sendfrom='$AdminUsername',addtime=unix_timestamp()"; 
        if(!$conn->exec('update '.$sql.' where property=0 limit 1')) $conn->exec('insert into '.$sql);
      }
    }
  }
  db_close();
  header('Location: ?mode=2');		
  exit(0);
}
else if($action=='reply'){
  $id=$_POST['id'];
  if(is_numeric($id) && $id>0){
    $row=$conn->query('select reply,sendto from mg_message where id='.$id,PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['sendto']==$AdminUsername) $IsSendToEditor=true;
      else if($row['sendto']=='adm') $IsSendToEditor=CheckPopedom('MANAGE');
      if($IsSendToEditor){
        $reply=trim($_POST['reply']);
        $conn->exec("update mg_message set reply='$reply' where id=$id");
    	PageReturn('消息回复提交成功！');
      }
    }
  }
  PageReturn('参数错误！');
}
else if($action=='readsign'){
  $selectids=$_POST['selectid'];
  if($selectids && is_array($selectids)){
    $selectids=implode(',',$selectids);
    if($_GET['read']=='yes') $new_property=2; else $new_property=1;
    if(CheckPopedom('MANAGE')) $conn->exec('update mg_message set property='.$new_property.' where id in ('.$selectids.') and sendto<>\'all\' and property>0 and property<>'.$new_property);
    else $conn->exec('update mg_message set property='.$new_property.' where id in ('.$selectids.') and sendto=\''.$AdminUsername.'\' and property<>'.$new_property);
    $res=$conn->query('select * from mg_message where id in ('.$selectids.') and sendto=\'all\' and property>0',PDO::FETCH_ASSOC);
    $row=$res->fetch();
    if($row){
       $cookie_dispost_list=$_COOKIE[$AdminUsername]['msgread'];
       do{
         $new_disposed_msg=$row['id'].',';
	 if($new_property==2){ 
           if(strpos(','.$cookie_dispost_list,','.$new_disposed_msg)===false) $cookie_dispost_list.=$new_disposed_msg;
         }
         else{
      	   $cookie_dispost_list=str_replace(','.$new_disposed_msg,',',','.$cookie_dispost_list);
         }
       }while(($row=$res->fetch()));
       setcookie($AdminUsername.'[msgread]',$cookie_dispost_list,time()+3600*24*365);
    }
    PageReturn('标记成功！');
  }
}
else if($action=='checkuser'){
  $username=FilterText(trim($_POST['username']));
  if($username){
    $existid=$conn->query('select id from mg_users where username=\''.$username.'\'')->fetchColumn(0);
    if($existid) echo 'OK';  
  }
  db_close();
  exit(0);
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>站内信息管理</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">  	
      <tr>
      	<td>
    	    <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>站内消息管理</font></a></b> 
    	  </td>
        <td align="right"><?php
 $mode=@$_GET['mode'];
 if($mode=='2'){ #发件箱
     $objtitle='收件人';
     $objname='sendto';
     $sql_msglist='where sendfrom=\''.$AdminUsername.'\' and property>0';
     echo '<a href="?mode=1" style="font-weight:bold;color:#8F8F8F">收件箱</a> | <a href="?mode=2" style="font-weight:bold;color:#FF6600">发件箱</a>';
 }
 else{ #收件箱
     if($mode!='1') $mode='1';
     $objtitle='发件人';
     $objname='sendfrom';
     $con_del_list=FilterText(@$_COOKIE[$AdminUsername]['msgdelete']);
     if($con_del_list) $con_del_list='and id not in ('.$con_del_list.')';
     if(CheckPopedom('MANAGE')) $sql_msglist='or sendto=\'adm\'';else $sql_msglist='';
     $sql_msglist='where (sendto=\''.$AdminUsername.'\' '.$sql_msglist.' or sendto=\'all\') and property>0 '.$con_del_list;
     echo '<a href="?mode=1" style="font-weight:bold;color:#FF6600">收件箱</a> | <a href="?mode=2" style="font-weight:bold;color:8F8F8F">发件箱</a>';
}?>
     </td>  
      </tr>
      </table>  
    </td>
  </tr>

  <tr bgcolor="#FFFFFF"> 

      <td valign="top" width="100%"> 
      	
<?php 
if($action=='new'){
   $sendto=str_replace(',','\',\'',trim(FilterText($_GET['sendto'])));?>
  <form method="post" action="?action=send" onsubmit="return CheckSubmitMessage(this)">
  <table width="96%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#F7F7F7">
     <td width="20%" height="25" align="center" background="images/topbg.gif"><b>收件人</b> <input type="checkbox" onclick="Checkbox_SelectAll('sendto',this.checked)">
     <br><input type="button" value="增加收件人..." style="color:#FF0000" onclick="AddReceiver()"></td>
     <td width="80%" id="SendList"><?php
      $sql='select username,1 as selected from mg_users where username in (\''.$sendto.'\') union select username,0 as selected from mg_admins where username not in (\''.$sendto.'\')  order by selected desc,username';
      $res=$conn->query($sql,PDO::FETCH_ASSOC);
      foreach($res as $row){
        echo '<input name="sendto[]" type="checkbox" value="'.$row['username'].'">'.$row['username'].' &nbsp; ';
      }
      if(CheckPopedom('MANAGE')) echo '<input name="sendto[]" type="checkbox"  value="all"><font color="#FF0000">所有客户</font> &nbsp;';?>
     </td>
  </tr>
  <tr bgcolor="#F7F7F7">
     <td width="20%" height="25" align="center" background="images/topbg.gif"><b>标&nbsp; 题</b></td>
     <td width="80%"><input name="title" type="text" maxlength="50" style="width:96%"></td>
  </tr>
  <tr bgcolor="#F7F7F7">
     <td width="20%" height="25" align="center" background="images/topbg.gif" valign="top"><b>内&nbsp; 容</b></td>
     <td width="80%">

     <INPUT type="hidden" name="content" value="">	
     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="content" type="text/plain"></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('content',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>


     	</td>
  </tr>
  <tr bgcolor="#F7F7F7">
  	 <td height="25" align="center" background="images/topbg.gif" valign="top">&nbsp;</td>
     <td  align="center"><input type="submit" value=" 发送消息 " >&nbsp; &nbsp; </td>
  </tr> 
  </TABLE>
</form> 
<script>
 var sendto_array=new Array('<?php echo $sendto;?>');
 function CheckSubmitMessage(myform){
   if(Checkbox_SelectedCount("sendto[]")==0){
     alert("没有选择收件人！");
     return false;
   }
   if(myform.title.value.trim()==""){
     alert("没有填写消息标题！");
     return false;
   }
   myform.content.value=ueditor.getContent();
   if(myform.content.value.trim()==""){
     alert("没有填写消息内容！");
     return false;
   }
   return true;
 }
 function AddReceiver(){
   var obj=document.getElementById("SendList");
   if(obj){
     var addSendTo=window.prompt("增加收件人，请输入用户名:\n\n", "");
     if(addSendTo){
       var ret=SyncPost("username="+escape(addSendTo),"?action=checkuser");
       if(ret=="OK"){
         var aa = document.getElementsByName("sendto[]");
	 var sendto_len=sendto_array.length;
	 for(var jj=0;jj<sendto_len;jj++){
           for (var ii=0; ii<aa.length; ii++){
             if (aa[ii].type == "checkbox" && aa[ii].value==addSendTo){
               aa[ii].checked=true;
       	       return;
             }
           }
         }
  	 obj.innerHTML='<input name="sendto[]" type="checkbox" checked value="'+addSendTo+'">'+addSendTo+' &nbsp; '+obj.innerHTML;
       }else alert("用户名["+addSendTo+"]不存在！");   
     }
   }
 }

 <?php if($sendto){?> 
     var aa = document.getElementsByName("sendto");
     var sendto_len=sendto_array.length;
     for(var jj=0;jj<sendto_len;jj++){
       for (var ii=0; ii<aa.length; ii++){
          if (aa[ii].type == "checkbox" && aa[ii].value==sendto_array[jj]){
            aa[ii].checked=true;
            break;
          }
       }
     }<?php
 }?>
  </script>
<?php
}
else{
    $id=@$_GET['id'];

    if(is_numeric($id) && $id>0){ 	
        $row=$conn->query('select * from mg_message where id='.$id,PDO::FETCH_ASSOC)->fetch();
        if($row){
            $IsSendFromMe=($row['sendfrom']==$AdminUsername); 
            if($row['sendto']==$AdminUsername || $row['sendto']=='all') $IsSendToMe=true;
            else if($row['sendto']=='adm') $IsSendToMe=CheckPopedom('MANAGE');
            else $IsSendToMe=false;
        }
        else{
            $IsSendToMe=false;
            $IsSendFromMe=false;	  			 
        }
        if($IsSendToMe || $IsSendFromMe){?>
            <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr> 
              <td HEIGHT="50" align="center">
              	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                   <td height="40" width="95%" align="center" style="color:#FF6600;font-size:16pt;font-weight:bold"><?php echo $row['title'];?></td><td width="5%">&nbsp;</td>
                </tr>
                <tr bgcolor="#f7f7f7">
                   <td height="30" align="center" >收件人：<b><?php echo $row['sendto'];?></b> &nbsp;  发件人：<?php echo $row['sendfrom'];?> &nbsp; 时间：<?php echo $row['addtime'];?></td>
                   <td nowrap align="right"><?php if($IsSendToMe && $row['sendto']!='all'){?><a href="#MessageReply" style="font-weight:bold;color:#FF0000" onclick="ShowReplyBox(true)"><img src="images/dot.gif" border="0" align="absmiddle">[<u>回复</u>]</a><?php }else{?><a href="<?php echo $_SERVER['HTTP_REFERER'];?>">[<u>返回</u>]</a><?php }?></td>
                </tr>
                </table>
              </td>
            </tr>
            <tr> 
              <td valign="top" style="padding-top:20px;font-size:11pt"><?php echo $row['content'];?></td>
            </tr>
            </table>
		        
		        <br><br>
      	<form method="post" action="?action=reply" onsubmit="if(confirm('确定要提交回复？')){this.reply.value=ueditor.getContent();return true;}else return false;"> 
       
            <table id="ReplyBox"  width="98%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
            <tr> 
              <td height="20" background="images/topbg.gif" bgcolor="#F7F7F7"><b><img src="images/pic19.gif" align="absmiddle" /><a name="MessageReply"><font color=#FF0000>消息回复</font></a></b></td>
            </tr>
            <?php if($IsSendToMe){?>
            <tr>        
                <td bgcolor="#FFFFFF" width="100%">
              	<input type="hidden" name="id" value="<?php echo $id;?>"><INPUT type="hidden" name="reply">	

     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="reply" type="text/plain"><?php echo $row['reply'];?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('reply',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>


              </td>
            </tr>
            <tr><td align="center" height="20" valign="middle"><input type="submit" value="提交回复"></td>
            </tr><?php
            }
            else{?>
              <tr><td valign="top"><?php if($row['reply']) echo $row['reply']; else echo '无';?></td></tr><?php
            }?>
            </table>
           </form>
           <script>
            var reply_box_show=false; 
            function ShowReplyBox(bShowOrHide)
            { var obj=document.getElementById("ReplyBox");
            	if(obj)obj.style.display=(bShowOrHide)?"block":"none";
            }<?php

            if($row['sendto']!='all'){
               if(empty($row['reply'])) echo 'if(window.location.href.indexOf("#MessageReply")>0)';
               echo 'reply_box_show=true;';
            }
            echo 'if(!reply_box_show)ShowReplyBox(false);';
	    if($row['property']==1){

               if($row['sendto']=='all'){
		 $cookie_dispost_list=$_COOKIE[$AdminUsername]['msgread'];
	         $new_disposed_msg=$id.',';
		 if(strpos(','.$cookie_dispost_list,','.$new_disposed_msg)===false)echo 'setCookie("'.$AdminUsername.'[msgread]","'.$cookie_dispost_list.$new_disposed_msg.'",new Date(new Date().getTime()+3600*1000*24*365))';
               }
               else if($IsSendToMe){
                   $conn->exec('update mg_message set property=2 where id='.$id);
               }
            }
            echo '</script>';
         } 
    }
    else{?><form method=post> 
        <table width="96%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#F7F7F7">
            <td width="5%" height="25" align="center" background="images/topbg.gif"><input type="checkbox" name="checkbox" value="0" onclick="Checkbox_SelectAll('selectid[]',this.checked)" ></td>
            <td width="55%" height="25" align="center" background="images/topbg.gif"><strong>主题</strong></td>
            <td width="15%" height="25" align="center" background="images/topbg.gif"><strong><?php echo $objtitle;?></strong></td>
            <td width="25%" height="25" align="center" background="images/topbg.gif"><strong>日期</strong></td>
          </tr><?php
     $res=page_query('select *','from mg_message',$sql_msglist,'order by addtime desc',20);
    if($res) foreach($res as $row){
       if($row['property']==2) $msgDisposed=true;
       else if($row['sendto']=='all') $msgDisposed = (strpos(','.@$_COOKIE[$AdminUsername]['msgread'],','.$row['id'].',')!==false);
       else $msgDisposed=false;?>		 
          <tr  bgcolor="#F7F7F7" onMouseOut="mOut(this)" onMouseOver="mOvr(this)" align="center" <?php if(!$msgDisposed) echo 'style="font-weight:bold"';?>>
            <td height="25"><input name="selectid[]" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>
            <td height="25" align="left" style="padding-left:10px">
             	<a href="?id=<?php echo $row['id'];?>&mode=<?php echo $mode;?>">
             		 <img src="images/mail<?php  echo ($msgDisposed)?'2':'1';?>.gif" width=13 height=13 border="0" align="absmiddle">&nbsp;<?php
             		 	 if($row['title'])echo $row['title'];
             		 	 else echo $row['content'];?>
             	   <?php if($row['reply']==null) echo '&nbsp; <img border=0 src="images/dot.gif" WIDTH="10" HEIGHT="10">&nbsp;<font color="#FF8000"><u><b>待回复</b></u></font>';
             	   else if($row['reply']) echo '<br><img border=0 src="images/pic19.gif" align="absmiddle"><u>回复</u>：<font color="#FF8000">'.$row['reply'].'</font>';?>
             	   </a>
            </td>
            <td height="25"><?php echo $row[$objname];?></td>
            <td height="25" nowrap><?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
          </tr><?php
    }?>
  <script>
       function DeleteMessage(myform)
       { var selcount=Checkbox_SelectedCount("selectid[]");
       	 if(selcount<1)alert("没有选择删除对象！");
       	 else if(confirm("从<?php if($mode=='1' )echo '收件箱'; else echo '您的发件箱及对方的收件箱';?>中删除"+selcount+"条信息？"))
       	 { myform.action='?action=delete'; 
           myform.submit();
      	 }
       }
       function SetReadSign(myform,bRead)
       { var selcount=Checkbox_SelectedCount("selectid[]");
       	 if(selcount<1)alert("没有选择操作对象！");
       	 else if(confirm("将选定的"+selcount+"条信息标志为"+((bRead)?"已":"未")+"读？"))
       	 { myform.action="?action=readsign&read="+((bRead)?"yes":"no");
           myform.submit();
         }
       }
       function RelTopic()
       { var selcount=Checkbox_SelectedCount("selectid[]");
       	 if(selcount==1)
       	 { var a = document.getElementsByName("selectid[]");
       	   var selections="";
       	   var mestitle="";
            for (var i=0; i<a.length; i++)
            { if (a[i].type == "checkbox" && a[i].checked)
              { self.location.href="?id="+a[i].value+"&mode=<?php echo $mode;?>#MessageReply";
              	 return;
              }
            }
          }
          else if(selcount>1) alert("一次只能回复一个主题！");  
          else alert("没有选择回复对象！");  
       }
       function RelyMessage()
       { var selections="";
       	var selcount=Checkbox_SelectedCount("selectid");
       	if(selcount>0)
       	{ var a = document.getElementsByName("selectid");
       	  
           for (var i=0; i<a.length; i++)
           { if (a[i].type == "checkbox" && a[i].checked)
             { if(!selections)selections=a[i].parentNode.parentNode.cells[2].innerText;
           	   else selections=selections+","+ a[i].parentNode.parentNode.cells[2].innerText;
             }
           }
       	}
       	self.location.href="?action=new&sendto="+selections+"&mode=2";
       }
      </script>
		  <tr height="30" bgcolor="#FFFFFF">
		    <td colspan="4" align="center" >
		      <?php if($mode==1){?>
            <input type="button" value="回复主题" onclick="RelTopic()">
            <input type="button" value="标志已读" onclick="SetReadSign(this.form,true)">
            <input type="button" value="标志未读" onclick="SetReadSign(this.form,false)"><?php
                      }?>
            <input type="button" value="发送信息" onclick="RelyMessage()">     
            <input type="button" value="删除信息" onclick="DeleteMessage(this.form)">
		    </td>
		  </tr>	
      <tr height="30" bgcolor="#FFFFFF">
        <td colspan="4" align="center" >
         	 <script language="javascript">  
             GeneratePageGuider("mode=<?php echo $mode;?>",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);
           </script>
        </td>
      </tr>  
      </table></form><?php
         }
    }
?>
      </td>

  </tr>
</table>

</body>
</html><?php
db_close();?>
