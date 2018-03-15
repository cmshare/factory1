<?php require('include/conn.php');
$PageKeywords='用户注册,化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,上海化妆品批发,苏州化妆品批发,无锡化妆品批发,南京化妆品批发';
$PageDescription='这是香港銘悅商城新用户注册中心,铭悦商城提供各种进口化妆品批发,韩国化妆品批发,上海化妆品批发,无锡化妆品批发,苏州化妆品批发等品牌化妆品批发零售业务';
$PageTitle='新用户注册 - 韩国化妆品批发|进口化妆品批发|欧美品牌化妆品批发-铭悦商城贸易有限公司';
OpenDB();
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="15"></TD></TR>	
<TR valign="top">
  <TD align="center" background="images/client_bg_left.jpg" width=210" height="100%"><?php
  require('include/guide_help.php');?>     
  </TD>  
  <TD width="790">
    <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
     <TR><TD width="100%" height="25" class="pageguider"><img src="images/arrow2.gif" width="6" height="7">&nbsp;您现在的位置：&nbsp;<a href=".">主页</a> &gt;&gt; 新用户注册</td></tr>
     <TR><TD height="40" background="images/kubars/kubar_reg.gif"></TD></TR>
     <TR>
        <td valign="top"><?php require('user/m_reg.php');?></td>
     </TE>
     </TABLE>
   </TD>
</TR>
</TABLE><?php
require('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
