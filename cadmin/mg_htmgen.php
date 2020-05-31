<?php header('content-type:text/html;charset=utf-8'); 
define('WEB_ROOT','/');

function HtmlUpdate($mode){
  switch($mode){
    case 'product': $productid=$_POST['id']; 
		    if($productid)PHP2HTML('product.php?id='.$productid,'products/'.$productid.'.htm');
		    break;            	
    case 'base':    PHP2HTML('main.php','index.htm'); 
		    PHP2HTML('sort.php','wares.htm');//新品上架
		    PHP2HTML('sort.php?cid=1','hotsell.htm');//销售排行
		    PHP2HTML('sort.php?cid=2','bargain.htm');//特价商品
		    PHP2HTML('promotion.php','promotion.htm');//限时秒杀
		    PHP2HTML('present.php','present.htm');
		    PHP2HTML('search.php','search.htm');	 		              	
		    PHP2HTML('news.php','article.htm');
		    PHP2HTML('help.php','help.htm');
		    PHP2HTML('usrmgr.php','usrmgr.htm');	
		    PHP2HTML('category.php','category/index.htm');
		    PHP2HTML('sitemap.php','sitemap.htm');  
		    echo '<iframe style="width:100%; height:20px;" scrolling="no"  Frameborder="no" marginwidth=0 marginheight=0 src="http://www.tellfun.com/shopping/htmupdate.php"></iframe>';
		   //echo '<iframe style="width:100%; height:20px;" scrolling=""no"  Frameborder="no" marginwidth=0 marginheight=0 src="http://www.tellfun.com/meray/htmupdate.asp"></iframe>';
		    break;
   case 'main':     PHP2HTML('main.php','index.htm');break; 
   case 'sysupdate':PHP2HTML('include/page_bottom.php','include/page_bottom.htm');break; 
   case 'category':    $cid=@$_POST['id']; 
		    if(is_numeric($cid))PHP2HTML('category.php?id='.$cid,'category/cat'.$cid.'.htm');
		    break;
   case 'help':     $helpid=$_POST['id'];
		    if($helpid)PHP2HTML('help.php?id='.$helpid,'help/help'.$helpid.'.htm');
		    break;
   case 'news':     $newsid=$_POST['id'];
		    if($newsid)PHP2HTML('news.php?id='.$newsid,'news/news'.$newsid.'.htm');
		    break;

   case 'guide_category':
           PHP2HTML('include/htm_guide_sort.php?mode=0','include/category.js');
           PHP2HTML('include/htm_guide_sort.php?mode=1','include/guide_category.htm');
           PHP2HTML('include/htm_guide_sort.php?mode=2','include/guide_catsort.htm');
           PHP2HTML('include/htm_guide_sort.php?mode=3','include/wx_sync_category.json');
		       break;	  
  }
}

function PHP2HTML($url,$savefile){
  global $noerror,$url_base;
  if($noerror || is_null($noerror)){
    echo 'Updating '.$url;
    $content=file_get_contents($url_base.$url);
    if($content){
      $savepath=WEB_ROOT.$savefile;
      if(substr($savepath,0,1)=='/') $savepath=$_SERVER['DOCUMENT_ROOT'].$savepath;
      else $savepath=getcwd().'/'.$savepath;
      $content=str_replace("\r\n","\n",$content);//dos2unix
      $noerror=file_put_contents($savepath,$content);    
    }
    else $noerror=false;
    echo ' &nbsp; ==> <font color="#FF0000">['.($noerror?'OK':'Error').']</font><br>';
  }
}


if(($mode=@$_GET['mode'])){
  echo '开始更新页面 ...<br>'; 
  $url_base='http://'.$_SERVER['SERVER_NAME'].(($_SERVER['SERVER_PORT']=='80')?'':':'.$_SERVER['SERVER_PORT']).WEB_ROOT;
  $noerror=null;
  HtmlUpdate($mode);
  if($noerror) echo '...<font color=red>更新完成！</font>';  
  else echo '...<font color=red>失败！</font>';
}?>
