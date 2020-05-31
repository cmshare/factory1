<?php
require("conn.php");
db_open();
$globalSortIndex=0;

$mode=$_GET["mode"];
if($mode=="0"){
  function GenCategoryMap($pid){
    global $conn,$globalSortIndex;
    $subcount=0;
    $arraycode='';
    $query=$conn->query('select id,pid,title,sortindex from mg_category where pid='.$pid.' and recommend>0 order by sequence desc',PDO::FETCH_NUM);
    foreach($query as $rs){
      if(++$globalSortIndex!=$rs[3]){//更新sortindex
        $conn->exec('update mg_category set sortindex='.$globalSortIndex.' where id='.$rs[0]);
      }
      if($subcount++==0)$arraycode='[';
      else $arraycode.=',';
      $arraycode.='{id:'.$rs[0].',pid:'.$pid.',title:"'.$rs[2].'",si:'.$rs[3];
      $children=GenCategoryMap($rs[0]);
      if($children) $arraycode.=',children:'.$children;
      $arraycode.='}';
    }
    if($subcount==0)return false;
    else return $arraycode.']';
  }

  function GenCategoryHot(){
    $query=$GLOBALS['conn']->query("select id from mg_category where recommend>1 order by recommend desc",PDO::FETCH_NUM);
    $ArrayCode='';
    foreach($query as $rs){
      if($ArrayCode) $ArrayCode.=','.$rs[0];
      else $ArrayCode='['.$rs[0];
    }  
    return $ArrayCode?$ArrayCode.']':'[]';
  }

  function GenCategoryNavSort(){
    $query=$GLOBALS['conn']->query("select id from mg_category where property>0 order by property desc",PDO::FETCH_NUM);
    $ArrayCode='';
    foreach($query as $rs){
      if($ArrayCode) $ArrayCode.=','.$rs[0];
      else $ArrayCode='['.$rs[0];
    }  
    return $ArrayCode?$ArrayCode.']':'[]';
  }
  echo 'var CategoryMap='.GenCategoryMap(0).";\r\n"; 
  echo 'var CategoryHotIndex='.GenCategoryHot().";\r\n"; 
  echo 'var CategoryNavSortIndex='.GenCategoryNavSort().";\r\n"; 
?>

function GetCategoryById(cid){
    var CatNestSearch=function(cid,catdata){
      var count=catdata && catdata.length;
      for(var i=0;i<count;i++){
        if(catdata[i].id==cid)return catdata[i];
        else{var ret=catdata[i].children;if(ret){ret=CatNestSearch(cid,ret);if(ret)return ret;}}
      }
      return null;
    }
    return CatNestSearch(cid,CategoryMap);
}

function CreateCategorySelection(SelectName,SelectValue,DefaultOptionTitle,OnchageProcess){
  var GenOption=function(catdata,SelectValue,cIndent){
    var count=catdata.length;
    var options='',indent_sign='';
    for(var i=0;i<cIndent;i++) indent_sign+='|-----';
    for(var i=0;i<count;i++){
      var children=catdata[i].children;
      var value=catdata[i].id;
      if(value==SelectValue) options+='<option selected  value="'+value+'">';
      else options+='<option value="'+value+'">';
      options+=indent_sign+catdata[i].title+'</option>';
      if(children)options+=GenOption(children,SelectValue,cIndent+1);
    }
    return options;
  }
  var selector='<select name="'+SelectName+'" onchange="'+OnchageProcess+'">';
  if(DefaultOptionTitle)selector+='<option value="0">'+DefaultOptionTitle+'</option>';
  selector+=GenOption(CategoryMap,SelectValue,0);
  selector+='</select>';
  document.write(selector);
}
<?php
}
else if($mode=="1") include("guide_category.php"); 

else if($mode=="2") include("guide_catsort.php");

else if($mode=="3"){
  define('TBL_CATEGORY_DST','wxhzp.cm_store_category');
  define('TBL_CATEGORY_SRC','meray_db.mg_category');
  $maxid_src=$conn->query('select max(id) from '.TBL_CATEGORY_SRC)->fetchColumn(0);
  $maxid_dst=$conn->query('select max(id) from '.TBL_CATEGORY_DST)->fetchColumn(0);
  
  while($maxid_dst<$maxid_src){
    $maxid_dst++;
    $conn->exec('insert into '.TBL_CATEGORY_DST.' set id='.$maxid_dst.',pid=-1,cate_name=\'\',sort=0,pic=\'\',is_show=0,add_time=unix_timestamp()');
  }
  $conn->exec('update ('.TBL_CATEGORY_DST.' as a inner join '.TBL_CATEGORY_SRC.' as b on a.id=b.id) set a.pid=b.pid,a.cate_name=b.title,a.sort=b.sequence,a.is_show=(b.recommend>0)');
  
  function GetCategoryByPID($pid){
     return  $GLOBALS['conn']->query('select id,cate_name,pic from '.TBL_CATEGORY_DST.' where pid='.$pid.' and is_show order by sort desc,id desc')->fetchAll();
  }
  
  $rootCategory = GetCategoryByPID(0);
  foreach ($rootCategory as $k => $cat2) {
     $subcatId=$cat2['id'];
     $subCategory=GetCategoryByPID($subcatId);
     if($subcatId==1) foreach ($subCategory as $j => $cat3) {
        $subCategory[$j]['children'] =GetCategoryByPID($cat3['id']);
     }
     $rootCategory[$k]['children'] =$subCategory; 
  } 
  
  echo '{"status":200,"msg":"ok","data":',json_encode($rootCategory,JSON_UNESCAPED_UNICODE),'}';
}
/* end of $mode==3 */
db_close();?>
