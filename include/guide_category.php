<TABLE border=0 cellSpacing=0 cellPadding=0 width="185"><TR><TD height="50" background="/images/guide_sort.gif"></TD></TR>
<?php  
if(@$UnfoldProperty>0)
{ function sort2($selec)
  { global $conn;
	  $count=0;
	  $res=$conn->query("select id,title from `mg_sort` where parent = $selec",PDO::FETCH_NUM);
	  foreach($res as $row)
	  { $count+=sort2($row[0]);
    }
    $res=$conn->query("select count(*) from `mg_product` where category=$selec and recommend>0",PDO::FETCH_NUM);
    $count+=$res->fetchColumn(0);
    return $count;
  }
	$row=$conn->query("select parent,title from `mg_sort` where id=$UnfoldProperty",PDO::FETCH_NUM)->fetch();
  echo "<TR style=\"CURSOR: pointer\" onMouseOver=\"gmEnter(this)\" onMouseOut=\"gmLeave(this)\" onclick=\"gmSwitch(this)\" height=24 valign=\"middle\"><TD> <IMG width=20 height=20 border=0 src=\"/images/guidefold2.gif\" align=absMiddle> <a href=\"/category-{$row[0]}.htm\"><font color=#FF0000>返回上级分类</font></a></TD></TR>";
  echo "<TR><TD class=\"gMenuOpen\"><span onMouseOver=\"gmEnter(this)\" onMouseOut=\"gmLeave(this)\"><span class=\"gMenuBar\"><a href=\"/category/sort{$UnfoldProperty}.htm\">{$row[1]}</a></span></span></td></TR>";
  $res=$conn->query("select id,title from `mg_sort` where parent=$UnfoldProperty order by sortorder",PDO::FETCH_NUM);
  foreach($res as $row)
  {?>
    <TR><td height=20 class="gMenu2" onmouseover="MM_showHideLayers('Layer<?php echo $row[0]?>',true)" onmouseout="MM_showHideLayers('Layer<?php echo $row[0]?>',false)"><a href="/category/sort<?php echo $row[0]?>.htm" style="font-weight:<?php if($row[0]==$propertyid)echo "bold"; else echo "normal";?>"><?php echo $row[1]?>(<?php echo sort2($row[0])?>)</a><?php
    $res_subsort=$conn->query("select id,title from `mg_sort` where parent={$row[0]} order by sortorder",PDO::FETCH_NUM);
    $row_subsort=$res_subsort->fetch();
    if($row_subsort){?>
      <div id="Layer<?php echo $row[0]?>" style="position: absolute; visibility: hidden; margin-left: 30px;" onmouseover="MM_showHideLayers('Layer<?php echo $row[0]?>',true)" onmouseout="MM_showHideLayers('Layer<?php echo $row[0];?>',false)">
		  <table width="137" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="7" align="right" valign="top" style="padding-top:6px"><img src="/images/kuang_04.gif" width="7" height="9"></td>
		  	<td valign="top">
			    <table width="129" border="0" cellspacing="0" cellpadding="0">
			    <tr>
			     	<td><img src="/images/kuang_02.gif" width="129" height="5"></td>
			    </tr>
			    <tr>
			  	  <td align="center" background="/images/kuang_07.gif">
				 	   	<table width="120" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed"><?php
			        do{?><tr>
					  	    <td width="15" align="center"><img src="/images/kuang_09.gif" width="5" height="8"></td>
					  	    <td width="105" height="20" ><a href="/category/sort<?php echo $row_subsort[0]?>.htm"><?php echo $row_subsort[1]?>(<?php echo sort2($row_subsort[0])?>)</a></td>
					  	    </tr>
					  	   <tr>
					  	   	<td colspan="2"><img src="/images/kuang_08.gif" width="113" height="3"></td>
					  	 	 </tr><?php
					    }while(($row_subsort=$res_subsort->fetch()));?>	 	
		  	 		  </table>	 
		  	    </td>
				  </tr>
				  </table>
				  <table width="129" border="0" cellspacing="0" cellpadding="0">
				  <tr>
				    <td><img src="/images/kuang_06.gif" width="129" height="5"></td>
				  </tr>
				  </table>
			  </td>
		  </tr>
		  </table>
	    </div><?php
    }?></td></tr><?php
  }
}
else{
  $res=$conn->query("select id,title from `mg_sort` where parent=0 order by sortorder",PDO::FETCH_NUM);
  foreach($res as $row){
    $res_subsort=$conn->query("select id,title from `mg_sort` where parent={$row[0]} order by sortorder",PDO::FETCH_NUM);
    $row_subsort=$res_subsort->fetch();
    if($row_subsort)	
    {?><TR>
         <TD class="<?php if($row[0]!=@$UnfoldProperty) echo "gMenuClose"; else echo "gMenuOpen";?>">
         <span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/category/sort<?php echo $row[0];?>.htm"><?php echo $row[1]?></a></span></span>
         <div><?php
         do{
           echo "<a href=\"/category/sort{$row_subsort[0]}.htm\">{$row_subsort[1]}</a>";
         }while(($row_subsort=$res_subsort->fetch()));?></div>
          </TD>
        </TR><?php
     }
     else
     {?><TR>
          <TD class="gMenuEmpty">
          <span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/category/sort<?php echo $row[0]?>.htm"><?php echo $row[1]?></a></span></span>
          </TD>
        </TR><?php
     }   
  }  
}?>
<tr><td height=10></td></tr></table>
