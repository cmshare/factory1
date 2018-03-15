<?php require('include/conn.php');
OpenDB();
   
if(@$_POST['action']=='get'){
  ShowWareList(true);
  CloseDB();
  exit(0);
}

function GenPageUrl($page){
 return 'promotion.php?page='.$page;
}

function ShowWareList($dynamicLoad){
  $page_size=12;
  $sql_count='from `mg_product` where (onsale&0xf)>0 and recommend>0 and onsale>unix_timestamp()-30*24*60*60';
  $sql_query='select id,name,onsale,spec,stock0,price0,price1,price3 '.$sql_count.' order by (onsale&0xf) desc,(onsale&~0xf) asc';
  $total_records=$GLOBALS['conn']->query('select count(*) '.$sql_count,PDO::FETCH_NUM)->fetchColumn(0); 
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
  $res=$GLOBALS['conn']->query($sql_query.' limit '.($page_size*($page-1)).','.$page_size,PDO::FETCH_ASSOC); 
  $id_count=0;
  echo '<TABLE cellSpacing=0 cellPadding=0 width="800" align="center" border="0" id="prolist"><tr>';
  foreach($res as $row){
    if($id_count>0 && $id_count % 2==0) echo '</tr><tr>';?>
    <td width="400">
    	<TABLE width="400" height="200" cellSpacing=0 cellPadding=0 align="center" border="0" deadline="<?php echo $row['onsale']&~0xf;?>" class="promotion">
      <tr><td width="25" rowspan="5"></td><td width="133" height="60"><div class="price_tj">￥<?php echo round($row['price0'],2);?>元</div></td><td width="62" class="qiangou"><a href="javascript:AddToCart(<?php echo $row['id'];?>)">抢购</a></td><td width="180" height="180" rowspan="4" align="center"><a href="/products/<?php echo $row['id'];?>.htm"><img width="160" height="160" alt="<?php echo $row['name'];?>" border="0" onmouseover="ProductTip(this)" src="<?php echo product_pic($row['id'],0);?>" spec="<?php echo $row['spec'];?>" <?php if($dynamicLoad) echo 'stoc="'.$row['stock0'].'"';?>></a></td></tr>
      <tr><td height="50" colspan="2">&nbsp; <strike>市场价：￥<?php echo round($row['price1'],2);?>元</strike><br>&nbsp; <font color=#55AA66><strike>批发价：￥<?php echo round($row['price3'],2);?>元</strike></font></td></tr>
      <tr><td height="35" colspan="2">&nbsp; 距活动结束<img src="/images/time1.png" width="16" height="16" style="margin-bottom:-4px"><font id="life<?php echo $row['id'];?>">00天00时00分00秒</font></td></tr>
      <tr><td height="35" colspan="2"></td></tr>
      <tr height="20"><td colspan="3"><div class="name_tj"><a href="/products/<?php echo $row['id'];?>.htm"><NOBR><?php echo $row['name'];?></NOBR></a></div></td></tr>
      </TABLE>
    </td><?php
    $id_count++;
  }?>
  </tr>
  </TABLE>
  <TABLE cellSpacing=0 cellPadding=0 width="100%" height="50" border="0">
  <TR><TD align="center"><form style="margin:0px"><?php
  echo '共 <b>'.$total_records.'</b> 件商品&nbsp;&nbsp;';
  if($page==1) echo '首页&nbsp;上一页';else echo '<a href="'.GenPageUrl(1).'" onclick="return JumpLinks(this)">首页</a>&nbsp;<a href="'.GenPageUrl($page-1).'" onclick="return JumpLinks(this)">上一页</a>';
  echo '&nbsp;';
  if($page==$total_pages) echo '下一页&nbsp;尾页';else echo '<a href="'.GenPageUrl($page+1).'" onclick="return JumpLinks(this)">下一页</a>&nbsp;<a href="'.GenPageUrl($total_pages).'" onclick="return JumpLinks(this)">尾页</a>';
  echo '&nbsp;页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp; 每页<b>'.$page_size.'</b>件商品&nbsp;&nbsp; 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="JumpToPage(this.form.page.value)"></form></TD></tr></TABLE>';
}
$Pagination='3';
$MyPageTitle='限时秒杀';
$PageTitle='限时秒杀-特价化妆品批发-韩国化妆品批发-进口化妆品批发-涵若铭妆';
$PageKeywords=$MyPageTitle.',化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,南京化妆品批发,上海化妆品批发,化妆品批发网,最低价化妆品批发市场';
$PageDescription='涵若铭妆'.$MyPageTitle.'，涵若铭妆化妆品公司主要提供韩国化妆品批发，进口化妆品批发，品牌化妆品批发及零售业务，通过南京化妆品批发网及上海化妆品批发市场组建完善的网络销售平台，打造化妆品批发最低价。';
include("include/page_head.php");?>

<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<tr>
   <td width="200" valign="TOP">
      <TABLE width="100%" height="100%" border="0"  cellSpacing="0" cellPadding="0" style="background:url(images/bg_left.gif) repeat-y;margin-top:30px;">
      <tr>
         <td height="1%" align="center"><?php
         include('include/guide_brand.htm');  
         include('include/guide_category.htm');?> 
         </td></tr>
    <tr><td height="99%" style="background:url(/images/advs/ADVs_Blank.gif) repeat-y;"></td></tr>
    </table>    
  </td>
  <td valign="top" width="800" height="100%">
    <TABLE cellSpacing=0 cellPadding=0 width="800" height="100%" align="center" border="0">
    <TR>
      <TD width="800" height="40" valign="middle">
   	   &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a>  &gt;&gt; 特价促销 &gt;&gt; <?php echo $MyPageTitle;?></TD>
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
function OnGetContent(ret){
  var pbox=document.getElementById("contentbox");
  if(pbox && ret){
    pbox.innerHTML=ret;
    document.body.scrollTop=0;	
    run_timer();
  }
}
function JumpToPage(page){
  AsyncPost("action=get","promotion.php?page="+page,OnGetContent);
}
function JumpLinks(alink){
  AsyncPost("action=get",alink.href,OnGetContent);	 
  return false;
}
function run_timer(){
  var pbox=document.getElementById("prolist");
  if(pbox){
    var cols_length,rows_length=pbox.rows.length; 
    var i,j,deadline,lifeindex,obj;
    for(j=0;j<rows_length;j++)
    { cols_length=pbox.rows[j].cells.length; 
      for(i=0;i<cols_length;i++){
	 obj=pbox.rows[j].cells[i].children[0];
	 if(obj){
	   deadline=obj.getAttribute("deadline");
	   obj=obj.rows[2].cells[0].getElementsByTagName("font");
	   if(obj){
	     clock_lifetime2(obj[0].id,deadline);
	   }
	 }
       }
    }
  }
}
run_timer();
</script>
<?php
  include("include/page_bottom.htm");
  CloseDB();
?>
</body>
</html>
