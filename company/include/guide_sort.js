var CurrentCategory=htmRequest("cid");       

function genCategoryURL(cid){return "catlist.htm?cid="+cid;}

function GenSingleCategory(catdata) { 
  var menu_class=catdata.children?(catdata.id==CurrentCategory?"gMenuOpen":"gMenuClose"):"gMenuEmpty";
  var catcode='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+genCategoryURL(catdata.id)+'">'+catdata.title+'</a></span></span><div>';
  var subsort=catdata.children;
  var subcount=subsort && subsort.length;
  for(var i=0;i<subcount;i++){
    catcode+='<a href="'+genCategoryURL(subsort[i].id)+'">'+subsort[i].title+'</a>'; 
  } 
  catcode+='</div></TD></TR>';
  return catcode;
}
 
function GenHotCategory() { 
   var menu_class=(CurrentCategory)?"gMenuClose":"gMenuOpen";
   var catcode='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/hotsell.htm">热销品牌</a></span></span><div>';
   var subcount=CategoryHotIndex.length;
   for(var i=0;i<subcount;i++){
     var catdata=GetCategoryById(CategoryHotIndex[i]);
     if(catdata)catcode+='<a href="'+genCategoryURL(catdata.id)+'">'+catdata.title+'</a>'; 
   } 
   catcode+='</div></TD></TR>';
   return catcode;
}

function ShowCategoryGuider(){
 var catdata=null,catcode='\
  <TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">\
  <TR>\
     <TD background="images/guide_brand.gif" width="188" height="31"></TD>\
  </TR>\
  <TR>\
    <TD vAlign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">\
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
  if(!isNaN(CurrentCategory) && CurrentCategory>0 && (catdata=GetCategoryById(CurrentCategory))){
   if(!catdata.children && catdata.pid!=0){
     CurrentCategory=catdata.pid;
     catdata=GetCategoryById(CurrentCategory);
   } 
 }
 else CurrentCategory=0; 
 var rootCategory=(catdata && catdata.pid==0)?0:CurrentCategory;
 if(rootCategory==0){ 
   var sortcount=CategoryMap.length;
   catcode+=GenHotCategory();
   for(var i=0;i<sortcount;i++){
     catcode+=GenSingleCategory(CategoryMap[i]);
   }  	
 }
 else{
    catcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="images/guidefold2.gif" align="absMiddle"> <a href="'+genCategoryURL(catdata.pid)+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
    catcode+=GenSingleCategory(catdata);
 } 
 catcode+='</table></TD>\
   </TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>\
   </TABLE><img src="images/index_4.gif" width="190" height="12">';
 document.write(catcode);
}
 

function GenSingleCatSort(catdata){
  var subsort=catdata && catdata.children; 
  var subcount=subsort && subsort.length;
  var menu_class=subcount?"gMenuClose":"gMenuEmpty";
  var catcode='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+genCategoryURL(catdata.id)+'">'+catdata.title+'</a></span></span><div>';
  
  for(var i=0;i<subcount;i++){
    catcode+='<a href="'+genCategoryURL(subsort[i].id)+'">'+subsort[i].title+'</a>';  
  }  
  catcode+="</div></td></tr>";
  return catcode;
}

function ShowCatSortGuider(){  
  var catcode='\
  <TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">\
   <TR>\
     <TD background="images/guide_property.gif" width="188" height="31"></TD>\
   </TR>\
   <TR>\
     <TD valign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">\
       <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
  var sortcount=CategoryNavSortIndex.length;
  for(var i=0;i<sortcount;i++){
    var catdata=GetCategoryById(CategoryNavSortIndex[i]);
    if(catdata)catcode+=GenSingleCatSort(catdata);
  }  	
  catcode+='</TABLE></TD>\
    </TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>\
    </TABLE>\
    <img src="images/index_4.gif" width="190" height="12">';
  document.write(catcode);
}


ShowCategoryGuider();
/* if(self.location.href.indexOf("catlist")>0) */
ShowCatSortGuider();