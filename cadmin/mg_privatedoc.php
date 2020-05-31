<?php require('includes/dbconn.php');
 CheckLogin();
 db_open();
    
 $PrivateNewsProperty=5;

 $articleid=@$_GET['id'];
 if(!is_numeric($articleid))$articleid=0;

 $mode=@$_GET['mode'];
 if($mode=='editsave'){
   $title=FilterText(trim(@$_POST['newstitle']));
   if($articleid>0 && $title){
     $content=rtrim(@$_POST['newscontent']);
     $sql='update mg_article set title=\''.$title.'\',content=\''.$content.'\' where id='.$articleid.' and property='.$PrivateNewsProperty.' and author=\''.$GLOBALS['AdminUsername'].'\'';
     $conn->exec($sql);
     PageReturn('修改成功!','?');
   }
 }
 else if($mode=='addsave'){
    $title=FilterText(trim(@$_POST['newstitle']));
    if($title){
      $content=rtrim(@$_POST['newscontent']);
      $sql='mg_article set title=\''.$title.'\',content=\''.$content.'\', property='.$PrivateNewsProperty.', author=\''.$GLOBALS['AdminUsername'].'\',addtime=unix_timestamp()';
      if($conn->exec('update '.$sql.' where property=0 limit 1') || $conn->exec('insert int '.$sql)) PageReturn('发表成功！','?');
    }
 }
 else if($mode=='del'){
   if($articleid>0){
     $sql='update mg_article set property=0 where id='.$articleid.' and property='.$PrivateNewsProperty.' and author=\''.$GLOBALS['AdminUsername'].'\'';
     if($conn->exec($sql))PageReturn('删除成功！','?');
   }
 }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>内部文档管理</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
    	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    	<TR>
    		<TD><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>内部文档管理</font></a></b></td>
        <TD align=right><?php if(empty($mode))echo '<input type="button" class="input_bot" value="发表新文章" onclick="self.location.href=\'?mode=add\'">';?></TD>
      </TR>
      </TABLE>    
    </td>
  </tr>
  <tr> 
    <td align="center" valign="top" bgcolor="#FFFFFF"><?php

if($mode=='edit'){
   if($articleid>0)
     $row=$conn->query('select * from mg_article where id='.$articleid.' and property='.$PrivateNewsProperty,PDO::FETCH_ASSOC)->fetch();
     if($row){?>
        <form method="post" action="?mode=editsave&id=<?php echo $articleid;?>" onsubmit="if(this.newstitle.value==''){alert('内容不完整！');return false;}else this.newscontent.value=ueditor.getContent();">
  	  <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td width="15%" height="25" bgcolor="#F7F7F7" align="center"><strong>文章主题：</strong></td>
            <td width="35%" height="25">
              <input name="newstitle" type="text" class="input_sr" id="newstitle" value="<?php echo $row['title'];?>" size="40" style="width:100%"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="25" valign="top" bgcolor="#F7F7F7" align="center"><strong>文章内容：</strong></td>
            <td height="25">

     <INPUT type="hidden" name="newscontent" value="">	
     <script id="newscontent" type="text/plain"><?php if($row['content'])echo $row['content'];?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('newscontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
 

            </td>
          </tr>
          <tr bgcolor="#F7F7F7" align="center"> 
            <td height="28" colspan="2" style="PADDING-RIGHT: 6px" align="right"> 
              <input type="submit" class="input_bot" value="保存修改"></td>
          </tr>
        </table></form><?php
     }
}
else if($mode=='add'){?>
          <form method="post" action="?mode=addsave" onsubmit="if(this.newstitle.value==''){alert('内容不完整！');return false;}else this.newscontent.value=ueditor.getContent();"> 
  	  <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF">
            <td width="15%" height="25" bgcolor="#F7F7F7" align="center"><strong>文章主题：</strong></td>
            <td width="35%" height="25">
              <input name="newstitle" type="text" class="input_sr" id="newstitle" value="" size="40" style="width:100%"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="25" valign="top" bgcolor="#F7F7F7" align="center"><strong>文章内容：</strong></td>
            <td height="25">

     <INPUT type="hidden" name="newscontent" value="">	
     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="newscontent" type="text/plain"></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('newscontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
 
            </td>
          </tr>
          <tr bgcolor="#F7F7F7" align="center"> 
            <td height="28" colspan="2" style="PADDING-RIGHT: 6px" align="right"> 
              <input type="submit" class="input_bot" value=" 提 交 "></td>
          </tr></form>
        </table><?php
}
else if($articleid>0){
  $row=$conn->query('select * from mg_article where id='.$articleid.' and property='.$PrivateNewsProperty,PDO::FETCH_ASSOC)->fetch();
  if($row){?>
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td HEIGHT="50" align="center">
      	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
           <td height="40" align="center" style="color:#FF6600;font-size:16pt;font-weight:bold"><?php echo $row['title'];?></td>
        </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr bgcolor="#f7f7f7" >
           <td height="30" align="center" width="95%">发布人：<?php echo $row['author'];?> &nbsp;发布时间：<?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
           <td nowrap><?php if($GLOBALS['AdminUsername']==$row['author']) echo '<a href="?id='.$articleid.'&mode=edit">编辑</a> | <a href="#" onclick="if(confirm(\'确定删除该文档？\')) self.location.href=\'?id='.$articleid.'&mode=del\';">删除</a>';?>&nbsp;</td>
        </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="200" valign="top" style="padding-top:20px;font-size:11pt"><?php echo $row['content'];?></td>
    </tr>
    </table><?php
  }
}  
else {
  $keyvalue=FilterText(trim(@$_GET['kv']));?>
       <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
       <tr bgcolor="#F7F7F7" align="center">
         <td height="25" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('articleid[]',this.checked)"></td>
         <td height="25" background="images/topbg.gif"><strong>标 题</strong></td>
         <td height="25" background="images/topbg.gif"><strong>发布人</strong></td>
         <td height="25" background="images/topbg.gif"><strong>发布时间</strong></td>
       </tr><?php
   $where="where property=$PrivateNewsProperty";
   if($keyvalue){
      $where.=' and title like \'%'.$keyvalue.'%\'';
      echo '<b>根据<font color="#FF6600">文章标题</font>模糊搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
   }
   $res=page_query('select *','from mg_article',$where,'order by addtime desc',20);
   if(!$res) echo '<tr><td colspan=5 align="center"> 您还没有添加该类文章！</td></tr>';
   else foreach($res as $row){?>
      <tr bgcolor="#FFFFFF"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)" align="center"> 
      <td height="25"><input name="articleid" type="checkbox" value=<?php echo $row['id'];?> onclick="mChk(this)"></td>  	
      <td height="25"  align="left">&nbsp;<a href="?id=<?php echo $row['id'];?>"><?php echo $row['title'];?></a></td>
      <td height="25"><?php echo $row['author'];?></td>
      <td height="25"><?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
      </tr><?php
   }?>
      <TR>
      	<TD colspan=4 align="center" bgcolor="#FFFFFF" height=35>
        <script language="javascript">GeneratePageGuider("kv=<?php echo $keyvalue;?>",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);</script>
      	</TD>
      </TR>
      </table>
    </td>
  </tr>
</table>
<br>
<table width="100%" border="5"  align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><form method="get" action="?property=<?php echo $PrivateNewsProperty;?>" style="margin:0px">
    按标题<input name="kv" type="text" size="12" value="<?php echo $keyvalue;?>"> &nbsp; <input type="submit" value="模糊查询"></form><?php
}?>
</td>
</tr>
</table>
</body>
</html><?php
db_close();?>
