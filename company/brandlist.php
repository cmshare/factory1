<?php require('include/conn.php');
   
$cid=@$_GET['cid'];
if(!is_numeric($cid))$cid=0;

$mode=@$_GET['mode'];
if(!is_numeric($mode))$mode=0;
 
OpenDB();
function brand_sort($selec){
  global $conn,$brandlist,$UnfoldBrand;
  if($selec==0)$sql='select id from `mg_category` where recommend>1 order by recommend';#热销品牌
  else $sql='select id from `mg_category` where parent='.$selec.' order by sortorder';
  $res=$conn->query($sql,PDO::FETCH_ASSOC);
  foreach($res as $row){
    $brandlist.=', '.$row['id'];
    if(empty($UnfoldBrand)) $UnfoldBrand=$selec;
    brand_sort($row['id']);
  }
}

#先遍历子类 	
$brandlist=$cid;
brand_sort($cid);

  
#再遍历父类     
if($cid==0)$LinkSortGuider='&nbsp;&gt;&gt;&nbsp;热销品牌'; 
else{
  $LinkSortGuider=''; 
  $PID = $cid;
  while($PID){
    $row=$conn->query('select id,title,parent from `mg_category` where id='.$PID,PDO::FETCH_ASSOC)->fetch();
      if(empty($row)){
    	echo '<script LANGUAGE="javascript">alert("您输入的参数非法，请正确操作！");history.go(-1);</script>';
        CloseDB();
        exit(0);
      }
      $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="brandlist.htm?cid='.$row['id'].'">'.$row['title'].'</a>'.$LinkSortGuider;
       if(empty($MyPageTitle)) $MyPageTitle = $row['title'].'-';
     
      if($PID==$UnfoldBrand) $ParentBrand=$row['parent'];
      $PID = $row['parent'];
      if(empty($UnfoldBrand))$UnfoldBrand=$PID;
  }
} 
 
if(@$_POST['action']=='get'){
  ShowWareList();
  CloseDB();
  exit(0);
}

 
function ShowWareList(){
  global $conn,$mode,$brandlist,$LinkSortGuider;
  if($mode==0){
    $sql_count='from `mg_product` where brand in ('.$brandlist.') and  recommend>0'; 
    $sql_query='select id,name,spec,stock0,price1,price2 '.$sql_count.' order by addtime desc';
  }
  else if($mode==1){
    $sql_count='from `mg_product` as a, `mg_ordergoods` as b, `mg_orders` as c where a.id=b.productid and  b.ordername=c.ordername and c.state>3 and c.actiontime > unix_timestamp()-30*24*60*60 and a.brand in ('.$brandlist.') and a.recommend>0';
    $sql_query='select a.id,a.name,a.spec,a.stock0,a.price0,a.price1,a.price3,a.onsale '.$sql_count.' group by a.id order by sum(b.amount) desc, a.recommend desc';
  }
  else{
    $sql_count='from `mg_product` where brand in ('.$brandlist.') and  recommend>0';
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
  $url='brandlist.htm?page='.$page;
  if($cid) $url.='&cid='.$cid;   
  if($mode)$url.='&mode='.$mode;
  return $url;
}

$PageTitle='品牌分类-化妆品批发-南京铭悦日化用品有限公司';
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="1"></TD></TR>	
<TR valign="top">
  <TD background="images/client_bg_left.jpg" width=190" height="100%">
    <TABLE cellSpacing="0" cellPadding="0" width="190" height="100%" border="0">
    <tr>
       <td height="1%">
        <!-----导航:商品分类 开始------> 
        <script src="include/guide_sort.js"></script>  
        <!-----导航:商品分类 结束------>   
      </td></tr><tr><td height="99%" background="images/left_bg.gif"> 
      </td></tr>
    </table> 
  </TD>
     <TD width="810"  style="BORDER-right:#FF67A0 1px solid;">
     <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0">
     <TR>
        <TD width="100%" height="28" valign="middle"  background="images/pdbg01.gif" style="padding-left:28px">
           <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"  border="0">
           <TR>
              <TD width="410"><div class="LinkSortGuider"><NOBR><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：<a href=".">主頁</a><span id="linkguider"><?php echo $LinkSortGuider;?></span></NOBR></div></TD>
               <TD nowrap align="right"><form name="topsearch" onsubmit="return check_search(this)" style="margin:0px">
               <img src="images/searchico.gif" width="23" height="23" align="absMiddle">
      	<select name="searchmode">
          <option value="0" selected>商品名称</option>
          <option value="1">商品编号</option>
          <option value="2">商品条码</option>
        </select><input type="hidden" name="category" value="0">
        <input name="searchkey" type="text" size="15" maxlength="50"><input type="submit"  value="查询"><font size="4" color="#8f8f8f">|</font><input type="button"  value="高级搜索" onClick="check_search(null)">
               </form></TD>
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
</TABLE><div id="ProductTipLayer" style="display:none;"></div>
<script>
var sortmode=htmRequest("mode"),page=htmRequest("page"),cid=htmRequest("cid");
function OnGetContent(ret)
{ var pbox=document.getElementById("contentbox");
  if(pbox && ret)
  { pbox.innerHTML=ret;
    document.body.scrollTop=0;
    pbox=document.getElementById("wareshow");
    if(pbox)
    { var linkguider=pbox.getAttribute("linkguider");
      if(linkguider)
      { pbox=document.getElementById("linkguider");
  	if(pbox)pbox.innerHTML=linkguider;
      }
    }
  }
}
function JumpToPage(page)
{ var params="?cid="+cid+"&mode="+sortmode+"&page="+page;
  AsyncPost("action=get","brandlist.php"+params,OnGetContent);	
}
function JumpLinks(alink)
{ AsyncPost("action=get","brandlist.php?"+GetUrlQuery(alink.href),OnGetContent);	
  return false;
}
function ChangeSort(sortindex)
{ sortmode=sortindex;
  JumpToPage(1); 
}
JumpToPage(page);  
</script><?php
require('include/page_bottom.htm');
CloseDB();?>
</body>
</html> 
