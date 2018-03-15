<?php require('include/conn.php');
$Pagination='7';
$PageKeywords='会员中心,化妆品,化妆品批发,韩国化妆品批发,日本化妆品批发,欧美化妆品批发,进口化妆品批发,品牌化妆品批发,南京化妆品批发';
$PageDescription='这是南京涵若铭妆会员中心,涵若铭妆提供各种进口化妆品批发,欧美化妆品批发,韩国化妆品批发,日本化妆品批发等品牌化妆品批发零售业务';
$PageTitle='会员中心 - 韩国化妆品批发|进口化妆品批发|欧美品牌化妆品批发-涵若铭妆贸易有限公司';
include('include/page_head.php');?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<tr>
   <td width="200" height="100%"  valign="TOP">
   <!------导航:会员中心 开始------> 
   <TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" border="0">
   <tr><td height="25"></td></tr>
   <tr><td height="35" class="ab_menu" align="center" background="/images/guide_userbg1.gif"><div STYLE="font-size:16px; font-weight:normal; color:#565656;font-family:微软雅黑,黑体,宋体;">会员中心 <span style="font-size:14px; font-family:Arial, Helvetica, sans-serif; color:#ccc; font-weight:bold;">MEMBER</span></div></TD></TR>	        
   <tr><td height="185" style="padding-left:8px;BACKGROUND-IMAGE:url(/images/guide_userbg2.gif);">

   <TABLE border=0 cellSpacing=2 cellPadding=0 width="180" height="100%" bgcolor="#f2f2f2">
   <TR align="center" height="45">
     <TD vAlign="top" colspan="2" width="100%" style="padding-bottom:1px"><a href="usrmgr.htm#msg" onclick="return show_msg(1)"><img src="images/zldx.gif" width="178" height="45" border="0"></a></TD>
   </TR>
   <tr align="center" height="25" bgcolor="#FFFFFF">
     <td><a href="?action=customerinfo">个人资料</a></td>
     <td><a href="?">账户信息</td>
   </tr>
   <tr align="center" height="25" bgcolor="#FFFFFF">
     <td><a href="?action=receiveaddr">收货地址</a></td>
     <!--td align="center"></a><a href="help.htm">常见问题</a></td-->
     <td></a><a href="?action=accountlog">账务明细</a></td>
   </tr>
   <tr align="center" height="25" bgcolor="#FFFFFF">
     <td><a href="?action=changepass">密码安全</a></td>
     <td><a href="?action=resetpsw">找回密码</a></td>
   </tr>
   <tr align="center" height="25" bgcolor="#FFFFFF">
     <td><a href="?action=myorders">我的订单</a></td>
     <td><a href="?action=payonline">在线支付</a></td>
   </tr>
   <tr align="center" height="25" bgcolor="#FFFFFF">
     <td><a href="?action=myfav">我的收藏</a></td>
     <td><a href="?action=mycart">购 物 车</a></td>
    </tr> 
    </TABLE></td></tr>

    <tr><td background="/images/guide_blank2.gif"></td></tr>
    <tr><td height="10" background="/images/guide_userbg3.gif"></td></tr>
    </table>	 
    <!------导航:会员中心 结束------>   
   </td>
   <td valign="top" width="800" height="100%" id="userbox" style="padding-top:25px"></td>
</tr>
</table><SCRIPT language="JavaScript" src="user/district.js" type="text/javascript"></SCRIPT><SCRIPT language="JavaScript" src="user/usrmgr.js" type="text/javascript"></SCRIPT><script>
process_request();
</script><?php
include('include/page_bottom.htm');?>
</body>
</html>
