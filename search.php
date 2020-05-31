<?php require('include/conn.php');

if(@$_POST['action']=='get'){
  $searchkey=trim(@$_GET['key']);
  $cid=@$_GET['cid'];
  $searchmode= @$_GET['mode'];
  if(!is_numeric($cid)) $cid=0;
  if(!is_numeric($searchmode)) $searchmode=0;
  db_open();
  ShowSearchResult();
  db_close();
  exit(0);
}

function GenPageUrl($page){
  global $cid,$searchmode,$searchkey;
  $url='/search.htm?page='.$page;
  if($cid) $url.='&cid='.$cid;
  if($searchmode) $url.='&mode='.$searchmode;	
  if($searchkey) $url.='&key='.rawurlencode($searchkey);	
  return $url;
}
 
function ShowSearchResult(){
  global $CatList,$cid,$searchmode,$searchkey;
 echo '<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0" align="center"><TR><TD HEIGHT="40" style="background:url(/images/kubars/kubar_research.gif);text-align:center">';
  if(empty($searchkey)){
     echo '对不起，请您输入查询关键字!</td></tr></table>';
     return false;
  }
  if($searchmode=='2'){#商品条码
    echo '<b>商品条码</b>';
    if(!is_numeric($searchkey)) $searchkey='?';
    $sql_count='from `mg_product` where recommend>0 and barcode=\''.$searchkey.'\'';
    $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count;
  }
  else if($searchmode=='1'){#商品编号
    echo '<b>商品编号</b>';
    if(!is_numeric($searchkey)) $searchkey='0';
    $sql_count='from `mg_product` where recommend>0 and id='.$searchkey;
    $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count;
  }
  else{ #商品名称
    echo '<b>商品名称</b>';
    $sql_count='from `mg_product` where recommend>0';
    if($searchkey){
      $searchkey=FilterText($searchkey);
      if(strstr($searchkey,' ')){
        $key_list=explode(' ',$searchkey);
        foreach($key_list as $subkey){
          if($subkey) $sql_count.=' and name like \'%'.$subkey.'%\'';
        }
      }
      else{
        $sql_count.=' and name like \'%'.$searchkey.'%\'';
      }
      if($cid>1){ #高级查询
        $sql_count.=' and cids like \'%,'.$cid.',%\'';
      }
    }
    $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by price3';
  }
  echo '搜索，您查询的关健字是：<font color=red>'.$searchkey.'</font></TD></tr></table>';
  include('include/m_warelist.php');
  $content=GenWareList($sql_count,$sql_query,$MAX_PER_PAGE=20,@GenPageUrl,$dynamicLoad=true);
  if(empty($content))$content='<br><p align="center" style="font-size:16px;margin-top:50px;color:#FF0000;"><img src="/images/nofound.gif"><br>对不起，没有搜索到你要找的商品！</p>';  
  echo $content;
}


$PageKeywords='产品搜索,化妆品,化妆品批发,韩国化妆品批发,上海化妆品批发,欧美化妆品批发,进口化妆品批发,品牌化妆品批发,南京化妆品批发';
$PageDescription='这里显示化妆品产品搜索,南京涵若铭妆提供各种进口化妆品批发,欧美化妆品批发,韩国化妆品批发,上海化妆品批发,等品牌化妆品批发零售业务';
$PageTitle='产品搜索-涵若铭妆-韩国化妆品批发|进口化妆品批发';
include('include/page_head.php');?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<tr>
   <td width="200" valign="TOP">
      <TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" border="0" style="background:url(images/bg_left.gif) repeat-y;margin-top:30px;">
      <tr>
	<td height="1%">
        <!-----导航:商品分类 开始------> 
        <SCRIPT language="JavaScript" src="include/category.js"></SCRIPT>
        <SCRIPT language="JavaScript" src="include/guide_sort.js" type="text/javascript"></SCRIPT>
        <!-----导航:商品分类 结束------>   
      </td></tr><tr><td height="99%"><?php 
       include('include/guide_blank.php'); # 空白导航 
      ?>
      </td></tr>
    </table>    
     <script>var page=1,searchmode,searchcat,searchkey=htmRequest("key");if(searchkey){searchmode=htmRequest("mode");searchcat=htmRequest("cid");page=htmRequest("page");}</script>
  </td>
  <td valign="top" width="800" height="100%">
     <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
     <TR>
        <TD height="40" valign="middle">
          &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<%=WebRoot%>#">首页</a> &gt;&gt; 产品搜索
        </TD>
      </TR>
      <TR>
        <TD id="ContentBox" valign="top">
        	 <!-------------------------------------->
        	 <table width="100%" border="0" cellpadding="0" cellspacing="0">
        	 <tr><TD HEIGHT="40" style="background:url(/images/kubars/kubar_research.gif);text-align:center"></TD></tr>
        	 <tr><td align="center">
           	   <table width="96%" border="0" align="center" cellpadding="4" cellspacing="1">
               <tr><form name="prosearch" onsubmit="return check_search(this)">
                 <td width="40%" height="25" align="right">关 健 字：</td>
                 <td width="60%" height="25"><input name="searchkey" type="text" class="input_sr" size="18" maxlength="15"></td>
               </tr>
               <tr>
                 <td height="25" align="right">查找方式：</td>
                 <td height="25"><select name="searchmode">
                   <option value="0" selected>商品名称</option>
						       <option value="1">商品编号</option>
                   <option value="2">商品条码</option>
                   </select></td>
               </tr>
               <tr>
                 <td height="25" align="right">商品分类：</td>
                 <td height="25"><script language="javascript">if(!searchkey)CreateCategorySelection("category","0","所有分类","");</script></td>
               </tr>
               <tr BGCOLOR=ffffff>
                  <td height="25">&nbsp;</td>
                  <td height="25" align="left" valign="bottom"><input type="submit" class="input_bot" value="查  找"></td>
               </tr></form>
               </table>
           </tr></table>
           <!-------------------------------------->
        </TD>
      </TR>
    </TABLE>    
  </td>
</tr>
<tr>
   <td height="5"><div id="ProductTipLayer" style="display:none;POSITION:absolute;z-index:1000;"></div></td>
</tr>	
</table>
<script>
function JumpToPage(page){ 
  var params="?mode="+searchmode+"&cid="+searchcat+"&key="+searchkey+"&page="+page;
  AsyncPost("action=get","search.php"+params,"ContentBox");
  document.body.scrollTop=0;
}
function JumpLinks(alink){
  var params="?"+GetUrlQuery(alink.href);
  AsyncPost("action=get","search.php"+params,"ContentBox");	 
  document.body.scrollTop=0;
  return false;
}
if(searchkey && topsearch){
  topsearch.searchkey.value=decodeURIComponent(searchkey);
  topsearch.searchkey.focus();
  if(searchmode>0)topsearch.searchmode.selectedIndex=searchmode; 
  JumpToPage(page);
}else InitSearchForm(prosearch);
</script><?php
include('include/page_bottom.htm');
db_close();?>
</body>
</html>
