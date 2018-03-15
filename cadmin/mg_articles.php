<?php require('includes/dbconn.php');
CheckLogin('INFOMATION');
OpenDB();

define('MAX_PROPERTY',7);
$PropertyName=array();
$PropertyName[0]='所有文章';
$PropertyName[1]='商城动态';  #涵若铭妆与铭悦商城其同的新闻模块
$PropertyName[2]='今日话题';
$PropertyName[3]='公司新闻';  #香港铭悦的新闻模块
$PropertyName[4]='站长资料';
$PropertyName[5]='内部文档';
$PropertyName[6]='网站导航';
$PropertyName[7]='美丽资讯';  #美丽商城的新闻模块

$action=@$_GET['action'];
if($action){
  switch($action){
    case 'del': del_articles();break;
    case 'add':add_article();break;
    case 'edit':edit_article();break;
    case 'addsave':edit_save(0);break;
    case 'editsave':edit_save($_POST['id']);break;
  }
  CloseDB();
  exit(0);
}

function del_articles(){
   global $conn;
   $selectid=$_POST['selectid'];
   if(empty($selectid)) PageReturn("没有选择操作对象！",-1);
   else{
      $idlist=implode(',',$selectid);
      $conn->exec('update mg_article set property=0 where id in ('.$idlist.')');
      PageReturn('删除成功！');
   }
}

function edit_save($articleid){
  global $conn;
  $title=FilterText(trim($_POST['newstitle']));
  $content=rtrim($_POST['newscontent']);
  $author=FilterText(trim($_POST['author']));
  $property=$_POST['property'];
  if(is_numeric($articleid) && $title && $content && $author && is_numeric($property) && $property>0){
    $sql="mg_article set title='$title',content='$content',author='$author',property=$property";
    if($articleid===0){
      $sql.=',viewnum=0,addtime=unix_timestamp()';
      if($conn->exec('update '.$sql.' where property=0 limit 1') || $conn->exec('insert into '.$sql)) PageReturn('文章发表成功！','?property='.$property);
    }
    else if($articleid>0){
      $addtime=trim($_POST['addtime']);
      if($addtime){
        $addtime=strtotime($addtime);
        if($addtime)$sql.=',addtime='.$addtime;
      }
      if($conn->exec('update '.$sql.' where id='.$articleid)) PageReturn('文章修改成功！','?property='.$property);
      else PageReturn('文章没有修改！');
    }
  }
}
   
function add_article(){
  global $conn,$PropertyName;
  $property=$_GET['property'];?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>发表文章</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
    <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_articles.php?property=<?php echo $property;?>">文章管理</a> -&gt; <font color=#FF0000>发表文章</font></b> </td></tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> 
	<form  method="post" action="?action=addsave" onsubmit="return CheckPost(this);" style="margin:0px">
         <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF">
            <td width="15%" bgcolor="#F7F7F7" align="center" nowrap><strong>文章主题：</strong></td>
            <td width="35%"><input name="newstitle" type="text" class="input_sr" size="40" style="100%"></td>
            <td width="50%" align="right"><strong>栏目：</strong><select name='property'><option value='0'>选择栏目</option><?php
             for($i=1;$i<MAX_PROPERTY;$i++){
                  $selected=($i==$property)?' selected':'';
                  echo '<option value="'.$i.'"'.$selected.'>'.$PropertyName[$i].'</option>';
        }?></select><input name="author" type="hidden" class="input_sr" id="author" value="<?php echo $GLOBALS['AdminUsername'];?>"></td>
          </tr>
          <tr bgcolor="#FFFFFF">
            <td valign="top" bgcolor="#F7F7F7" align="center" nowrap><strong>文章内容：</strong></td>
            <td colspan="2">
            
     <INPUT type="hidden" name="newscontent">	
     <script id="newscontent" type="text/plain"></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('newscontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});
          
        function CheckPost(myForm){
          if(myForm.newstitle.value==""){
            alert("请输入文章标题！");
	    return false;
	  }
	  else if(myForm.property.selectedIndex==0){
            alert("请选择文章所属的栏目类别！");
            return false;
	  }
          else if(myForm.author.value==""){
            alert("发表人不能为空！");
            return false;
	  }
          myForm.newscontent.value=ueditor.getContent();
          return true; 
	}</script>
 
             </td>
          </tr>
          <tr bgcolor="#F7F7F7">
            <td  colspan="3" align="center"><input type="submit" value=" 提交发表 "></td>
          </tr>
        </table>
	</form></td>
  </tr>
</table>
</body>
</html><?php
}

function edit_article(){
  global $conn,$PropertyName;
  $articleid=$_GET['id'];
  if(is_numeric($articleid) && $articleid>0) $row=$conn->query('select * from mg_article where id='.$articleid,PDO::FETCH_ASSOC)->fetch();
  if($row){
     $property=$row['property'];?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_articles.php?property=<?php echo $property;?>">文章管理</a> -&gt; <font color=#FF0000>文章编辑</font></b></td>
  </tr>
  <tr> 
    <td height="50" valign="top" bgcolor="#FFFFFF"><form method="post" action="?action=editsave" onsubmit="return CheckModifyPost(this);">
        <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td width="15%" height="25" bgcolor="#F7F7F7" align="center"><strong>文章主题：</strong></td>
            <td width="30%" height="25">
              <input type="hidden" name="id" value="<?php echo $row['id'];?>" >
              <input name="newstitle" type="text" class="input_sr" value="<?php echo $row['title'];?>" size="40"></td>
             <td width="15%" nowrap><strong>栏目：</strong>
            	<select name="property">
            		<option value="0">选择栏目</option><?php
        for($i=1;$i<MAX_PROPERTY;$i++){
                  $selected=($i==$property)?' selected':'';
                  echo '<option value="'.$i.'"'.$selected.'>'.$PropertyName[$i].'</option>';
        }?></select></td>
             <td width="20%" nowrap><strong>发 表 人：</strong><input name="author" type="text" class="input_sr" value="<?php echo $row['author'];?>">
             <td width="20%" ><strong>发布时间：</strong><input name="addtime" type="text" class="input_sr" value="<?php echo date('Y-m-d H:i:s',$row['addtime']);?>"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td height="25" valign="top" bgcolor="#F7F7F7" align="center"><strong>文章内容：</strong></td>
            <td height="25" colspan="4">

     <INPUT type="hidden" name="newscontent">	
     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="newscontent" type="text/plain"><?php if($row['content']) echo $row['content'];?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('newscontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});
        String.prototype.trim = function(){
          return this.replace(/(^\s*)|(\s*$)/g, ""); 
        } 
        function CheckModifyPost(myForm){
          if(myForm.newstitle.value.trim()==""){
            alert("请输入文章标题！");
	    return false;
	  }
          else if(myForm.property.selectedIndex==0){
            alert("请选择文章所属的栏目类别！");
            return false;
	  }
          else if(myForm.author.value.trim()==""){
            alert("发表人不能为空！");
            return false;
	  }
          myForm.newscontent.value=ueditor.getContent();
          if(myForm.newscontent.value.trim()==""){
            alert("请输入文章内容！");
	    return false;
	  }
          return true; 
	}</script>

            </td>
          </tr>
          <tr bgcolor="#F7F7F7" align="center"> 
            <td height="28" colspan="5" style="PADDING-LEFT: 6px"> <input type="submit" value=" 提交修改 "></td>
          </tr>
        </table>
	</form></td>
  </tr>
</table>
</body>
</html><?php
  }
}

$property=@$_GET['property'];
if(!is_numeric($property)) $property=0;   
$keyvalue=FilterText(trim(@$_GET['kv']));
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>文章管理</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
    	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    	<TR>
    		<TD><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?property=<?php $property;?>"><font color=#FF0000>文章管理</font></a></b></td>
        <TD align=right><select name="select" onChange="var jmpURL=this.options[this.selectedIndex].value ; if(jmpURL!='') {window.location=jmpURL;} else {this.selectedIndex=0 ;}" ><?php
        for($i=0;$i<MAX_PROPERTY;$i++){
           $selected=($i==$property)?' selected':'';
           echo '<option value="?property='.$i.'"'.$selected.'>'.$PropertyName[$i].'</option>';
        }?></select>
        </TD>
      </TR>
      </TABLE>    
    </td>
  </tr>
  <tr> 
    <td align="center" valign="top" bgcolor="#FFFFFF"> 
       <form method="post" action="?action=del"> 
       <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
       <tr bgcolor="#F7F7F7" align="center">
         <td height="25" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)"></td>
         <td height="25" background="images/topbg.gif"><strong>标 题</strong></td>
         <td height="25" background="images/topbg.gif"><strong>栏目</strong></td>
         <td height="25" background="images/topbg.gif"><strong>发布人</strong></td>
         <td height="25" background="images/topbg.gif"><strong>发布时间</strong></td>
       </tr><?php
       if($keyvalue){
          $sql_where='where title like \'%'.$keyvalue.'%\'';
          echo '<b>根据<font color="#FF6600">文章标题</font>模糊搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
       }
       else $sql_where=($property>0)?"where property=$property":'where property>0';
       $res=page_query('select *','from mg_article',$sql_where,'order by addtime desc',20);
       if(!$res)echo '<tr><td colspan=5 align="center"> 您还没有添加该类文章！</td></tr>';
       else{
         foreach($res as $row){?>
          <tr bgcolor="#FFFFFF"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)" align="center"> 
          <td height="25"><input name="selectid[]" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>  	
          <td height="25"  align="left">&nbsp;<a href="?id=<?php echo $row['id'];?>&action=edit"><?php echo $row['title'];?></a></td>
          <td height="25"><?php if($row['property']>MAX_PROPERTY) echo '未分类';else echo $PropertyName[$row['property']];?></td>
            <td height="25"><?php echo $row['author'];?></td>
            <td height="25"><?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
          </tr><?php
         }?>
         <tr>
      	  <TD colspan=5 align="center" bgcolor="#FFFFFF" height=35><script language="javascript">GeneratePageGuider("property=<?php echo $property;?>&kv=<?php echo rawurlencode($keyvalue);?>",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);</script></TD>
         </tr><?php
      }?>
         <tr bgcolor="#F7F7F7"> 
            <td height="35" colspan="5" align="center">
            	<input type="button"  value="发表新文章" onclick="self.location.href='mg_articles.php?action=add&property=<?php echo $property;?>'">&nbsp;
            	<input type="button"  value="删除所选文章" onclick="BatchDeleteArticle(this.form)">
            </td>
          </tr>
        </table>
        </form>
 
    </td>
  </tr>
</table>
<br>
<table width="100%" border="5" height="20" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td align="center">
    <form method="get" action="?property=<?php echo $property;?>">
      按标题<input name="kv" type="text" size="20" value="<?php echo $keyvalue;?>"> &nbsp; <input type="submit" value="模糊查询"></td>
    </form>
  </td>
</tr>
</table>

<script language=javascript>
function BatchDeleteArticle(myForm){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else if(confirm("确定要删除所选的"+selcount+"篇文章吗？")) myForm.submit();
} 
</script>
</body>
</html><?php
CloseDB();?>
