<TABLE border=0 cellSpacing=0 cellPadding=0 width="185" height="100%" style="margin-top:10px">
<TR><TD height="50" background="/images/guide_pay.gif"></TD></TR>     
<tr><td height="119"><a href="/usrmgr.htm?action=payonline"><img src="/images/onlinepay.gif" width=185 height=119 border=0></a></td></tr>
<TR><td height="98%" style="background:url(/images/advs/advs_blank.gif) repeat-y;"></td></TR>
<tr><td>
  <TABLE cellSpacing=0 cellPadding=0 width="100%" border="0" style="margin-top:10px">
  <TR><TD height="50" colspan="2" background="/images/guide_link.gif"></TD></TR>  
<?php
$res=$conn->query("select linkurl,linktitle,linkname from `mg_links` where property=1 order by linkorder",PDO::FETCH_NUM);
foreach($res as $row)
{?>
  <TR height="25">
     <TD width="40" align="center"><img src="/images/icon_4.gif" width="16" height="16"></TD>
     <TD><a href="<?php echo $row[0]?>" title="<?php echo $row[1]?>" target="_blank"><font color="#00aa00"><?php echo $row[2]?></font></a></TD></TR><?php
}?>
  <tr><td height="10"></td></tr></table></td></tr>
</table>

      
  
