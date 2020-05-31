var CurrentCategory;

function genCategoryURL(cid){return "/category/cat"+cid+".htm";}

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
  var catdata=null,catcode='<TABLE cellSpacing=0 cellPadding=0 width="185" align="center"  border="0"><TR><TD height=50 background="/images/guide_category.gif"></TD></TR>';
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
     catcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="/images/guidefold2.gif" align="absMiddle"> <a href="'+genCategoryURL(catdata.pid)+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
     catcode+=GenSingleCategory(catdata);
  } 
  catcode+='<TR><td height="10"></td></tr></TABLE>';
  return catcode;
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
  var catcode='<TABLE cellSpacing=0 cellPadding=0 width="185" align="center" border="0"><TR><TD height=50 background="/images/guide_sort.gif"></TD></TR>';
  var sortcount=CategoryNavSortIndex.length;
  for(var i=0;i<sortcount;i++){
    var catdata=GetCategoryById(CategoryNavSortIndex[i]);
    if(catdata)catcode+=GenSingleCatSort(catdata);
  }  	
  catcode+='<TR><td height="10"></td></tr></TABLE>';
  return catcode;
}
 
if(typeof(CategoryMap)=="undefined"){
   document.write("<div id='guide_sort'></div>");
   loadScript("/include/category.js",function(){
    var obj=document.getElementById("guide_sort");
    obj.innerHTML=ShowCategoryGuider()+ShowCatSortGuider();
  });
}
else{
  document.write(ShowCategoryGuider());
  document.write(ShowCatSortGuider());
}
