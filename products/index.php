<?php require('../include/conn.php');
OpenDB();
?><HTML>
<HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="化妆品,化妆品批发,南京化妆品批发,进口化妆品批发,品牌化妆品批发,化妆品批发网,化妆品批发市场,广东美容化妆品网">
<META http-equiv="Description" content="南京涵若铭妆化妆品批发网(www.gdhzp.com)经销各种进口化妆品批发,欧美化妆品批发,韩国化妆品批发,日本化妆品批发等品牌化妆品批发零售业务，并入驻各大化妆品批发市场,广东美容化妆品网.">
<link href="/include/mycss.css" rel="stylesheet" type="text/css">
<title>产品清单－化妆品批发－【涵若铭妆】</title>
</HEAD>
<body oncontextmenu="return isNaIMG(event)">
<SCRIPT language="JavaScript" src="/user/cmbase.js"></SCRIPT><SCRIPT language="JavaScript" src="/include/page_frame.js"></SCRIPT>
<TABLE cellSpacing="0" cellPadding="0" width="1000" align="center"  bgcolor="#FFFFFF" border="0">
<tr>
  <td width="190" valign="TOP">
	 	
    <TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" align="center" border="0">
    <tr>
      <td height="1%">
       <!-----导航:商品分类 开始-----> 
        <SCRIPT language="JavaScript" src="/include/guide_sort.js" type="text/javascript"></SCRIPT>
        <!-----导航:商品分类 结束----->   
      </td></tr><tr><td height="99%"> 
        <!-----导航:空白导航 开始-----> 
        <table border="0" width="190" height="100%" cellpadding="0" cellspacing="0" align="center"  style="BACKGROUND:#FFFFFF; BORDER-COLLAPSE: collapse; border:1px solid #cccccc;">
<tr>
 	 <td align=center height="100%">
     <table width="95%"  height="100%" border="0"  cellspacing="0" cellpadding="0">
     <tr>
    	  <td align="center" style="BACKGROUND-IMAGE:url(/images/advs/advs_blank.gif);"> &nbsp;</td>
     </tr>
     </table>
   </td>
</tr>
</table>
    
        <!-----导航:空白导航 结束----->  
      </td></tr>
    </table>    
     
  </td>
  <td width="10"></td>
  
  <td valign="top" width="800" height="100%">
  	<TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
      <TR>
        <TD width="800" height="25" valign="bottom" style="BACKGROUND-IMAGE:url(/images/ppbar3.gif); BACKGROUND-REPEAT: no-repeat;">
          &nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="/#">首页</a> &gt;&gt; 产品清单
        </TD>
      </TR>
      <TR>
        <TD id="contentbox" valign="top">

<table width=100% border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#f2f2f2"> 
<tr align="center" height=30> 
    <td width="10%" bgcolor="#f7f7f7"><font color="#000000">商品编号</font></td>
    <td width="60%" bgcolor="#f7f7f7"><FONT COLOR="#000000">商品名称</FONT></td>
    <td width="15%" bgcolor="#f7f7f7"><FONT COLOR="#000000">市场价</FONT></td>
    <td width="15%" bgcolor="#f7f7f7"><FONT COLOR="#000000">批发价</FONT></td>
</tr><?php

function GenPageUrl($page){
  return '?page='.$page;
}
$page_size=50;
$res=page_query('select *','from mg_product','where recommend>=0','order by addtime desc',$page_size);
foreach($res as $row){
  echo '<tr bgcolor="#FFFFFF" height=30> 
     <td align="center">'.GenProductCode($row['id']).'</td>
     <td><a href="'.GenProductLink($row['id']).'" target="_blank">'.$row['name'].'</a></td>
     <td align="center" nowrap><strike>￥'.FormatPrice($row['price1']).'元</strike></td>
     <td align="center" nowrap><font color=#FF0000>'.FormatPrice($row['price3']).'</font></td>
  </tr>';
}
echo '</table><TABLE cellSpacing=0 cellPadding=0 width="100%" align="center" border="0"><TR><form><TD align="center">共 <b>'.$total_records.'</b> 件商品&nbsp;&nbsp;';

if($page==1) echo '首页 上一页';else echo '<a href="'.GenPageUrl(1).'"  onclick="return JumpLinks(this)">首页</a> <a href="'.GenPageUrl($page-1).'"  onclick="return JumpLinks(this)">上一页</a>';
  echo '&nbsp;';
  if($page==$total_pages) echo '下一页 尾页';else echo '<a href="'.GenPageUrl($page+1).'" onclick="return JumpLinks(this)">下一页</a> <a href="'.GenPageUrl($total_pages).'" onclick="return JumpLinks(this)">尾页</a>'; 
  echo ' 页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页 <b>'.$page_size.'</b>篇文章/页';
  echo ' 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="JumpToPage(this.form.page.value);"></form></td></tr></TABLE>';
  
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
		<td width="100%"><A href="<%=WebRoot%>#">站点首页</A> | <A href="<%=WebRoot%>help/">常见问题</A> | <A href="<%=WebRoot%>help/help27.htm">批发规则</A> | <A href="<%=WebRoot%>help/help11.htm">购物流程</A> | <A href="<%=WebRoot%>help/help6.htm">付款方式</A> |&nbsp;<A href="/usrmgr.htm?action=payonline">在线支付</A> | <A href="<%=WebRoot%>help/help16.htm">配送问题</A> |&nbsp;<A href="<%=WebRoot%>help/help8.htm">售后服务</A> | <A href="<%=WebRoot%>news/">商城新闻</A> | <A href="/products/">产品清单</A> |&nbsp;<A href="<%=WebRoot%>newarrival.asp">最新到货</A>&nbsp;| <A href="<%=WebRoot%>help/help15.htm">联系我们</A>&nbsp;| <A href="<%=WebRoot%>help/help10.htm">关于我们</A></td>
	</tr>
	<tr align="center" height="20" valign="bottom">
		<td width="100%">  	&copy; 2006~2012&nbsp; 涵若铭妆 &nbsp;进口化妆品批发&nbsp;　<a href="https://www.alipay.com/aip/aip_validate_list.htm?trust_id=AIP03023958">淘宝信任商家</a>   </td>
	</tr>
</table>
</body>
</html><?php
CloseDB();?>
