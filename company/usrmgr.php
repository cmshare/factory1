<?php require('include/conn.php');
$PageTitle='会员中心 - '.WEB_NAME;
require('include/page_head.php');?>
<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="1"></TD></TR>	
<TR valign="top">
  <TD background="images/client_bg_left.jpg" width=190" height="100%">
    <TABLE cellSpacing="0" cellPadding="0" width="190" height="100%" border="0">
    <tr>
      <td height="1%">
      <!---导航:会员中心 开始------> 
      <TABLE cellSpacing=0 cellPadding=0 width="188" align="center"  border="0" id="usrnav" style="display:none">
      <TR>
         <TD height="31"><IMG height=31 src="images/guide_member.gif" width=188></TD>
      </TR>
      <TR>
      	<TD style="BORDER-left:#FF6600 1px solid;BORDER-right:#FF6600 1px solid;">
          <TABLE cellSpacing="1" cellPadding="0" width="100%"  border="0" bgcolor="#dddddd">
          <TR align="center" height="25">
             <TD vAlign="top" colspan="2" width="100%" style="padding-bottom:1px" bgcolor="#FFFFFF">
             	<a href="#" onclick="return show_msg(1)"><img src="images/zldx.gif" width="178" height="45" border="0"></a>
             </TD>
          </TR>
          <tr align="center" height="25">
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_customerinfo();">个人资料</a></td>
            <td bgcolor="#FFFFFF"><a href="#"  onclick="return show_accountinfo();">账户信息</td>
          </tr>
          <tr align="center" height="25">
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_receiveaddr();">收货地址</a></td>

            <td bgcolor="#FFFFFF"></a><a href="#" onclick="return show_accountlog(1);">账务明细</a></td>
          </tr>
          <tr align="center" height="25">
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_changepass();">密码安全</a></td>
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_resetpsw();">找回密码</a></td>
          </tr>
          <tr align="center" height="25">
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_myorders();">我的订单</a></td>
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_onlinepay();">在线支付</a></td>
          </tr>
          <tr align="center" height="25">
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_myfav();">我的收藏</a></td>
            <td bgcolor="#FFFFFF"><a href="#" onclick="return show_mycart();">购 物 车</a></td>
          </tr> 
          </TABLE>         	
      	</TD>
      </TR>
      </TABLE>
      <!---导航:会员中心 结束------> 
      </td></tr><tr><td height="99%" background="images/left_bg.gif"> 
      </td></tr>
    </table> 
  </TD>
  <TD width="810"  style="BORDER-right:#FF67A0 1px solid;">
     <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0">
     <TR>
        <TD width="100%" height="30" valign="middle"  background="images/pdbg01.gif">
          <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"  border="0">
          <TR>
            <TD style="padding-left:25px"><img src="images/arrow2.gif" width="6" height="7">&nbsp;您现在的位置：<a href="<?php echo WEB_ROOT;?>">主页</a> &gt;&gt; 会员中心 <span id="manage_item"></span></TD>
          </TR>
          </TABLE>
         </TD>
     </TR>
     <TR>
        <TD valign="top" id="userbox"></td>
     </tr>
     </TABLE>
  </TD>
</TR>
</TABLE><?php
require('include/page_bottom.htm');
if(OWN_ICP){?>
<SCRIPT language="JavaScript" src="user/district.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="user/usrmgr.js" type="text/javascript"></SCRIPT>
<script>
  var do_action=htmRequest("action");
  if(!do_action)do_action=GetLinkLabel();
  switch(do_action)  
  {  case   "payonline":    show_onlinepay();break;
     case   "myorders":     show_myorders();break;
     case   "resetpsw":     show_resetpsw();break; 
     case   "customerinfo": show_customerinfo();break;
     case   "receiveaddr":  show_receiveaddr();break; 
     case   "accountlog":   show_accountlog(1);break; 
     case   "changepass":   show_changepass();break; 
     case   "mycart":       show_mycart();break; 
     case   "myfav":        show_myfav();break;
     case   "msg":          show_msg(1);break;    
     default:               show_accountinfo();
  }
  if(OnlineUserID)document.getElementById("usrnav").style.display="";
/*change header links of page_head.asp*/ 
</script><?php
}?>
</body>
</html>
