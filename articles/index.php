<?php
include('conn_articles.php');
$articlestype=1;
db_open();
$id=@$_GET['id'];
if(is_numeric($id) && $id>0){
  $row=$conn->query("select * from `articles` where id=$id and property=$articlestype",PDO::FETCH_ASSOC)->fetch();
  if($row){
    $newsTitle=$row['title'];
    $newsLink=$row['link'];
    $newsContent=$row['content'];
    $newsAddTime=$row['addtime'];
    $NewsAuthor=$row['author'];
  }
  else{
    echo '您访问的内容不存在或者已经删除!';
    exit(0);
  }
}
else $id=0;

if (empty($newsTitle))  $newsTitle='网闻资讯';
$KeywordsArray=array('进口化妆品批发','品牌化妆品批发','韩国化妆品批发','日本化妆品批发','欧美化妆品批发','广东美容化妆品网','热卖化妆品','香薰精油批发','香水批发','江苏化妆品批发','安徽化妆品批发','浙江化妆品批发','山东化妆品批发','南京化妆品批发','广州化妆品批发','上海化妆品批发','温州化妆品批发','苏州化妆品批发','无锡化妆品批发','常州化妆品批发','镇江化妆品批发','南通化妆品批发','扬州化妆品批发','淮安化妆品批发"');
$HotKeyword=$KeywordsArray[$id % count($KeywordsArray)];
$PageKeywords='化妆品,化妆品批发,'.$HotKeyword.','.$newsTitle;
$PageDescription=$newsTitle.',涵若铭妆主要提供各种品牌化妆品批发,韩国化妆品批发,进口批妆品批发,欧美化妆品批发等业务,地区代理'.$HotKeyword;

$PageTitle=$newsTitle.'－'.$HotKeyword.'地区代理－涵若铭妆';
?><HTML>
<HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="<?php echo $PageKeywords;?>">
<META http-equiv="Description" content="<?php echo $PageDescription;?>">
<link href="/include/mycss.css" rel="stylesheet" type="text/css">
<title><?php echo $PageTitle;?></title>
</HEAD>
<body oncontextmenu="return isNaIMG(event)">
<SCRIPT language="JavaScript" src="/user/cmbase.js"></SCRIPT><SCRIPT language="JavaScript" src="/include/page_frame.js"></SCRIPT>

<TABLE cellSpacing="0" cellPadding="0" width="1000" align="center"  bgcolor="#FFFFFF" border="0">
 <tr>
	 <td width="190" valign="TOP">
	 	
	 	<TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" align="center" border="0">
	 	<tr>
	 		<td height="1%">
        <!-----导航:商品分类 开始------> 
        <SCRIPT language="JavaScript" src="/include/category.js"></SCRIPT>
        <SCRIPT language="JavaScript" src="/include/guide_sort.js" type="text/javascript"></SCRIPT>
        <!-----导航:商品分类 结束------>   
      </td></tr><tr><td height="99%"> 
        <!-----导航:空白导航 开始------> 
        <table border="0" width="190" height="100%" cellpadding="0" cellspacing="0" align="center"  style="BACKGROUND:#FFFFFF; BORDER-COLLAPSE: collapse; border:1px solid #cccccc;">
<tr>
 	 <td align=center height="100%">
     <table width="95%"  height="100%" border="0"  cellspacing="0" cellpadding="0">
     <tr>
    	  <td align="center" style="BACKGROUND-IMAGE:url(/images/advs/ADVs_Blank.gif);"> &nbsp;</td>
     </tr>
     </table>
   </td>
</tr>
</table>
    
        <!-----导航:空白导航 结束------>  
      </td></tr>
    </table>    
     
  </td>
  <td width="10"></td>
  
  <td valign="top" width="800" height="100%">
  	<TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
      <TR>
        <TD width="800" height="25" valign="bottom" style="BACKGROUND-IMAGE:url(/images/ppbar3.gif); BACKGROUND-REPEAT: no-repeat;">
        &nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="/#">首页</a> &gt;&gt; <a href=".">文章资讯</a>
        </TD>
      </TR>
      <TR>
        <TD valign="top" style="padding:20px;BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc;BACKGROUND-POSITION: left bottom; BACKGROUND-IMAGE:url(/images/clientbot.jpg); BACKGROUND-REPEAT: repeat-x">


<?php
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
           <td height="10" align="center" bgcolor="#f7f7f7"><hr width="98%"></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td height="200" valign="top"><?php
if($newsLink)
    echo '<br><br><p align=center>内容链接转向－－<a href="'.$newsLink.'"><font color="#0000FF">'.$newsTitle.'</font></a>...</p>';
else{

   function GetCurURL(){
     $strURL="http://".$_SERVER['SERVER_NAME'].str_replace('/index.php','/',$_SERVER['SCRIPT_NAME']);
     $strQuery=$_SERVER['QUERY_STRING'];
     if($strQuery) $strURL=$strURL.'?'.$strQuery; 
     return $strURL;
   }
   
   $textlength=strlen($newsContent);
   if($textlength>0){
     $firstblocklenth=round($textlength/2);
     $sep_list_array=array('</p>','</P>','<br>','<BR>','。','？','！',',','，','.','?');
     for($jishu=0;$jishu<count($sep_list_array);$jishu++){
       $newpos=strpos($newsContent,$sep_list_array[$jishu],$firstblocklenth);
       if($newpos>0){
       	 if($jishu<4) $firstblocklenth=$newpos-1;//在分割号前插入
       	 else $firstblocklenth=$newpos+strlen($sep_list_array[$jishu])-1;//在分割号后插入
         break;
       }
     } 
     echo substr($newsContent,0,$firstblocklenth);
     echo '由<a href="'.GetCurURL().'">'.$HotKeyword.'网</a>转载。';
     echo substr($newsContent,$firstblocklenth+1,$textlength-$firstblocklenth);
     echo '<br><font color="#FF0000">注：本文源自网络转载，所有权归原作者.如果有文字内容引发的敏感问题触犯了您的权益，请立即联系站长删除，谢谢！</font>';
   }


}?>
      </td>
    </tr>
    </table><?php
	
}
else{
  $MaxPerPage=30;
  $res=page_query('select *','from articles','where property='.$articlestype,'order by addtime desc',$MaxPerPage);
  if(empty($res)){
    echo '<br><p align="center">还没有文章!</p>';  
  }
  else{
    function GenPageURL($page){
      return '?page='.$page;
    }
    echo '<TABLE WIDTH="730" BORDER="0" align="center" CELLPADDING="5" CELLSPACING="1" bgcolor="#f2f2f2">
    <TR ALIGN="center" bgcolor="#f7f7f7" height=30><TD width="65%"><strong>文章主题</strong></TD><TD width="35%"><strong>发布时间</strong></TD>
    </TR>';
    foreach($res as $row){
      $link= ($row['link'])?$row['link']:'/articles/?id='.$row['id'];
      echo '<TR height=25 bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor=\'#f2f2f2\'; this.style.color=\'#ff0000\' " onmouseout="this.style.backgroundColor=\'\';this.style.color=\'\'">
      <TD> &nbsp;  &nbsp; <a href="'.$link.'">'.$row['title'].'</a></TD>
      <TD align="center">'.date('Y-m-d H:i:s',$row['addtime']).'</TD>
      </TR>';
    }
    echo '</table>
    <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
    <TR><form>
    <TD align="center">
    共 <b>'.$total_records.'</b> 条记录&nbsp;&nbsp;';
    if($page==1) echo '首页&nbsp;上一页 ';
    else echo '<a href="'.GenPageURL(1).'" target="_self">首页</a>&nbsp;<a href="'.GenPageURL($page-1).'" target="_self">上一页</a> ';
    if($page==$total_pages) echo '下一页&nbsp;尾页 ';
    else echo '<a href="'.GenPageURL($page+1).'" target="_self">下一页</a>&nbsp;<a href="'.GenPageURL($total_pages).'" target="_self">尾页</a>&nbsp;';
    echo '页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp; 每页<b>'.$MaxPerPage.'</b>条记录&nbsp;&nbsp;
    转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页
    &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="self.location.href=\'?page=\'+this.form.page.value;">
    </TD></form>
    </tr>
    </TABLE>';

  }

} 
?>    
         <br>
         
        </TD>
      </TR>
    </TABLE>    
  </td>
</tr>
<tr>
	<td height="5"></td>
</tr>	
</table>
 
<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0" id="MyPageBottom" class="NavBotTable">
	<tr height="22" align="center" class="menu_bar">
		<td width="100%"><A href="/">站点首页</A> | <A href="/help/">常见问题</A> | <A href="/help/help27.htm">批发规则</A> | <A href="/help/help11.htm">购物流程</A> | <A href="/help/help6.htm">付款方式</A> |&nbsp;<A href="/usrmgr.htm?action=payonline">在线支付</A> | <A href="/help/help16.htm">配送问题</A> |&nbsp;<A href="/help/help8.htm">售后服务</A> | <A href="/news/">商城新闻</A> | <A href="/products/">产品清单</A> |&nbsp;<A href="/newarrival.php">最新到货</A>&nbsp;| <A href="/help/help15.htm">联系我们</A>&nbsp;| <A href="/help/help10.htm">关于我们</A></td>
	</tr>
	<tr align="center" height="20" valign="bottom">
		<td width="100%">  	&copy; 2006~2011&nbsp; 涵若铭妆 &nbsp;进口化妆品批发&nbsp;</td>
	</tr>
</table>
</body>
</html>
<?php
db_close();
?>
