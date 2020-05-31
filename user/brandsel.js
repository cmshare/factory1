var CurSortSelection1=0;
function GenOption1(cID,cTitle,cIndent)
{ var i,StrIndent="";
  for(i=0;i<cIndent;i++)StrIndent+="　　";
  document.write("<option value=\""+cID+"\" "+((cID==CurSortSelection1)?"selected":"")+">"+StrIndent+cTitle+"</option>");
}
function CreateBrandSelection(SelectName,SelectValue,DefaultOptionTitle,OnchageProcess)
{ CurSortSelection1=SelectValue;
  document.write("<select name=\""+SelectName+"\" onchange=\""+OnchageProcess+"\">");
  if(DefaultOptionTitle)document.write("<option value=\"0\">"+DefaultOptionTitle+"</option>");
document.write("</select>");
}