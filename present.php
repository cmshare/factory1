<?php require('include/conn.php');
OpenDB();
   
if(@$_POST['action']=='get'){
  ShowPresent();
  CloseDB();
  exit(0);
}
   
function ShowPresent(){
  global $page,$total_pages,$total_records;
  $page_size=15;
  $res=page_query('select `mg_product`.id,`mg_product`.name,`mg_product`.stock0,`mg_present`.available,`mg_present`.score,`mg_present`.remark','from `mg_product` inner join `mg_present` on `mg_product`.id=`mg_present`.productid','','order by `mg_present`.addtime desc',$page_size);
  if(empty($total_records)){
     echo '<br><p align="center">没有相关记录！</p>'; 
     return false;
  }
  echo '<TABLE cellSpacing=0 cellPadding=0 width="96%" align="center" border="0"><tr>';
  $jishu=0;
  foreach($res as $row){
     $PresentAvailable=($row['available']>$row['stock0'])?$row['stock0']:$row['available'];
    if($jishu>0 && ($jishu%3==0)) echo '</tr><tr><td height=15> </td></tr><tr>';?>
      <td width="33%" valign="top">
      <table border=0 cellspacing=0 cellpadding=0 valign="middle">
      <tr>
        <td width="140" align="center"><img border=0 src="images/pic_border.gif"><p style="MARGIN-TOP: -133px;MARGIN-left:0px"><a href="product.php?pid=<?php echo $row['id'];?>" target="_blank"><img  border=0 width=125 height=125 src="<?php echo product_pic($row['id'],0);?>" oncontextmenu="return(false);"></a></td>
        <td height="100%">
      	   <table  border=0 height="100%" cellspacing=0 cellpadding=0 valign="top" class="presentshow">
           <tr>
              <td valign="top">&nbsp;<a href="product.php?pid=<?php echo $row['id'];?>" target="_blank"><b><font color="#E26217"><?php echo $row['name'];?></font></b></a></td>
           </tr>
           <tr>
               <td>&nbsp;【编&nbsp; 号】 <?php echo substr("0000".$row['id'],-5);?></td>
           </tr>
           <tr>
               <td>&nbsp;【兑购分】 <b><font color=red><?php echo $row['score'];?>分</font></b></td>
           </tr>
           <tr>
               <td>&nbsp;【还剩下】 <b><font color=red><?php echo $PresentAvailable;?>件</font></b></td>
           </tr>
           <tr>
               <td><div><nobr>&nbsp;【备&nbsp; 注】<?php echo ($row['remark'])?$row['remark']:'无';?></nobr></div></td>
           </tr>
           </table> 
         </td>
      </tr>
      </table>
    </td>
    <td width="1%"></td><?php
    $jishu++;
  }
  echo '</tr></TABLE><br>';
  echo '<TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0"><TR><TD align="center">共 <b>'.$total_records.'</b> 件商品&nbsp;&nbsp;';
  if($page==1) echo '首页&nbsp;上一页';else echo '<a href="present.php?page=1" onclick="return JumpLinks(this)">首页</a>&nbsp;<a href="present.php?page='.($page-1).'" onclick="return JumpLinks(this)">上一页</a>';
  echo '&nbsp;';
  if($page==$total_pages) echo '下一页&nbsp;尾页';else echo '<a href="present.php?page='.($page+1).'" onclick="return JumpLinks(this)">下一页</a>&nbsp;<a href="present.asp?page='.$total_pages.'>" onclick="return JumpLinks(this)">尾页</a>';
  echo '&nbsp; 页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp; 每页<b>'.$page_size.'</b>件商品&nbsp;&nbsp; 转到：<select onchange="JumpToPage(this.value)" size="1" name="page">';
  for($i=1;$i<=$total_pages;$i++){
    echo '<option value="'.$i.'"';
    if($i==$page) echo ' selected';
    echo '>第'.$i.'页</option>';
  }
  echo '</select></TD></tr></TABLE>';
}

$Pagination='4';
$PageKeywords='化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,南京化妆品批发,品牌化妆品批发,化妆品批发网,化妆品批发市场,欧美化妆品批发';
$PageDescription='这里是赠品兑换区,南京涵若铭妆化妆品批发网经销各种进口化妆品批发,欧美化妆品批发,韩国化妆品批发,日本化妆品批发等品牌化妆品批发零售业务，并入驻各大化妆品批发市场';
$PageTitle='赠品兑换-韩国化妆品批发|进口化妆品批发 －【涵若铭妆】南京化妆品批发网|欧美品牌化妆品批发市场';
include("include/page_head.php");?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<TR>
   <TD height="35">
   &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; <b>赠品兑购区</b>
   </TD>
</TR>
<TR>
   <TD id="contentbox"><?php echo ShowPresent();?></TD>
</TR>
<TR>
   <TD height="56" style="BACKGROUND-IMAGE:url(images/presentresume.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center"></TD>
</TR>
<style> .presentshow div{width:176px; height:18px; overflow:hidden; text-overflow:ellipsis} </style>
<TR>
   <TD style="padding-top:8px;padding-bottom:20px;BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc;BACKGROUND-POSITION: left bottom; BACKGROUND-IMAGE:url(/images/clientbot.jpg); BACKGROUND-REPEAT: repeat-x;"><?php echo $conn->query('select aboutpresent from mg_configs')->fetchColumn(0);?></TD>
</TR>
</TABLE>
 
<script>
  function JumpToPage(page){
    AsyncPost("action=get","present.php?page="+page,"contentbox");
  }
  function JumpLinks(alink) {
    AsyncPost("action=get",alink.href,"contentbox");
    return false;
  }
  var cur_page=htmRequest("page");
  if(cur_page!="" && cur_page!="0" && cur_page!="1"){
     JumpToPage(cur_page);
  }
</script>
<?php
  include("include/page_bottom.htm");
  CloseDB();
?>
</body>
</html> 
