<?php require('include/conn.php');
include('user/cmbase.php');

$url_base=get_location_base();
function PHP2HTML($url,$savefile){
  echo 'Update ==> '.$url.'<br>';
  return document_download($GLOBALS['url_base'].$url,$savefile);
}

$ret=TRUE;

echo '开始更新页面...<br>';  
if($ret) $ret=PHP2HTML('include/guide_sort.php','include/guide_sort.js');
if($ret) $ret=PHP2HTML('user/brandsel.php','user/brandsel.js');
if($ret) $ret=PHP2HTML('main.php','index.htm');
if($ret) $ret=PHP2HTML('brandlist.php','brandlist.htm');
if($ret) $ret=PHP2HTML('catlist.php','catlist.htm');	
if($ret) $ret=PHP2HTML('news.php','news.htm');
if($ret) $ret=PHP2HTML('help.php','help.htm');
if($ret) $ret=PHP2HTML('search.php','search.htm');		
if($ret) $ret=PHP2HTML('product.php','product.htm');	 		
if($ret) $ret=PHP2HTML('usrmgr.php','usrmgr.htm');	 		

echo ($ret)?'...<font color=red>更新完成！</font>':'...<font color=red>失败！</font>'; 
?>
