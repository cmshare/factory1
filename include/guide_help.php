<TABLE border=0 cellSpacing=0 cellPadding=0 width="185">
<TR><TD height="50" background="/images/guide_help.gif"></TD></TR>
<TR><TD align="center"><TABLE border=0 cellSpacing=0 cellPadding=0 width="100"><?php
$query=$conn->query("select id,title from `mg_help` where parent=0 and property=1 order by sequence",PDO::FETCH_NUM);
foreach($query as $rs) 
{?>
<TR>
  <TD onMouseOver="bgColor='#FFE3D2';" onMouseOut="bgColor='';" height=26 valign="middle">
 	  <IMG id="img<?php echo $rs[0]?>"  width="12" height="12" src="/images/greenaccept.gif" align="absMiddle">
    <a href="/help/help<?php echo $rs[0]?>.htm"><font color="#77CC00"><B><?php echo $rs[1]?></B></font></a></td></tr><?php
}?>
</table></td></tr></table>
