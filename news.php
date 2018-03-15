<?php require('include/conn.php');
OpenDB();

$news_limited='(property=1 or property=2)';
$id=@$_GET['id'];
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
  $newsproperty=@$_GET['cid'];
  if($newsproperty=='1' || $newsproperty=='2'){
    $sql_condition='property='.$newsproperty;
  }
  else{
    $sql_condition=$news_limited;
    $newsproperty='0';
  }
  $sql_count='from `mg_article` where '.$sql_condition;
  $sql_query='select id,title,content,author,addtime '.$sql_count.' order by addtime desc';
}

switch($newsproperty){
  case '1': $SubTopic='商城动态';break;
  case '2': $SubTopic='今日话题';break;
  default:  $SubTopic='新闻资讯';break;
}
if(empty($newsTitle))$newsTitle=$SubTopic;
  	

function GenPageUrl($page){
  global $newsproperty;
  $url='/news.php?page='.$page;
  if($newsproperty>0) $url.='&cid='.$newsproperty;
  return $url;
}

function ShowNewsList(){
  global $conn,$sql_count,$sql_query;
  $page_size=20;
  $total_records=$conn->query('select count(*) '.$sql_count,PDO::FETCH_NUM)->fetchColumn(0); 
  if(empty($total_records)){
    echo '<br><p align="center">没有相关记录！</p>'; 
    return false;
  }
  $total_pages=(int)(($total_records+$page_size-1)/$page_size);
  $page=@$_GET['page'];
  if(is_numeric($page)){
    if($page<1)$page=1;
    else if($page>$total_pages)$page=$total_pages;
  }else $page=1;
  $res=$conn->query($sql_query." limit ".($page_size*($page-1)).",$page_size",PDO::FETCH_ASSOC); 
  echo '<TABLE WIDTH="96%" BORDER="0" align="center" CELLPADDING="5" CELLSPACING="1" bgcolor="#f2f2f2">
   <TR ALIGN="center" bgcolor="#f7f7f7" height=30> 
     <TD width="65%"><strong>文章主题</strong></TD>
     <TD width="35%"><strong>发布时间</strong></TD>
   </TR>';
  foreach($res as $row){
    echo '<TR bgcolor="#FFFFFF" height=25  onmouseover="this.style.backgroundColor=\'#f2f2f2\'; this.style.color=\'#ff0000\' " onmouseout="this.style.backgroundColor=\'\';this.style.color=\'\'"><TD> &nbsp; <a href="/news/news'.$row['id'].'.htm" target="_blank">'.$row['title'].'</a></TD><TD align="center" >'.date('Y-m-d H:i:s',$row['addtime']).'</TD></TR>'; 
  }
  echo '<tr bgcolor="#FFFFFF"><td colspan="2" align="center" height="30"><form style="margin:0px">共 <b>'.$total_records.'</b> 篇文章';
  if($page==1) echo '首页 上一页';else echo '<a href="'.GenPageUrl(1).'"  onclick="return JumpLinks(this)">首页</a> <a href="'.GenPageUrl($page-1).'"  onclick="return JumpLinks(this)">上一页</a>';
  echo '&nbsp;';
  if($page==$total_pages) echo '下一页 尾页';else echo '<a href="'.GenPageUrl($page+1).'" onclick="return JumpLinks(this)">下一页</a> <a href="'.GenPageUrl($total_pages).'" onclick="return JumpLinks(this)">尾页</a>'; 
  echo ' 页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页 <b>'.$page_size.'</b>篇文章/页';
  echo ' 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="JumpToPage(this.form.page.value);"></form></td></tr></TABLE>';
}


if(@$_POST['action']=='get'){
  ShowNewsList();
  CloseDB();
  exit(0);
}

$Pagination='5';
$KeywordsArray=array('进口','品牌','韩国','日本','欧美','网络热销','热卖','香薰精油','香水','江苏','安徽','浙江','山东','南京','广州','上海','温州','苏州','无锡','常州','镇江','南通','扬州','淮安');
$MyKeyword=$KeywordsArray[$id % count($KeywordsArray)].'化妆品批发';
$PageKeywords='化妆品,化妆品批发,南京化妆品批发,韩国化妆品批发,进口化妆品批发,品牌化妆品批发,欧美化妆品批发,'.$MyKeyword;
$PageDescription='南京涵若铭妆主要提供各种品牌化妆品批发,韩国化妆品批发,进口批妆品批发,欧美化妆品批发等业务,地区代理'.$MyKeyword;
$PageTitle=$newsTitle.'－'.$MyKeyword.'地区代理|涵若铭妆化妆品公司';
include('include/page_head.php');?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(/images/bg_mid.gif) repeat-x;">
<TR>
   <TD height="40" valign="middle">&nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; <a href="/article.htm">新闻资讯</a><?php if($newsproperty>0) echo ' &gt;&gt; <a href="/news.php?cid='.$newsproperty.'">'.$SubTopic.'</a>';?></TD>
</TR>
<TR>
   <TD id="contentbox" align="top" style="padding-bottom:15px"><?php
   if($id>0){?>
    <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td HEIGHT="50" align="center">
      	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
           <td height="40" align="center" style="color:#FF6600;font-size:16pt;font-weight:bold"><?php echo $newsTitle;?></td>
        </tr>
        </table>

        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
           <td height="15" align="right" style="padding-right:20px;color:#8f8f8f">发布时间：<?php echo date('Y-m-d H:i:s',@$newsAddTime);?></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="200" valign="top" style="font-size:11pt"><?php echo @$newsContent;?></td>
    </tr>
    </table><?php
  }  
  else{
    echo ShowNewsList();
  }?></td>
</TR>
</TABLE>
<script>
  function JumpToPage(page){
    var params="?cid=<?php echo $newsproperty;?>&page="+page;
    AsyncPost("action=get","/news.php"+params,"contentbox");
    document.body.scrollTop=0;	
  }
  function JumpLinks(alink){
    AsyncPost("action=get",alink.href,"contentbox");	 
    document.body.scrollTop=0;	
    return false;
  }
</script><?php
include('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
