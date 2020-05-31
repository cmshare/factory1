<?php require('conn_articles.php');

$PropertyName=array('所有文章','涵若铭妆','铭悦商城','-未归类-');

class CommSQL{
  private $fields=array();
  private $strings=array();
  private $tableName;
  function __construct($tableName) {
    //在php4中构造函数采用与类同名的方式进行定义
    //在php5中构造函数采用__construct定义
    $this->tableName=$tableName;
  }
  function __destruct(){
    //析构函数
  }
  private function genInsertSQL(){
    $key_list='';
    $value_list='';
    foreach($this->fields as $key=>$value){
      if($key_list){
        $key_list.=',';
        $value_list.=',';
      }
      $key_list.=$key;
      $value_list.=$value;
    }
    return 'insert into '.$this->tableName.'('.$key_list.') values('.$value_list.')';
  }
  private function genUpdateSQL(){
    $se_list='';
    foreach($this->fields as $key=>$value){
      if(empty($set_list)) $set_list=$key.'='.$value;
      else $set_list.=','.$key.'='.$value;
    }
    return 'update '.$this->tableName.' set '.$set_list;
  }
  public function addField($key,$value){
    $this->fields[$key]=$value;
  }
  public function addString($key,$value){
    $this->fields[$key]='\''.$value.'\'';
  }
  public function insert($where=false){
    if($where){
      $id=$GLOBALS['conn']->query('select id from '.$this->tableName.' '.$where.' limit 1')->fetchColumn(0);
      if($id) return $GLOBALS['conn']->exec($this->genUpdateSQL().' where id='.$id);
    }
    return $GLOBALS['conn']->exec($this->genInsertSQL());
  }
  public function update($where){
    return $GLOBALS['conn']->exec($this->genUpdateSQL().' '.$where);
  }
}

db_open();
$conn->setAttribute( PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);

$mode=@$_GET['mode'];
if($mode=='save0' || $mode=='save1' || $mode=='save2'){
  $author=FilterText(trim(@$_POST['newsauthor']));
  $link=FilterText(trim(@$_POST['newslink']));
  $property=@$_POST['newsproperty'];  
  $title=FilterText(trim(@$_POST['newstitle']));
  $content=rtrim(@$_POST['newscontent']);

  $sql=new CommSQL('articles');
  $sql->addString('title',$title);
  $sql->addString('author',$author);
  $sql->addString('link',$link);
  $sql->addString('content',$content);
  $sql->addField('property',$property);
  if($mode!='save1')$sql->addField('addtime',"strftime('%s','now')");
  if($mode=='save0'){
     $sql->insert('where property=0');
  }
  else{
     $newsid=$_POST['id'];
     if(!$sql->update('where id='.$newsid)) PageReturn('文章不存在！');
  }
  PageReturn('保存成功！');
}
else if($mode=='del'){
  $selectid=$_POST['newsid'];
  if(empty($selectid)) PageReturn('没有选择操作对象！');
  else{
    $idlist=implode(',',$selectid);
    //echo "update `articles` set property=0 where id in ($idlist)";
    $conn->exec("update `articles` set property=0 where id in ($idlist)");
    PageReturn("删除成功！");
  }
}
else{
  $newsid=@$_GET['id'];
  if(empty($mode) && is_numeric($newsid) && $newsid>0){
    $row=$conn->query('select * from `articles` where id='.$newsid,PDO::FETCH_ASSOC)->fetch();
    if($row){
      $property=$row['property'];
    }
    else{
      echo '<br><p align="cener">您访问的内容不存在或者已经删除！</p>';
      db_close();
      exit(0);
    }
  }
  else{
    $property=@$_GET['property'];
    if(!is_numeric($property)) $property=0; 		       
  }
}
session_start();

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/cadmin/includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="/cadmin/includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>网文八卦-资讯文章管理</title>
</head>
<body leftmargin="0" topmargin="0">
<?php
 $AdminName=@$_SESSION['meray[admin]'];
 if($AdminName!='aufame'){
  echo '<br><p align=center>权限不足！</p>';
  db_close();
  exit(0);
}?>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="/cadmin/images/topbg.gif">
    	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    	<TR>
    		<TD><b><img src="/cadmin/images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="/cadmin/admincenter.php">管理首页</a> -&gt; <a href="?property=<?php echo $property;?>"><font color=#FF0000>资讯文章管理</font></a></b></td>
        <TD align=right><?php if($newsid==0) echo '<input type="button" class="input_bot"  value="删除文章" onclick="BatchDeleteArticle(formlist);"> &nbsp; ';?><input type="button" class="input_bot" value="发表新文章" onclick="self.location.href='?mode=add&property=<?php echo $property;?>';">&nbsp</TD>
      </TR>
      </TABLE>    
    </td>
  </tr>
  <tr> 
    <td height="140" valign="top" bgcolor="#FFFFFF"> 
<?php if($mode=='add'){?>
  	   <table width="750" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <form method="post" action="?mode=save0" onsubmit="return CheckModifyPost(this);">
          <tr bgcolor="#F7F7F7" height="25">
            <td width="600"><strong>文章标题：</strong><input name="newstitle" type="text" class="input_sr"  style="width:500px"></td>
            <td width="150">&nbsp;<strong>栏目：</strong><select name="newsproperty" style="width:100px;"><?php
               for($jishu=1;$jishu<count($PropertyName);$jishu++){
            	   $selectcode=($property==$jishu)?'selected':'';
            	   echo '<option '.$selectcode.' value="'.$jishu.'">'.$PropertyName[$jishu].'</option>';
               }?></select></td>
          </tr>
          <tr bgcolor="#F7F7F7" height="25">
            <td><strong>链接转向：</strong><input name="newslink" type="text" style="width:500px"></td>
            <td><strong>&nbsp;作者：</strong><input name="newsauthor" type="text" value="<?php echo $AdminName;?>" size=10 style="width:100px"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
             <td height="25" colspan="2"><INPUT type="hidden" name="newscontent">	

     <link rel="stylesheet" href="/cadmin/ueditor/themes/default/css/ueditor.css">
     <script id="newscontent" type="text/plain"></script>
     <script type="text/javascript" src="/cadmin/ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="/cadmin/ueditor/ueditor.all.js"></script>
            </td>
          </tr>
          <tr bgcolor="#F7F7F7" align="center"> 
            <td height="28" colspan="2" style="PADDING-RIGHT: 6px" align="right"> 
              <input type="submit" class="input_bot" value=" 提 交 "></td>
          </tr></form>
        </table><?php
} 
else if(empty($mode) && $newsid>0){ 
  $row=$conn->query('select * from `articles` where id='.$newsid,PDO::FETCH_ASSOC)->fetch();
  if($row){?>
  	 <form method="post" style="border:0px"> 
  	 <table width="750" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#F7F7F7" height="25">
            <td width="600"><strong>文章标题：</strong><input name="newstitle" type="text" class="input_sr" id="newstitle" value="<?php echo $row['title'];?>" style="width:500px"></td>
            <td width="150">&nbsp;<strong>栏目：</strong><select name="newsproperty" style="width:100px;"><?php
            	 for($jishu=1;$jishu<count($PropertyName);$jishu++){
            	    $selectcode=($property==$jishu)?'selected':'';
            	    echo '<option '.$selectcode.' value="'.$jishu.'">'.$PropertyName[$jishu].'</option>';
            	 }?></select></td>
          </tr>
          <tr bgcolor="#F7F7F7" height="25">
            <td><strong>链接转向：</strong><input name="newslink" type="text" value="<?php echo $row['link'];?>" style="width:500px"></td>
            <td><strong>&nbsp;作者：</strong><input name="newsauthor" type="text" value="<?php echo $row['author'];?>" size=10 style="width:100px"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
             <td height="25" colspan="2">
            	<INPUT type="hidden" name="newscontent">	

     <link rel="stylesheet" href="/cadmin/ueditor/themes/default/css/ueditor.css">
     <script id="newscontent" type="text/plain"><?php echo $row['content'];?></script>
     <script type="text/javascript" src="/cadmin/ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="/cadmin/ueditor/ueditor.all.js"></script>

            </td>
          </tr>
          <tr bgcolor="#F7F7F7" align="center"> 
            <td height="28" colspan="2" style="PADDING-RIGHT: 6px" align="right"> 
              <input type="hidden" name="id" value="<?php echo $newsid;?>" > <input type="button" name="savebtn_republish"  value="重新发布" onclick="SaveArticle(this.form,2)" disabled> <input type="button" name="savebtn"  value="保存修改" onclick="SaveArticle(this.form,1)" disabled>
            </td>
          </tr></form>
        </table><?php
    }
}
else{
   $namekey=FilterText(trim(@$_GET['namekey']));?>
     <style type="text/css"><!--TR.deleted TD,TR.deleted TD A,TR.deleted TD A:visited{ COLOR: #AFAFAF;}--></style>
     <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr bgcolor="#F7F7F7" align="center"><form name="formlist" method="post" action="?mode=del"> 
       <td height="25" background="/cadmin/images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('newsid',this.checked)"></td>
       <td height="25" background="/cadmin/images/topbg.gif"><strong>标 题</strong></td>
       <td height="25" background="/cadmin/images/topbg.gif"><select style="font-weight:bold" onchange="self.location.href='?property='+this.value;"><?php
          for($jishu=0;$jishu<count($PropertyName);$jishu++){
            $selectcode=($property==$jishu)?'selected':'';
            echo '<option '.$selectcode.' value="'.$jishu.'">'.$PropertyName[$jishu].'</option>';
          }?>
          </select></td>
       <td height="25" background="/cadmin/images/topbg.gif"><strong>发布时间</strong></td>
     </tr><?php
         $MaxPerPage=20; 
	 if($property>0) $where='where property='.$property;
         else $where='where property>0';
	 if($namekey) $where.=' and title like \'%'.$namekey.'%\'';
         $res=page_query('select *','from `articles`',$where,'order by addtime desc',$MaxPerPage);
	 if(!$res){
	    echo '<tr><td colspan=4 align="center"> 您还没有添加该类文章！</td></tr>';
         }
	 else{
           foreach($res as $row){?>
        <tr height="25" bgcolor="#FFFFFF" <?php if($row['property']==0) echo 'class="deleted"';?> onMouseOut=mOut(this,"#FFFFFF"); onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR) align="center"> 
        <td><input name="newsid[]" type="checkbox" id="newsid" value="<?php echo $row['id'];?>"></td>  	
        <td>&nbsp;<a href="?id=<?php echo $row['id'];?>" ><?php echo $row['title'];?></a></td>
        <td><?php echo $PropertyName[$row['property']];?></td>
        <td><?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
        </tr><?php
           }?>
	  <TR>
    	<TD colspan=5 align="center" bgcolor="#FFFFFF" height=35>
      <script language="javascript">  
          GeneratePageGuider("property=<?php echo $property;?>&namekey=<?php echo $namekey;?>",<?php echo $total_records;?>,<?php echo $page;?>,<?php echo $total_pages;?>);
      </script>
    	</TD>
    </TR><?php
        }?>
        </form>
      </table>
 
    </td>
  </tr>
</table>
<br>
<table width="100%" border="5" height="20" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td  background="/cadmin/images/topbg.gif" bgcolor="#F7F7F7">
     <table width="90%" border="0" cellpadding="0" cellspacing="0">
     <TR><form method="post" action="?property=<%=property%>">
     	 <TD width="10%" nowrap><img src="/cadmin/images/pic5.gif" width="28" height="22" align="absmiddle"><font color=#FF0000><b>文章搜索</b></font></td>
       <td align="center" width="90%">按标题模糊查找: <input name="namekey" type="text" id="namekey" size="12" value="<?php echo $namekey;?>">&nbsp;<input type="submit" value="查询"></td>
    </TR></form>
    </TABLE><?php
}?>
    
</td>
</tr>
</table>

<script language=javascript>
function BatchDeleteArticle(myForm)
{ var selcount=Checkbox_SelectedCount("newsid[]");
	if(selcount==0)
	{ alert("没有选择操作对象！");
	}
  else if(confirm("确定要删除所选的"+selcount+"篇文章吗？"))
  { myForm.submit();
  }
} 

function SaveArticle(myform,mode){  
   if(CheckModifyPost(myform)){
     myform.action="?mode=save"+mode;
     myform.submit();
   }
}  

var ueditor = UE.getEditor('newscontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});
ueditor.ready(function(){
  var btn1=document.forms[0].savebtn;
  var btn2=document.forms[0].savebtn_republish;
  if(btn1) btn1.disabled=false;
  if(btn2) btn2.disabled=false;  
});

String.prototype.trim = function(){
   return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 

function CheckModifyPost(myForm){
  if(myForm.newstitle.value.trim()==""){
    alert("请输入文章标题！");
    return false;
  }
  else if(myForm.newsauthor.value.trim()==""){
    alert("发表人不能为空！");
    return false;
  }
  else{
    myForm.newscontent.value=ueditor.getContent();
    if(myForm.newscontent.value.trim()==""){
      alert("请输入文章内容！");
      return false;
    }
  }
  return true; 
}
</script>
</body>
</html><?php
db_close();?>
