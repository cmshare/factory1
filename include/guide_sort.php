var CurrentBrand,CurrentCategory;	
var BrandIndex=new Array();
var CategoryIndex=new Array();
<?php
require("conn.php");
OpenDB();

function GenBrandIndex($brand,$categorytitle,$parent)
{ global $conn; 
  $res=$conn->query("select id,title from `mg_category` where parent=$brand and recommend>0 order by sortorder",PDO::FETCH_NUM);
  $ArrayCode="BrandIndex[\"$brand\"]=new Array(\"$parent\",\"$categorytitle\"";
  foreach($res as $row)
  { $ArrayCode=$ArrayCode.",\"".$row[0]."\"";
    GenBrandIndex($row[0],$row[1],$brand);
  }
  echo $ArrayCode.");\r\n";

}

function GenCategoryHot()
{ global $conn;
  $res=$conn->query("select id from `mg_category` where recommend>1 order by recommend desc",PDO::FETCH_NUM);
  $ArrayCode="BrandIndex[\"hot\"]=new Array(\"\",\"热销品牌\"";
  foreach($res as $row)
  { $ArrayCode=$ArrayCode.",\"".$row[0]."\"";
  }
  echo $ArrayCode.");\r\n";
}


function GenCategoryIndex($category,$categorytitle,$parent)
{ global $conn;
  $res=$conn->query("select id,title from `mg_sort` where parent=$category order by sortorder",PDO::FETCH_NUM);
  $ArrayCode="CategoryIndex[\"$category\"]=new Array(\"$parent\",\"$categorytitle\"";
  foreach($res as $row)
  { $ArrayCode=$ArrayCode.",\"".$row[0]."\"";
    GenCategoryIndex($row[0],$row[1],$category);
  }
  echo $ArrayCode.");\r\n";
}

GenBrandIndex(0,"","");
GenCategoryHot();
GenCategoryIndex(0,"","");
CloseDB();
?>


function gmSwitch(m)
{ m=m.parentNode;
	if(m.className=="gMenuOpen")m.className="gMenuClose";
	else if(m.className=="gMenuClose")m.className="gMenuOpen";
	else
	{ m=m.getElementsByTagName("A");
	  if(m && m.length)self.location.href=m[0].href;
	}
}
function gmEnter(m){m.style.backgroundColor="#FFE3D2";}
function gmLeave(m){m.style.backgroundColor="";}
function genBrandURL(brandid){return "/category/cat"+brandid+".htm";}
function genCategoryURL(catid){return "/category/sort"+catid+".htm";}

function GenSingleBrand(brand)
{ var menu_class,brand_file,brand2,brandcode="";
	if(brand=="hot")
	{ brand2="0";
	  brand_file="/hotsell.htm";
	}
	else
	{ brand2=brand;
	  brand_file=genBrandURL(brand2);
	}
	if(BrandIndex[brand].length>2)
	{ if(brand2==CurrentBrand)menu_class="gMenuOpen";
		else menu_class="gMenuClose";
	}
	else
	{ menu_class="gMenuEmpty";
	}
	
	brandcode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+brand_file+'">'+BrandIndex[brand][1]+'</a></span></span><div>';
  subsort=BrandIndex[brand];
  subcount=subsort.length;
   
  for(j=2;j<subcount;j++)
  { brandcode+='<a href="'+genBrandURL(subsort[j])+'">'+BrandIndex[subsort[j]][1]+'</a>'; 
  } 
  brandcode+='</div></TD></TR>';
  return brandcode
}

      

function ShowBrandGuider()
{ var i,j,RootBrand,sortcount,subcount,subsort,parent,brandcode;
  
  if(!CurrentBrand)CurrentBrand="0";
   
  if(CurrentBrand!="0")
  { if(BrandIndex[CurrentBrand].length<3 && BrandIndex[CurrentBrand][0]!="0")
  	{ CurrentBrand=BrandIndex[CurrentBrand][0];
  	}
  }

  RootBrand=(BrandIndex[CurrentBrand][0]=="0")?"0":CurrentBrand;
  
  brandcode='<TABLE cellSpacing=0 cellPadding=0 width="185" align="center"  border="0"><TR><TD height=50 background="/images/guide_category.gif"></TD></TR>';
  if(RootBrand!="0")
  { brandcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="/images/guidefold2.gif" align="absMiddle"> <a href="'+genBrandURL(BrandIndex[RootBrand][0])+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
  	brandcode+=GenSingleBrand(RootBrand);
  }
  else
  { brandcode+=GenSingleBrand("hot");
    subsort=BrandIndex[RootBrand];
  	sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { brandcode+=GenSingleBrand(subsort[i]);
    }  	
  }
  brandcode+='<TR><td height="10"></td></tr></TABLE>';
  document.write(brandcode);
}

function GenSingleCategory(category)
{ var guide_fold_image,guide_fold_disp_opt,categorycode="";
	if(CategoryIndex[category].length>2)
	{ if(category==CurrentCategory)menu_class="gMenuOpen";
		else menu_class="gMenuClose";
	}
	else
	{ menu_class="gMenuEmpty";
	}

  categorycode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+genCategoryURL(category)+'">'+CategoryIndex[category][1]+'</a></span></span><div>';

  subsort=CategoryIndex[category];
  subcount=subsort.length;
  
  for(j=2;j<subcount;j++)
  {  categorycode+='<a href="'+genCategoryURL(subsort[j])+'">'+CategoryIndex[subsort[j]][1]+'</a>';  
  } 
  categorycode+="</div></td></tr>";
  
  return categorycode
}

function ShowCategoryGuider()
{ var i,j,rootcategory,sortcount,subcount,subsort,parent,categorycode;
 
  if(!CurrentCategory)CurrentCategory="0";
   
  if(CurrentCategory!="0")
  { if(CategoryIndex[CurrentCategory].length<3 && CategoryIndex[CurrentCategory][0]!="0")
  	{ CurrentCategory=CategoryIndex[CurrentCategory][0];
  	}
  }

  rootcategory=(CategoryIndex[CurrentCategory][0]=="0")?"0":CurrentCategory;
  

  categorycode='<TABLE cellSpacing=0 cellPadding=0 width="185" align="center" border="0"><TR><TD height=50 background="/images/guide_sort.gif"></TD></TR>';
  if(rootcategory!="0")
  { categorycode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="/images/guidefold2.gif" align="absMiddle"> <a href="'+genCategoryURL(CategoryIndex[rootcategory][0])+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
  	categorycode+=GenSingleCategory(rootcategory);
  }
  else
  { subsort=CategoryIndex[rootcategory];
  	sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { categorycode+=GenSingleCategory(subsort[i]);
    }  	
  }
  categorycode+='<TR><td height="10"></td></tr></TABLE>';
  document.write(categorycode);
}

ShowBrandGuider();
ShowCategoryGuider();
