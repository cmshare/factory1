<?php require('include/conn.php');
CheckLogin('paybill.php');
OpenDB();
$PageKeywords='订单,化妆品批发,南京化妆品批发,韩国化妆品批发,日本化妆品批发,进口化妆品批发';
$PageDescription=WEB_NAME.'主要提供各种韩国化妆品批发、日本化妆品批发、欧美化妆品批发、进口化妆品批发、国际名牌化妆品,网络热销化妆品、精油香水等化妆品批发零售业务, 是南京地区规模最大的进口名牌化妆品批发平台';
$PageTitle='收银台--确认订单--'.WEB_NAME;
include('include/page_head.php');?>   
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(images/bg_mid.gif) repeat-x;">
<TR>
   <TD height="40">&nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; <a href="usrmgr.htm">会员中心</a> &gt;&gt; 收银台 &gt;&gt; <b>确认订单</b></TD>
</TR>
<tr>
  <td><?php include('user/m_confirmbill.php');?></td>
</tr>
</table><?php
include('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
