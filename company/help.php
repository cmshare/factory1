<?php require('include/conn.php');

db_open();
if(@$_POST['action']=='get'){
  ShowHelp();
  db_close();
  exit(0);
}

function ShowHelp(){
  global $conn,$topicid;
  $keyfilter='南京铭悦_';
  $newproperty=6;
  $topicid=$_GET['id'];
  if(is_numeric($topicid) && $topicid>0){
    $row=$conn->query('select * from `mg_article` where id='.$topicid.' and property='.$newproperty,PDO::FETCH_ASSOC)->fetch();
  }
  else{
    $helpTitle=FilterText(trim($_GET['title']));
    if(empty($helpTitle)) $helpTitle='常见问题';
    $row=$conn->query('select * from `mg_article` where title=\''.$keyfilter.$helpTitle.'\' and property='.$newproperty,PDO::FETCH_ASSOC)->fetch();
    if($row)$newid=$row['id'];
  }
  if($row){
    $helpContent=$row['content'];
    $helpTitle=str_replace($keyfilter,'',$row['title']);
  }
  else{
    $helpTitle=' ';
    $helpContent='<br><br><br><p align="center">无内容！</p>';
  }?>
  <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
  <TR>
     <TD width="100%" height="25"  background="images/client_title_bg.gif" class="pageguider"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href=".">首頁</a> &gt;&gt; <a href="help.htm">幫助導航</a>
          <?php if($helpTitle) echo '&gt;&gt; '.$helpTitle;?>
     </TD>
  </TR>
  <TR>
     <TD valign="top" style="padding:15px"><?php echo $helpContent;?></TD>
  </TR>
  </TABLE><?php
}

$PageTitle='帮助导航－南京铭悦日化用品有限公司';
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR>
  <TD colspan=2 height="15"></TD>
</TR>	
<TR valign="top">
  <TD align="center" background="images/client_bg_left.jpg" width=210" height="100%"><?php
   require('include/guide_help.php');?>     
  </TD>  
  <TD id="contentbox"></TD>
</TR>
</TABLE><?php
require('include/page_bottom.htm');
db_close();?>	
<script> AsyncPost("action=get","help.php"+window.location.search,"contentbox"); </script>	
</BODY>
</HTML>
