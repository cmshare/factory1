<TABLE width=185 border=0 cellSpacing=0 cellPadding=0> <TR><TD height="50" background="/images/guide_brand.gif"></TD></TR>  
<?php //说明：`category`.recommend (<0)表示已删除的品牌  (=0)表示隐藏的品牌  (=1)表示普通品牌 (>1)表示热销品牌 
function GenBrand($cat_id,$cat_title)
{ global $UnfoldBrand,$conn;
  $SortGuidePath="";
	if($cat_id==0)
	{ $cat_index_file="/hotsell.htm";
    $query_subcat=$conn->query("select id,title from `mg_category` where recommend>1 order by recommend desc",PDO::FETCH_NUM);
  }
  else
  { $cat_index_file="/category/cat".$cat_id.".htm";
    $query_subcat=$conn->query("select id,title from `mg_category` where pid=$cat_id and recommend>0 order by sequence desc",PDO::FETCH_NUM);
  }?>
  <TR><TD class="<?php if($cat_id!=$UnfoldBrand) echo "gMenuClose"; else echo "gMenuOpen";?>">
  <span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="<?php echo $cat_index_file?>"><?php echo $cat_title?></a></span></span><div><?php
  foreach($query_subcat as $rs_subcat)
  { echo "<a href=\"/category/cat".$rs_subcat[0].".htm\">".$rs_subcat[1]."</a>";
  }?></div></TD></TR><?php 	 
}
if(empty($UnfoldBrand)) $UnfoldBrand=0;
if(empty($ParentBrand)) $ParentBrand=0;
if($ParentBrand==0)
{ GenBrand(0,"热销品牌");
  $query_cat=$conn->query("select id,title from `mg_category` where pid=0 and recommend>0 order by sequence desc",PDO::FETCH_NUM);
}
else { echo "<TR style=\"CURSOR: pointer\" onMouseOver=\"gmEnter(this)\" onMouseOut=\"gmLeave(this)\" onclick=\"gmSwitch(this)\" height=24 valign=\"middle\"><TD> <IMG width=20 height=20 border=0 src=\"/images/guidefold2.gif\" align=absMiddle> <a href=\"/category/cat$ParentBrand.htm\"><font color=#FF0000>返回上级分类</font></a></TD></TR>";
  $query_cat=$conn->query("select id,title from `mg_category` where id=$UnfoldBrand and recommend>0",PDO::FETCH_NUM);
}

foreach($query_cat as $rs_cat)   
{ GenBrand($rs_cat[0],$rs_cat[1]);
}
?><tr><td height=10></td></tr></table>  



