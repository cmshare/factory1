<TABLE border=0 cellSpacing=0 cellPadding=0 width="185"><TR><TD height="50" background="/images/guide_sort.gif"></TD></TR>
<?php 
  $res=$conn->query("select id,title from `mg_category` where property>0 and recommend>0 order by property desc",PDO::FETCH_NUM);
  foreach($res as $row){
    $res_subsort=$conn->query("select id,title from `mg_category` where pid={$row[0]} and recommend>0 order by sequence desc",PDO::FETCH_NUM);
    $row_subsort=$res_subsort->fetch();
    if($row_subsort)	
    {?><TR>
         <TD class="<?php if($row[0]!=@$UnfoldProperty) echo "gMenuClose"; else echo "gMenuOpen";?>">
         <span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/category/cat<?php echo $row[0];?>.htm"><?php echo $row[1]?></a></span></span>
         <div><?php
         do{
           echo "<a href=\"/category/cat{$row_subsort[0]}.htm\">{$row_subsort[1]}</a>";
         }while(($row_subsort=$res_subsort->fetch()));?></div>
          </TD>
        </TR><?php
     }
     else
     {?><TR>
          <TD class="gMenuEmpty">
          <span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/category/cat<?php echo $row[0]?>.htm"><?php echo $row[1]?></a></span></span>
          </TD>
        </TR><?php
     }   
  }  
?>
<tr><td height=10></td></tr></table>
