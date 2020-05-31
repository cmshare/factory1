<?php //注意：所有被include的文件要保存为无BOM格式，否则会导致页面头部空隙。
require('conn.php');
db_open();
$rs=$conn->query("select webname,weblogo,address,postcode,webemail,tel,fax,copyright,icp from `mg_configs`",PDO::FETCH_ASSOC)->fetch();?>
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" id="MyPageBottom" style="background:url(/images/bg_bot.gif) repeat-x;">
<tr height="22" align="center" class="menu_bar">
  <td width="100%"><A href="/">站点首页</A> | <A href="/help.htm">常见问题</A> | <A href="/help/help7.htm">批发规则</A> | <A href="/help/help2.htm">购物流程</A> | <A href="/help/help3.htm">付款方式</A> |&nbsp;<A href="/usrmgr.htm?action=payonline">在线支付</A> | <A href="/help/help4.htm">配送问题</A> |&nbsp;<A href="/help/help5.htm">售后服务</A> | <A href="/article.htm">商城新闻</A> | <A href="/products/">产品清单</A> |&nbsp;<A href="/newarrival.php">最新到货</A>&nbsp;| <A href="/help/help8.htm">联系我们</A>&nbsp;| <A href="/help/help9.htm">关于我们</A></td>
</tr>
<tr>
  <td width="100%" align="center">
  <table border="0" cellpadding="1" cellspacing="1" width="1000" style="margin-top:5px;">
  <tr align="center" valign="middle">
    <td width="225" rowspan="2"><img src="/<?php echo $rs['weblogo'];?>" border=0></td>
    <td width="550"><font face="Arial">&copy;</font>2006~<?php echo date("Y")." ".$rs['copyright'];?> 版权所有 <a href="http://www.beian.miit.gov.cn" ><?php echo $rs['icp'];?></a> </td>
    <td width="225" rowspan="2"><img src="/images/servertel.png" width="190" height="36" alt="加盟服务热线"></td>
   </tr>
   <tr><td align="center" class="onlineservice"><div><a href="/sitemap.htm">化妆品批发网</a>|<a href="/articles/">南京化妆品批发网新闻</a>|<a href="/category/">品牌进口化妆品批发</a>|<a href="/sort.htm">南京韩国化妆品批发</a>|<a href="/onsale.htm">特价进口化妆品批发</a>|<a href="/hotsale.htm">热销品牌化妆品批发</a>|<?php echo WEB_NAME;?>~打造南京化妆品批发最低价</div></td></tr>
   <tr><td align="center" colspan="3"></td></tr>
   <tr><td align="center" colspan="3">公司地址：<?php echo $rs['address'];?> &nbsp;邮编：<?php echo $rs['postcode'];?>  &nbsp;电话：<?php echo $rs['tel'];?> &nbsp;传真：<?php echo $rs['fax'];?> &nbsp;邮箱：<?php echo $rs['webemail'];?></td></tr>
   </table>
   </td>
</tr>
</table><SCRIPT language="JavaScript" src="/include/qqservice.js" type="text/javascript"></SCRIPT>
<?php db_close();?>
