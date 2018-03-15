<?php require('include/conn.php');
CheckLogin();
OpenDB();
$PageTitle='提交订单 - '.WEB_NAME;
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR>
	<TD colspan=2 height="15"></TD>
</TR>	
<TR valign="top">
  <TD align="center" background="images/client_bg_left.jpg" width=210" height="100%">
  <!--#include file="include/guide_help.asp"-->     
  </TD>  
	<TD id="contentbox">
		 <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
     <TR>
        <TD width="100%" height="25" class="pageguider">
          <img src="images/arrow2.gif" width="6" height="7">&nbsp;您現在的位置：&nbsp;<a href=".">主頁</a> &gt;&gt; 收银台——提交订单
        </TD>
     </TR>
     <TR>
        <TD valign="top" style="padding:15px"><?php require('user/m_paybill.php');?></td>
      </tr>
     </TABLE>
  </TD>
</TR>
</TABLE><?php
require('include/page_bottom.htm');
CloseDB();?>
</BODY>
</HTML>
