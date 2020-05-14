<?php require('conn.php');?>
var CurrentBrand,CurrentCategory;       
var BrandIndex=new Array();
var CategoryIndex=new Array();
<?php
function GenBrandIndex($brandid,$brandtitle,$parent){
  $res_sort=$GLOBALS['conn']->query('select * from `mg_category` where parent='.$brandid.' and recommend>0 order by sortorder',PDO::FETCH_ASSOC);
  $BrandArrayCode='BrandIndex["'.$brandid.'"]=new Array("'.$parent.'","'.$brandtitle.'"';
  foreach($res_sort as $row_sort){
    $BrandArrayCode.=',"'.$row_sort['id'].'"';
    GenBrandIndex($row_sort['id'],$row_sort['title'],$brandid);
  }
 echo $BrandArrayCode.");\r\n";
}

function GenBrandHot(){
  $res_sort=$GLOBALS['conn']->query('select * from `mg_category` where recommend>1 order by recommend desc',PDO::FETCH_ASSOC);
  $BrandArrayCode='BrandIndex["hot"]=new Array("","热销品牌"';
  foreach($res_sort as $row_sort){
    $BrandArrayCode.=',"'.$row_sort['id'].'"';
  }
 echo $BrandArrayCode.");\r\n";
}

function GenCategoryIndex($categoryid,$categorytitle,$parent){
  $res_sort=$GLOBALS['conn']->query('select * from `mg_sort` where parent='.$categoryid.' order by sortorder',PDO::FETCH_ASSOC);
  $CategoryArrayCode='CategoryIndex["'.$categoryid.'"]=new Array("'.$parent.'","'.$categorytitle.'"';
  foreach($res_sort as $row_sort){
    $CategoryArrayCode.=',"'.$row_sort['id'].'"';
    GenCategoryIndex($row_sort['id'],$row_sort['title'],$categoryid);
  }
  echo $CategoryArrayCode.");\r\n";
}

OpenDB();
GenBrandIndex('0','','');
GenBrandHot();
GenCategoryIndex('0','','');
CloseDB(); ?>


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

function GenSingleBrand(brandid)
{ var menu_class,categorycode="";
  var brandid2=(brandid=="hot")?"0":brandid;
        var brand_file="brandlist.htm?cid="+brandid2;
  if(BrandIndex[brandid].length>2)
  { if(brandid2==CurrentBrand)menu_class="gMenuOpen";
    else menu_class="gMenuClose";
  }
  else
  { menu_class="gMenuEmpty";
  }
  categorycode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+brand_file+'">'+BrandIndex[brandid][1]+'</a></span></span><div>';
  subsort=BrandIndex[brandid];
  subcount=subsort.length;
   
  for(j=2;j<subcount;j++)
  { categorycode+='<a href="brandlist.htm?cid='+subsort[j]+'">'+BrandIndex[subsort[j]][1]+'</a>'; 
  } 
  categorycode+='</div></TD></TR>';
  return categorycode
}

function ShowBrandGuider(){
  var i,j,RootBrand,sortcount,subcount,subsort,parent,brandcode="";
  if(!CurrentBrand)CurrentBrand="0";
  if(CurrentBrand!="0"){
    if(BrandIndex[CurrentBrand].length<3 && BrandIndex[CurrentBrand][0]!="0"){
      CurrentBrand=BrandIndex[CurrentBrand][0];
    }
  }
  RootBrand=(BrandIndex[CurrentBrand][0]=="0")?"0":CurrentBrand;
  
  brandcode+='<TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">';
  brandcode+='<TR>';
  brandcode+='   <TD background="images/guide_brand.gif" width="188" height="31"></TD>';
  brandcode+='</TR>';
  brandcode+='<TR>';
  brandcode+=' <TD vAlign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">';
  brandcode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
         
  if(RootBrand!="0")
  { brandcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="images/guidefold2.gif" align="absMiddle"> <a href="brandlist.htm?cid='+BrandIndex[RootBrand][0]+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
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
  brandcode+='      </table>';
  brandcode+='   </TD>';
  brandcode+='</TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>';
  brandcode+='</TABLE><img src="images/index_4.gif" width="190" height="12">';
  document.write(brandcode);
}

function GenSingleCategory(categoryid)
{ var guide_fold_image,guide_fold_disp_opt,catcode="";
        if(CategoryIndex[categoryid].length>2)
        { if(categoryid==CurrentCategory)menu_class="gMenuOpen";
                else menu_class="gMenuClose";
        }
        else
        { menu_class="gMenuEmpty";
        }

  catcode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="catlist.htm?cid='+categoryid+'">'+CategoryIndex[categoryid][1]+'</a></span></span><div>';

  subsort=CategoryIndex[categoryid];
  subcount=subsort.length;
  
  for(j=2;j<subcount;j++)
  {  catcode+='<a href="catlist.htm?cid='+subsort[j]+'">'+CategoryIndex[subsort[j]][1]+'</a>';  
  } 
  catcode+="</div></td></tr>";
  
  return catcode
}

function ShowCategoryGuider()
{ var i,j,RootCategory,sortcount,subcount,subsort,parent,catcode="";
  if(!CurrentCategory)CurrentCategory="0";
  if(CurrentCategory!="0")
  { if(CategoryIndex[CurrentCategory].length<3 && CategoryIndex[CurrentCategory][0]!="0")
    { CurrentCategory=CategoryIndex[CurrentCategory][0];
    }
  }
  RootCategory=(CategoryIndex[CurrentCategory][0]=="0")?"0":CurrentCategory;
  
  
  catcode+='<TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">';
  catcode+='<TR>';
  catcode+='   <TD background="images/guide_property.gif" width="188" height="31"></TD>';
  catcode+='</TR>';
  catcode+='<TR>';
  catcode+=' <TD vAlign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">';
  catcode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
  
  if(RootCategory!="0")
  { catcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="images/guidefold2.gif" align="absMiddle"> <a href="catlist.htm?cid='+CategoryIndex[RootCategory][0]+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
        catcode+=GenSingleCategory(RootCategory);
  }
  else
  { subsort=CategoryIndex[RootCategory];
        sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { catcode+=GenSingleCategory(subsort[i]);
    }   
  }
  catcode+='      </table>';
  catcode+='   </TD>';
  catcode+='</TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>';
  catcode+='</TABLE><img src="images/index_4.gif" width="190" height="12">';
  document.write(catcode);
}

if(self.location.href.indexOf("catlist")>0)
{ CurrentCategory=htmRequest("cid")
  ShowCategoryGuider();
} 
else
{ CurrentBrand=htmRequest("cid") 
  ShowBrandGuider();
  ShowCategoryGuider();
}

