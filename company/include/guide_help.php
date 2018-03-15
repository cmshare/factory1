<table  width="100%"  border="0" cellSpacing="0" cellPadding="0">
<tr><td align="center" height="45"><img src="images/company_info.gif" width="159" height="45"></td></tr>	
<tr><td height="310"><img src="images/support.gif" width="210" height="310"></td></tr>
<tr>
  <td align="right" valign="top">
       <style type="text/css">
       TR.newsmenu_style1 TD A {color:#000000;font-size:14px;Filter:glow(color=#FFFFFF,strength=5); height:16px}
       TR.newsmenu_style1 TD img {display:none}
       TR.newsmenu_style2 TD A {color:#FFFFFF;font-size:14px;font-weight:bold;Filter:glow(color=#FF6600,strength=8); height:16px} 
       TR.newsmenu_style2 TD img {display:block}
     </style> 
  	 <table  width="160"  border="0" cellSpacing="0" cellPadding="0"><?php
     $keyfilter='南京铭悦_';
     $newproperty=6;
     $res=$conn->query('select * from `mg_article` where property='.$newproperty.' and title like \''.$keyfilter.'%\' order by addtime desc',PDO::FETCH_ASSOC);
     foreach($res as $row_help){
       $TrAttr=($row_help['id']==@$topicid)?'class="newsmenu_style2"':'class="newsmenu_style1" onmouseover="this.className=\'newsmenu_style2\'" onmouseout="this.className=\'newsmenu_style1\'"';
       echo '<tr '.$TrAttr.' height="30" ><td width="20"><img src="images/focusdot.gif" width="12" height="12" ></td><td><a href="help.htm?id='.$row_help['id'].'">'.str_replace($keyfilter,'',$row_help['title']).'</a></td></tr>';
     }?>
  	 </table>
  </td>
</tr>
</table>
