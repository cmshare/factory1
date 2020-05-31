<?php require('include/conn.php');
db_open();

$id=@$_GET["id"];
if(is_numeric($id)) $sql_condition='id='.$id;
else
{ $help_Title=FilterText(trim(@$_GET["title"]));
  if(empty($help_Title)) $help_Title="常见问题";
  $sql_condition='title=\''.$help_Title.'\'' ;
}
$row=$conn->query('select title,content from `mg_help` where '.$sql_condition.' and property=1',PDO::FETCH_NUM)->fetch();
if($row){ 
   $help_Title=$row[0];
   $helpContent=$row[1];
}  

$Pagination=6;
$PageKeywords="化妆品,化妆品批发,南京化妆品批发,韩国化妆品批发,进口批妆品批发,品牌化妆品批发,欧美化妆品批发,上海化妆品批发";
$PageDescription=$help_Title.",南京涵若铭妆主要提供各种品牌化妆品批发,韩国化妆品批发,进口批妆品批发,欧美化妆品批发等业务,地区代理上海化妆品批发.";
if($id>0)$PageTitle=$help_Title."－韩国化妆品批发-进口化妆品批发-南京涵若铭妆化妆品批发网";
else $PageTitle="帮助导航－南京涵若铭妆化妆品批发网";
include("include/page_head.php");?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(/images/bg_mid.gif) repeat-x;">
 <tr>
	 <td width="200" valign="TOP">
		<TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" border="0" style="background:url(/images/bg_left.gif) repeat-y;margin-top:30px;">
	 	<tr>
	 		<td height="1%">
	 	    <?php //导航:帮助导航
         include("include/guide_help.php");
        ?>
      </td></tr><tr><td height="99%"> 
        <?php //导航:友情链接
         include("include/guide_links.php");
        ?>
      </td></tr>
    </table>	  	
  </td>
  <td valign="top" width="800" height="100%">
    <TABLE cellSpacing=0 cellPadding=0 width="800" height="100%" align="center"  border="0">
     <TR>
         <TD width="800" height="40" valign="middle">         
          &nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">主页</a> &gt;&gt; <a href="help.htm">帮助导航</a>
          <?php if($help_Title) echo "&gt;&gt; ".$help_Title;?>
         </TD>
     </TR>
     <TR>
        <TD id="ContentBox" valign="top" style="padding-left:20px;padding-right:15px;line-height:200%"><?php echo $helpContent;?></td>
      </tr>
    </table>
<!------- 结束---------->
    
   </td>
</tr>
<tr>
   <td height="5" colspan="3"></td>
</tr>	
</table>
<?php
  include("include/page_bottom.htm");
  db_close();
?>
</body>
</html> 
