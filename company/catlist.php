<?php require('include/conn.php');
$cid=@$_GET['cid'];
if(!is_numeric($cid))$cid=0;
$mode=@$_GET['mode'];
if(!is_numeric($mode))$mode=0;
OpenDB();

function cat_sort($selec){
  global $conn,$catlist,$UnfoldCategory;
  $res=$conn->query('select id from `mg_sort` where parent='.$selec.' order by sortorder',PDO::FETCH_NUM);
  foreach($res as $row){
    $catlist .= ','.$row[0];
    if(empty($UnfoldCategory)) $UnfoldCategory=$selec;
    cat_sort($row[0]);
  }
}
 
#先遍历子类 	
$catlist=$cid;
cat_sort($cid);
  
#再遍历父类     
$LinkSortGuider='';
$PID=$cid;
while($PID){
  $row=$conn->query('select id,title,parent from `mg_sort` where id='.$PID,PDO::FETCH_ASSOC)->fetch();
  if($row){
    $LinkSortGuider='&nbsp;&gt;&gt;&nbsp;<a href="catlist.htm?cid='.$row['id'].'">'.$row['title'].'</a>'.$LinkSortGuider;
    if(empty($MyPageTitle)) $MyPageTitle = $row['title'].'-';
    if($PID==$UnfoldCategory) $ParentCategory=$row['parent'];
    $PID = $row['parent'];
    if(empty($UnfoldCategory)) $UnfoldCategory=$PID;
  }
  else{
    echo '<script LANGUAGE="javascript">alert("您输入的参数非法，请正确操作！");history.go(-1);</script>';
    CloseDB();
    exit(0);
  }
}
if(empty($LinkSortGuider)) $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="catlist.htm">所有产品</a>';
 
    
if(@$_POST['action']=='get'){
  ShowWareList();
  CloseDB();
  exit(0);
}

function ShowWareList(){
  global $mode,$catlist,$LinkSortGuider;
  if($mode==0){
    $sql_count='from `mg_product` where category in ('.$catlist.') and recommend>0';
    $sql_query='select id,name,spec,stock0,price1,price2 '.$sql_count.' order by addtime desc';
  }
  else if($mode==1){
    $sql_count='from  `mg_product` as a, `mg_ordergoods` as b, `mg_orders` as c where a.id=b.productid and  b.ordername=c.ordername and c.state>3 and c.actiontime>unix_timestamp()-30*24*60*60 and a.category in ('.$catlist.') and a.recommend>0';
      $sql_query='select a.id,a.name,a.spec,a.stock0,a.price1,a.price2 '.$sql_count.' group by a.id order by sum(b.amount) desc, a.recommend desc';
  }
  else{
      $sql_count='from `mg_product` where category in ('.$catlist.') and recommend>0';
      $sql_query='select id,name,spec,stock0,price1,price2 '.$sql_count.' order by price2 asc';
  }
  include('include/m_warelist.php');
  $LinkSortGuider=FilterText($LinkSortGuider);
  $content=GenWareList($sql_count,$sql_query,$MAX_PER_PAGE=20,@GenPageUrl,$LinkSortGuider);
  if($content) echo $content;
  else echo '<br><p align="center" id="wareshow" linkguider="'.$LinkSortGuider.'">本类商品暂无记录！</p>';
} 
   
function GenPageUrl($page){
  global $cid,$mode;
  $url='catist.htm?page='.$page;
  if($cid)$url.='&cid='.$cid;
  else if($mode)$url.='&mode='.$mode;
  return $url;
}
   
$PageTitle='分类导航-化妆品批发-南京铭悦日化用品有限公司';
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="1"></TD></TR>	
<TR valign="top">
   <TD background="images/client_bg_left.jpg" width=190" height="100%">
      <TABLE cellSpacing="0" cellPadding="0" width="190" height="100%" border="0">
      <tr><td height="1%"><script src="include/guide_sort.js"></script></td></tr>
      <tr><td height="99%" background="images/left_bg.gif"></td></tr>
    </table> 
  </TD>
  <TD width="810"  style="BORDER-right:#FF67A0 1px solid;">
     <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0">
     <TR>
        <TD width="100%" height="28" valign="middle"  background="images/pdbg01.gif" style="padding-left:25px">
           <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"  border="0">
           <TR>
              <TD width="410"><div class="LinkSortGuider"><NOBR><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：<a href=".">主頁</a><span id="linkguider"><%=LinkSortGuider%></span></NOBR></div></TD>
               <TD nowrap align="right"><form name="topsearch" onsubmit="return check_search(this)" style="margin:0px"><img src="images/searchico.gif" width="23" height="23" align="absMiddle">
      	<select name="searchmode">
          <option value="0" selected>商品名称</option>
          <option value="1">商品编号</option>
          <option value="2">商品条码</option>
        </select><input type="hidden" name="category" value="0">
        <input name="searchkey" type="text" size="15" maxlength="50"><input type="submit"  value="查询"><font size="4" color="#8f8f8f">|</font><input type="button"  value="高级搜索" onClick="check_search(null)">
               	</TD></form>
           </TR>
           </TABLE>
        </TD>
     </TR>
     <TR>
        <TD valign="top" style="padding-top:15px;padding-bottom:20px;" id="contentbox"></td>
     </TR>
     </TABLE>	
   </TD>
</TR>
</TABLE>
<div id="ProductTipLayer" style="display:none;"></div>
<script>
var sortmode=htmRequest("mode"),page=htmRequest("page"),cid=htmRequest("cid");
function OnGetContent(ret){
  var pbox=document.getElementById("contentbox");
  if(pbox && ret){
    pbox.innerHTML=ret;
    document.body.scrollTop=0;
    pbox=document.getElementById("wareshow");
    if(pbox){
      var linkguider=pbox.getAttribute("linkguider");
      if(linkguider){
        pbox=document.getElementById("linkguider");
  	if(pbox)pbox.innerHTML=linkguider;
      }
    }
  }
}
function JumpToPage(page){ 
  var params="?cid="+cid+"&mode="+sortmode+"&page="+page;
  AsyncPost("action=get","catlist.php"+params,OnGetContent);	
}
function JumpLinks(alink){
  AsyncPost("action=get","catlist.php?"+GetUrlQuery(alink.href),OnGetContent);	
  return false;
}
function ChangeSort(sortindex){
  sortmode=sortindex;
  JumpToPage(1); 
}
JumpToPage(page);  
</script><?php
require('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
