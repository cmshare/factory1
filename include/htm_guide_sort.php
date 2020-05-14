<?php
require("conn.php");
OpenDB();

$mode=$_GET["mode"];
if($mode=="1") include("guide_brand.php"); 
else if($mode=="2") include("guide_category.php");
else if($mode=="3"){
  function gen_sort1($selec,$indent){
    global $conn;
    $res=$conn->query("select id,title from `mg_category` where parent = $selec order by sortorder",PDO::FETCH_NUM);
    foreach($res as $row){
      echo 'GenOption1('.$row[0].',"'.$row[1].'",'.$indent.');'.chr(10);
      gen_sort1($row[0],$indent+1);
    }
  }?>
var CurSortSelection1=0;
function GenOption1(cID,cTitle,cIndent){
  var i,StrIndent="";
  for(i=0;i<cIndent;i++)StrIndent+="　　";
  document.write('<option value="'+cID+'" '+((cID==CurSortSelection1)?'selected':'')+'>'+StrIndent+cTitle+'</option>');
}
function CreateBrandSelection(SelectName,SelectValue,DefaultOptionTitle,OnchageProcess){
  CurSortSelection1=SelectValue;
  document.write('<select name="'+SelectName+'" onchange="'+OnchageProcess+'">');
  if(DefaultOptionTitle)document.write('<option value="0">'+DefaultOptionTitle+'</option>');<?php
  gen_sort1(0,0);
  echo 'document.write("</select>");}';
}
else if($mode==4){
  function gen_sort2($selec,$indent){
    global $conn;
    $res=$conn->query("select id,title from `mg_sort` where parent=$selec order by sortorder",PDO::FETCH_NUM);
    foreach($res as $row){
      echo 'GenOption2('.$row[0].',"'.$row[1].'",'.$indent.');'.chr(10);
      gen_sort2($row[0],$indent+1 );
    }
  }?>

var CurSortSelection2=0;
function GenOption2(cID,cTitle,cIndent){
  var i,StrIndent="";
  for(i=0;i<cIndent;i++)StrIndent+="　　";
  document.write('<option value="'+cID+'" '+((cID==CurSortSelection2)?'selected':'')+'>'+StrIndent+cTitle+'</option>');
}
function CreateCategorySelection(SelectName,SelectValue,DefaultOptionTitle,OnchageProcess)
{ CurSortSelection2=SelectValue;
  document.write('<select name="'+SelectName+'" onchange="'+OnchageProcess+'">');
  if(DefaultOptionTitle)document.write('<option value="0">'+DefaultOptionTitle+'</option>');<?php
  gen_sort2(0,0);
  echo 'document.write("</select>");}';
}   
CloseDB();?>
