<?php require('include/conn.php');
$cid=@$_GET['cid'];
$mode=@$_GET['mode'];
if(!is_numeric($cid))$cid=0;
if(!is_numeric($mode))$mode=0;
if($cid=='1'){
  $Pagination='2';
  $MyPageTitle='销售排行';
  $PageTitle='销售排行－ 韩国化妆品批发|进口化妆品批发|南京化妆品批发网|上海化妆品批发市场';       
  if($mode=='1') $days=7; #本周销售排行
  else if($mode=='2')$days=90;#季度销售排行
  else $days=30;#本月销售排行
  $sql_count='from `mg_product` as a, `mg_ordergoods` as b, `mg_orders` as c where a.id=b.productid and b.ordername=c.ordername and c.state>3 and c.actiontime>unix_timestamp()-'.strval($days*24*60*60);
  $sql_query='select  a.id,a.name,a.spec,a.stock0,a.price0,a.price1,a.price3,a.onsale '.$sql_count.' group by a.id order by sum(b.amount) desc,a.recommend desc';
}
else if($cid=='2'){
  $Pagination='3';
  $MyPageTitle='特价商品';
  $PageTitle='特价化妆品批发-韩国化妆品批发-进口化妆品批发-涵若铭妆';
  $sql_count='from `mg_product` where (onsale&0xf)>0 and recommend>0';
  $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by (onsale&0xf) desc,addtime desc';
}
else{
  $Pagination='1';
  $MyPageTitle='新品上架';
  $PageTitle='新品上架－涵若铭妆－南京进口化妆品批发网';
  $sql_count='from `mg_product` where recommend>0';
  $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by addtime desc';
}

OpenDB();

if(@$_POST['action']=='get'){ 
  ShowWareList(true);
  CloseDB();
  exit(0);
}

function GenPageUrl($page){
  global $cid,$mode;
  $url='sort.php?page='.$page;
  if($cid) $url.='&cid='.$cid;
  if($mode)$url.='&mode='.$mode;
  return $url;
}

function ShowWareList($dynamicLoad){
  global $sql_count,$sql_query;
  include('include/m_warelist.php');
  $content=GenWareList($sql_count,$sql_query,$MAX_PER_PAGE=20,@GenPageUrl,$dynamicLoad);
  if(empty($content))$content='<br><p align="center">本类商品暂无记录！</p>';
  echo $content;
}

$PageKeywords=$MyPageTitle.',化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,南京化妆品批发,上海化妆品批发,化妆品批发网,最低价化妆品批发市场';
$PageDescription='涵若铭妆'.$MyPageTitle.'，涵若铭妆化妆品公司主要提供韩国化妆品批发，进口化妆品批发，品牌化妆品批发及零售业务，通过南京化妆品批发网及上海化妆品批发市场组建完善的网络销售平台，打造化妆品批发最低价。';

include('include/page_head.php');?>

<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<tr>
  <td width="200" valign="TOP">
    <TABLE width="100%" height="100%" border="0"  cellSpacing="0" cellPadding="0" style="background:url(images/bg_left.gif) repeat-y;margin-top:30px;">
    <tr>
      <td height="1%" align="center"><?php  
        #导航:商品分类 开始 
        include('include/guide_brand.htm');
        include('include/guide_category.htm'); 
        #导航:商品分类 结束
        ?>  
      </td></tr>
    <tr><td height="99%" style="background:url(/images/advs/ADVs_Blank.gif) repeat-y;"></td></tr>
    </table>    
  </td>
  <td valign="top" width="800" height="100%">
    <TABLE cellSpacing=0 cellPadding=0 width="800" height="100%" align="center" border="0">
    <TR>
      <TD width="800" height="40" valign="middle"><?php
        if($Pagination!='2'){
   	  echo '&nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="'.WEB_ROOT.'#">首页</a> &gt;&gt; '.$MyPageTitle;
        }
        else{?>
       <TABLE cellSpacing=0 cellPadding=0 width="100%" height="20" border="0">
       <TR valign="bottom">
       	  <TD>&nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; <?php echo $MyPageTitle;?>（<font color=#FF0000>站内即时统计</font>）－销售量从高到底排列</TD>
       	  <TD align="right"><select name="sortsel" id="sortsel" style="color:#005500" onchange="ChangeSort(this.value)"><option value="1">本周统计</option><option value="0" <?php if($mode==0) echo 'selected';?>>本月统计</option><option  value="2" <?php if($mode==2) echo 'selected';?>>季度统计</option></select></TD><td width="18"></td>
       </TR>
       </TABLE><?php
       }?>
      </TD>
    </TR>
    <TR>
      <TD valign="top" id="contentbox"><?php ShowWareList(false);?></TD>
    </TR>
    </TABLE>
  </td>
</tr>
<tr>
  <td height="5"><div id="ProductTipLayer" style="display:none;"></div></td>
</tr>	
</table>
</div>
<script>
  var sortmode=<?php echo $mode;?>;
  function JumpToPage(page){ 
    var params="?cid=<?php echo $cid;?>&mode="+sortmode+"&page="+page;
    AsyncPost("action=get","sort.php"+params,"contentbox");
    document.body.scrollTop=0;	
  }
  function JumpLinks(alink){
    AsyncPost("action=get",alink.href,"contentbox");	 
    document.body.scrollTop=0;	
    return false;
  }
  function ChangeSort(sortindex){
    sortmode=sortindex;
    JumpToPage(1); 
  }
</script>
<?php
include('include/page_bottom.htm');
CloseDB();?>
</body>
</html> 
