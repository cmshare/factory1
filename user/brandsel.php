<?php require('../include/conn.php');?>
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
<?php
 function sort1($selec,$indent){
    global $conn,$OutCode;
    $res=$conn->query('select * from `mg_category` where parent = '.$selec.' order by sequence',PDO::FETCH_ASSOC);
    foreach($res as $row){
       $OutCode.='GenOption1('.$row['id'].',"'.$row['title'].'",'.$indent.');'."\r\n";
       sort1($row['id'],$indent+1);
    }
  }
  db_open();
  sort1(0,0); 
  $OutCode.='document.write("</select>");'."\r\n}";
  echo $OutCode;
  db_close();
?>
