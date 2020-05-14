<?php require('include/conn.php');

$PageTitle='搜索结果－'.WEB_NAME.'－化妆品折扣商城';

OpenDB();
if(@$_POST['action']=='get'){
  $searchkey=trim(@$_GET['key']);
  $cid=@$_GET['cid'];
  $searchmode=@$_GET['mode'];
  if(!is_numeric($cid)) $cid=0;
  if(!is_numeric($searchmode)) $searchmode=0;
  ShowSearchResult();
  CloseDB();
  exit(0);
}

function sorts($selec){
  global $conn,$CatList;
  $res=$conn->query('select id from `mg_category` where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
  foreach($res as $row){
    $CatList .= ','.$row[0];
    sorts($row[0]);
  }
}

function ShowSearchResult(){
  global $CatList,$cid,$searchmode,$searchkey;
  echo '<TABLE WIDTH="96%" BORDER="0" CELLSPACING="0" CELLPADDING="0" align="center"><TR><TD HEIGHT="60" style="text-align:center;background-image:url(images/kubars/kubar_research.gif);BACKGROUND-POSITION:center center;BACKGROUND-REPEAT: no-repeat;">';
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
      if($cid>0){ #高级查询
        $CatList=$cid;
        sorts($cid);
        $sql_count.=' and brand in ('.$CatList.')';
      }
    }
    $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by price3';
  }
  echo '搜索，您查询的关健字是：<font color=red>'.$searchkey.'</font></TD></tr></table>';
  include('include/m_warelist.php');
  $content=GenWareList($sql_count,$sql_query,$MAX_PER_PAGE=20,GenPageUrl,NULL);
  if(empty($content))$content='<br><p align="center" style="font-size:16px;margin-top:50px;color:#FF0000;"><img src="images/nofound.gif"><br>对不起，没有搜索到你要找的商品！</p>';  
  echo $content;
}

function GenPageUrl($page){
  global $cid,$searchkey,$searchmode;
  $url='search.htm?page='.$page;
  if($cid)$url.='&cid='.$cid;
  if($searchmode)$url.='&mode='.$searchmode;
  if($searchkey)$url.='&key='.$searchkey;
  return $url;
}

$PageTitle='产品搜索-进口化妆品批发-南京铭悦日化用品有限公司';
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="1"></TD></TR>	
<TR valign="top">
  <TD background="images/client_bg_left.jpg" width=190" height="100%">
     <TABLE cellSpacing="0" cellPadding="0" width="190" height="100%" border="0">
     <tr><td height="1%"><SCRIPT language="JavaScript" src="include/guide_sort.js" type="text/javascript"></SCRIPT></td></tr>
     <tr><td height="99%" background="images/left_bg.gif"></td></tr>
    </table> 
  </TD>
  <SCRIPT language="JavaScript" src="user/brandsel.js"></SCRIPT>
  <script>var searchmode,searchcat,searchkey=htmRequest("key");if(searchkey){searchmode=htmRequest("mode");searchcat=htmRequest("cid");page=htmRequest("page");}</script>
  <TD width="810"  style="BORDER-right:#FF67A0 1px solid;">
     <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0">
     <TR>
        <TD width="100%" height="30" valign="middle"  background="images/pdbg01.gif" style="padding-left:25px">
           <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"  border="0">
           <TR>
              <TD><img src="images/arrow2.gif" width="6" height="7">&nbsp;您現在的位置：&nbsp;<a href=".">主頁</a> &gt;&gt; 产品搜索</TD>
              <TD nowrap align="right"><form name="topsearch" onsubmit="return check_search(this)" style="margin:0px">
        <img src="images/searchico.gif" width="23" height="23" align="absMiddle">
      	<select name="searchmode">
          <option value="0" selected>商品名称</option>
          <option value="1">商品编号</option>
          <option value="2">商品条码</option>
        </select><input type="hidden" name="category" value="0">
        <input name="searchkey" type="text" size="15" maxlength="50"><input type="submit"  value="查询"><font size="4" color="#8f8f8f">|</font><input type="button"  value="高级搜索" onClick="check_search(null)"></form></TD>
           </TR>
           </TABLE>
        </TD>
     </TR>
     <TR>
        <TD id="ContentBox" valign="top" style="padding-bottom:20px;">
        	 <!-------------------------------------->
        	 <table width="96%" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#FFFFFF">
        	 <tr><TD HEIGHT="60" style="text-align:center;background-image:url(images/kubars/kubar_research.gif);BACKGROUND-POSITION:center center;BACKGROUND-REPEAT: no-repeat;"></TD></tr>
        	 <tr><td align="center">
           	   <table width="96%" border="0" align="center" cellpadding="4" cellspacing="1">
               <tr><form name="prosearch" onsubmit="return check_search(this);">
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
                 <td height="25"><script language="javascript">if(!searchkey)CreateBrandSelection("category","0","所有分类","");</script></td>
               </tr>
               <tr>
                  <td height="25">&nbsp;</td>
                  <td height="25" align="left" valign="bottom"><input type="submit" class="input_bot" value="查  找"></td>
               </tr></form>
               </table>
           </tr></table>
           <!-------------------------------------->
       </TD>
     </tr>
     </TABLE>
  </TD>
</TR>
</TABLE>
<script>
function JumpToPage(page){ 
  var params="?mode="+searchmode+"&cid="+searchcat+"&key="+searchkey+"&page="+page;
  AsyncPost("action=get","search.php"+params,"ContentBox");
  document.body.scrollTop=0;
}
function JumpLinks(alink){
  AsyncPost("action=get","search.php?"+GetUrlQuery(alink.href),"ContentBox");     
  return false;
}
if(searchkey && topsearch){
  topsearch.searchkey.value=decodeURIComponent(searchkey);
  topsearch.searchkey.focus();
  if(searchmode>0)topsearch.searchmode.selectedIndex=searchmode; 
  JumpToPage(page);
}
else{
  InitSearchForm(prosearch);
}
</script><?php
require('include/page_bottom.php');
CloseDB();?>
</body>
</html> 
