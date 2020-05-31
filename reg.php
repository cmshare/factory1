<?php
require("include/conn.php");
$PageKeywords='用户注册,化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,上海化妆品批发,苏州化妆品批发,无锡化妆品批发,南京化妆品批发';
$PageDescription='这是南京涵若铭妆新用户注册中心,涵若铭妆提供各种进口化妆品批发,韩国化妆品批发,上海化妆品批发,无锡化妆品批发,苏州化妆品批发等品牌化妆品批发零售业务';
$PageTitle='新用户注册 - 韩国化妆品批发|进口化妆品批发|欧美品牌化妆品批发-涵若铭妆贸易有限公司';
ob_start();
require('include/page_head.php');
db_open();
?>
<TABLE cellSpacing="0" cellPadding="0" border="0" width="1000" align="center" style="background:url(/images/bg_mid.gif) repeat-x;">
 <tr>
	 <td width="200" valign="TOP">
	 	
	 	<TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" border="0" style="background:url(/images/bg_left.gif) repeat-y;margin-top:30px;">
	 	<tr><td height="1%">
	 	    <!-----导航:帮助中心 开始------> 
                    <?php require('include/guide_help.php');?>  
                   <!-----导航:帮助中心 结束------>
      </td></tr>
    <tr>
    	<td height="99%" style="background:url(/images/advs/ADVs_Blank.gif) repeat-y;"></td></tr>
    </table>	
     
  </td>
  

  
  <td valign="top" width="800" height="100%">
  <!-----客户区 开始-------->
    <TABLE cellSpacing=0 cellPadding=0 width="96%" height="100%" align="center"  border="0" style="margin-top:25px">
    <TR>
      <TD width="100%" height="40" align="right" style="BACKGROUND-IMAGE:url(/images/kubars/kubar_reg.gif); BACKGROUND-REPEAT: no-repeat;">
        &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<%=WebRoot%>#">首页</a> >> 新用户注册
      </TD>
    </TR>
    <TR>
      <TD valign="top"><?php require('user/m_reg.php');?></TD>
    </tr>
    </table>
    <!--------客户区 结束--------->
  </td>
</tr>
<tr><td height="5"></td></tr>	
</table>
<?php include('include/page_bottom.htm');
db_close();?>
</body>
</html>
