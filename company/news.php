<?php require('include/conn.php');
$news_limited='property=1';
$id=@$_GET['id'];
OpenDB();
if(is_numeric($id) && $id>0){
    $row=$conn->query('select * from `mg_article` where id='.$id.' and '.$news_limited,PDO::FETCH_ASSOC)->fetch();
    if($row){
      $newsTitle=$row['title'];
      $newsContent=$row['content'];
      $newsAddTime=$row['addtime'];
      $newsproperty=$row['property'];
    }
    else{
      CloseDB();
      echo '您访问的内容不存在或者已经删除!';
      exit(0);
    }
}
else{ 
  $id=0;
  $newsproperty=@$_GET['property'];
  if($newsproperty=='1'||$newsproperty=='2'){
    $sql_condition='property='.$newsproperty;
  }
  else{ 
    $sql_condition=$news_limited;
    $property='0';
  }
}

switch($newsproperty){
  case '1': $newsCategory='商城动态';break;
  case '2': $newsCategory='今日话题';break;
  default:  $newsCategory='新闻资讯';break;
}

function ShowNews(){
  global $conn,$id,$newsTitle,$newsAddTime,$sql_condition,$total_records,$total_pages,$page,$newsContent;
  if($id>0){?>
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td HEIGHT="50" align="center">
      	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
           <td height="40" align="center" style="color:#FF6600;font-size:16pt;font-weight:bold"><?php echo $newsTitle;?></td>
        </tr>
        </table>

        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
           <td height="15" align="right" style="padding-right:20px;color:#8f8f8f">发布时间：<?php echo date('Y-m-d',$newsAddTime);?></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="200" valign="top" style="padding-top:20px;font-size:11pt"><?php echo $newsContent;?></td>
    </tr>
    </table><?php
 }
 else{?>
    <TABLE WIDTH="730" BORDER="0" align="center" CELLPADDING="5" CELLSPACING="1" bgcolor="#f2f2f2">
    <TR ALIGN="center" bgcolor="#f7f7f7" height=30> 
    <TD width="65%"><strong>文章主题</strong></TD>
    <TD width="35%"><strong>发布时间</strong></TD>
    </TR><?php
  $page_size=20;
  $res=page_query('select id,title,content,author,addtime','from mg_article','where '.$sql_condition,'order by addtime desc',$page_size);
  if(empty($total_records)){
    echo '<TR><TD ALIGN=center colspan=3>还没有文章!</TD></TR>';
    return false;
  }
  foreach($res as $row){?> 
     <TR bgcolor="#FFFFFF" height=25  onmouseover="this.style.backgroundColor='#f2f2f2'; this.style.color='#ff0000' " onmouseout="this.style.backgroundColor='';this.style.color=''">
       <TD> &nbsp; <a href="news.htm?id=<?php echo $row['id'];?>" target="_blank"><?php echo $row['title'];?></a></TD>
       <TD align="center"><?php echo date('Y-m-d H:i:s',$row['addtime']);?></TD>
     </TR><?php
      } 
    echo '<TR bgcolor="#FFFFFF"><td colspan="3" align=center height=50><form style="margin:0px">共 <b>'.$total_records.'</b> 篇文章 &nbsp;';
    if($page==1) echo '首页 上一页';
    else echo '<a href="javascript:JumpToPage(1)">首页</a> <a href="javascript:JumpToPage('.($page-1).')">上一页</a>';
    echo '&nbsp;';
    if($page==$total_pages) echo '下一页 尾页';
    else echo '<a href="javascript:JumpToPage('.($page+1).')">下一页</a> <a href="javascript:JumpToPage('.$total_pages.')">尾页</a>';
    echo '&nbsp; 页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页 <b>'.$page_size.'</b>篇文章/页'; 
    echo '转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="javascript:JumpToPage(this.form.page.value)"></form></td></tr></TABLE>';
 }
}
if(@$_POST['action']=='get'){
  ShowNews();
  CloseDB();
  exit(0);
}

$Pagination=5;
$PageTitle='新闻中心－南京铭悦日化用品有限公司';
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="10"></TD></TR>	
<TR valign="top">
  <TD align="center" background="images/client_bg_left.jpg" width=210" height="100%">
  	<table  width="100%" height="100%"  border="0" cellSpacing="0" cellPadding="0">
  	<tr><td height="10"></td></tr>
  	<tr><td align="center" height="45"><img src="images/company_info.gif" width="159" height="45"></td></tr>	
  	<tr><td height="310"><img src="images/support.gif" width="210" height="310"></td></tr>
  	<tr><td background="images/left_bg.gif"></td></tr>
    </table>
  </TD>
	<TD width="790">
	   <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0">
     <TR>
        <TD width="100%" height="25" valign="middle"  background="images/client_title_bg.gif" class="pageguider">
          <img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href=".">主頁</a> &gt;&gt; <a href="news.htm"><?php echo $newsCategory;?></a></TD>
     </TR>
     <TR>
        <TD valign="top" style="padding:15px" id="contentbox"></td>
     </tr>
     </TABLE>
  </TD>
</TR>
</TABLE>
<script>
  function JumpToPage(page){
    var params="?id="+htmRequest("id")+"&property="+htmRequest("property")+"&page="+page;
    AsyncPost("action=get","news.php"+params,function(ret){
        document.getElementById("contentbox").innerHTML=ret;
    });
    document.body.scrollTop=0;	
  }
  JumpToPage(htmRequest("page"));
</script><?php
require('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
