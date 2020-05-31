<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();


$action=@$_GET['action'];
if($action){
  switch($action){
    case 'edit_save': edit_save();break;
    case 'add_save': add_save();break;
    case 'del_save': del_save();break;
    case 'pid_save': pid_save();break;
  }
  PageReturn('参数错误！');
}  

/*
function nestreverse($pid){
  global $conn;
  $max_sequence=$conn->query('select max(sequence) from mg_category where pid='.$pid)->fetchColumn(0);
  if($max_sequence!==false){
    $conn->exec('update mg_category set sequence='.$max_sequence.'-sequence where pid='.$pid);
    $query=$conn->query('select id from mg_category where pid='.$pid,PDO::FETCH_NUM);
    foreach($query as $rs){
      nestreverse($rs[0]);
    }
  }
}*/


function edit_save(){ 
  $cid=@$_POST['cid'];
  $pid=@$_POST['pid'];
  $recommend=@$_POST['recommend'];
  if(is_numeric($cid) && $cid>0 && is_numeric($pid) && $pid>=0 && is_numeric($recommend) && $recommend>=0){
    $sequence=@$_POST['sequence'];
    if(!is_numeric($sequence)) $sequence=0;
    $isbrand=(@$_POST['isbrand']==='1')?'1':'0';
    $title=htmlspecialchars(trim(@$_POST['title']));
    $sql='update mg_category set title="'.$title.'",sequence='.$sequence.',pid='.$pid.',recommend='.$recommend.',isbrand='.$isbrand.' where id = '.$cid;
    if($GLOBALS['conn']->exec($sql)) PageReturn('保存成功');
    else PageReturn('没有修改~');
  }
}

function add_save(){ 
  global $conn;
  $pid=@$_POST['pid'];
  $recommend=@$_POST['recommend'];
  if(is_numeric($pid) && $pid>=0 && is_numeric($recommend) && $recommend>=0){
    $title=htmlspecialchars(trim(@$_POST['title']));
    $sequence=@$_POST['sequence'];
    if(!is_numeric($sequence)) $sequence=0;
    $isbrand=(@$_POST['isbrand']==='1')?'1':'0';
    $sql='mg_category set title="'.$title.'",sequence='.$sequence.',pid='.$pid.',recommend='.$recommend.',isbrand='.$isbrand;
    if($conn->exec('update '.$sql.' where pid=-1 order by id asc limit 1') || $conn->exec('insert into '.$sql)) PageReturn('分类添加成功');
  }  
}

function del_save(){
  global $conn;
  $cid=@$_POST['cid'];
  if(is_numeric($cid) && $cid>0){
    if($conn->query('select id from mg_category where pid='.$cid.' limit 1')->fetchColumn(0)) PageReturn('该分类有子分类，请先删除该分类的所有子分类！');
    else if($conn->query('select id from mg_product where cids like \'%,'.$cid.',%\' and recommend>0 limit 1')->fetchColumn(0)) PageReturn('该分类下有商品存在，无法删除！');
    else if($conn->exec('update mg_category set pid=-1,title=null where id='.$cid)) PageReturn('分类删除成功');
  }
}


function pid_save(){ 
  $cid=@$_POST['cid'];
  $pid_new=@$_POST['sequence'];
  if(is_numeric($cid) && $cid>0 && is_numeric($pid_new) && $pid_new>=0){
    $sql='update mg_category set pid='.$pid_new.' where id = '.$cid;
    if($GLOBALS['conn']->exec($sql)) PageReturn('修改成功');
    else PageReturn('没有修改~');
  }
}

$pid=@$_GET['pid'];
if(!is_numeric($pid) || $pid<0) $pid=0;
$catnav='';
$root_title=false;
if($pid){
  $catid=$pid;
  while($catid){
    $rs=$conn->query('select pid,title from mg_category where id='.$catid,PDO::FETCH_NUM)->fetch();
    if($rs){
      $link='<a href="?pid='.$catid.'">'.$rs[1].'</a>';
      if($catnav)$catnav=$link.' &gt; '.$catnav;
      else{$catnav=$link;$root_title=$rs[1];} 
      $catid=$rs[0];
    }
    else break;
  }
}
if($root_title===false){
  $pid=0;
  $root_title='根分类';
} 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
    <td height="20"  background="images/topbg.gif" bgcolor="#F7F7F7">
       <table width="100%" border="0" cellpadding="0" cellspacing="0">    	
    	  <tr><td ><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>商品分类</font></a><?php
     if($catnav)echo ' &gt; ',$catnav;?></b></td>
    	      <td align="right"><a href="#" onclick="AddCategory();return false;"><u>[新增分类]</u></a>&nbsp; &nbsp; <a href="mg_catsort.php"><u>[导航分类设置]</u></a>&nbsp; &nbsp; <a href="mg_hotcat.php"><u>[热销品牌设置]</u></a>&nbsp; &nbsp; <a href="mg_sharebrands.php"><u>[共享品牌管理]</u></a>&nbsp; &nbsp; <a href="mg_htmgen.php?mode=guide_category"><u>[应用更新输出]</u></a></td>
    	  </tr>
    	  </table>
    </td>
</tr>
<tr>
  <td height="100%" valign="top">
  <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#f7f7f7" align="center" height="20">
     <td width="8%" background="images/topbg.gif"><strong>排序</strong></td>
     <td width="32%" background="images/topbg.gif"><strong>分类名称</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>下级分类</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>ID</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>状态</strong></td>
     <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
   </tr><?php
$cate_options='<option value="'.$pid.'">'.$root_title.'</option>';
$query=$conn->query('select a.*,b.count from mg_category as a left join (select pid,count(*) as count from mg_category group by pid) as b on a.id=b.pid where a.pid='.$pid.' order by sequence desc',PDO::FETCH_ASSOC);
foreach($query as $rs){
  $recommend=$rs['recommend'];
  $cat_type=($rs['isbrand']>0)?'品牌':'分类';
  if($recommend==1){
    $title_show=$rs['title'];
    $cat_status='显示'.$cat_type;
  }
  else if($recommend>1){
    $title_show='<span style="color:#FF6600;">'.$rs['title'].'</span> <img src="images/hot01.gif" border=0 >';
    $cat_status='热销'.$cat_type;
  }
  else{
    $title_show='<span style="color:#AfAfAf;text-decoration:line-through">'.$rs['title'].'</span>';
    $cat_status='<span style="color:#AfAfAf;">隐藏'.$cat_type.'</span>';
  }  
  echo '<tr height=25 align="center" bgcolor="#FFFFFF"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)"><td>',$rs['sequence'],'</td><td align="left">&nbsp; ',$title_show,'</td>
  <td><a href="?pid=',$rs['id'],'"><u> &nbsp;',((int)$rs['count']),'&nbsp; </u></a></td>
  <td>',$rs['id'],
  '<td>',$cat_status,'</td>
  <td><input type="button" value="转移" onclick="MoveCategory(',$rs['id'],')"> <input type="button" value="编辑" onclick="EditCategory(',$rs['id'],')"> <input type="button" value="删除" onclick="DeleteCategory(',$rs['id'],')"></td>
  </tr>';
  $cate_options.='<option value="'.$rs['id'].'" pid="'.$rs['pid'].'" sort="'.$rs['sequence'].'" recommend="'.$recommend.'" isbrand="'.$rs['isbrand'].'">|-----'.$rs['title'].'</option>';
}?>
   </table>

  </td>
</tr>
</table>
<form id="CateEditor" style="display:none;margin:10px" method="post">
  <div style="display:flex">				
	  <strong style="width:60px;text-align:right;padding-right:5px;">分类名称</strong>
    <input style="flex:1;" name="title" type="text">
  </div> 
  <div style="display:flex">				
	  <strong style="width:60px;text-align:right;padding-right:5px;">父级分类</strong>
    <select style="width: 213px;overflow:hidden" name="pid"><?php echo $cate_options;?></select>
  </div> 
  <div style="display:flex">				
	  <strong style="width:60px;text-align:right;padding-right:5px;">排列序号</strong>
    <input style="flex:1;" name="sequence" type="text">
  </div>
  <div style="display:flex">				
    <strong style="width:60px;text-align:right;padding-right:5px;">分类属性</strong>
    <input name="isbrand" type="radio" checked value="0">一般分类 &nbsp; <input name="isbrand" type="radio" value="1">品牌名称
  </div>  
  <div style="display:flex">				
	  <strong style="width:60px;text-align:right;padding-right:5px;">分类状态</strong>
     <input type="hidden" name="cid">
     <input name="recommend" type="radio" checked value="?">显示分类 &nbsp; <input name="recommend" type="radio" value="0">隐藏分类
  </div> 
  <div style="margin-top:10px;"><input type="button" value="确定" onclick="if(CheckFormCategory())closeDialog(true)"> &nbsp; <input type="button" value="取消" onclick="closeDialog()"></div> 						                      
</form>
<script>
var FormCategory=document.getElementById("CateEditor");
function getCateOptionById(catid){
  var pid=FormCategory.pid;
  for(var i=pid.length-1;i>=0;i--){
    if(pid.options[i].value==catid){
      return pid[i];
    }
  }
}

function CheckFormCategory(){
  var title=FormCategory.title.value.trim();
  if(title===""){
    alert('分类名称为空');
    FormCategory.title.focus();
    return false;
  } 
  var sequence=FormCategory.sequence.value.trim();
  if(sequence==="" || isNaN(sequence)){
    alert('分类序号无效');
    FormCategory.sequence.focus();
    return false;
  } 
  return true;
}

function EditCategory(catid){
  var option=getCateOptionById(catid);
  var recommend=option.getAttribute('recommend');
  FormCategory.title.value=option.innerText.replace(/^\|-*/,'');//过滤分类名前的|-----
  FormCategory.sequence.value=option.getAttribute('sort');
  FormCategory.recommend[0].value=(recommend>0)?recommend:1;
  FormCategory.recommend[recommend>0?0:1].checked=true;
  FormCategory.recommend[1].disabled=(recommend>1);//热销品牌不可隐藏
  FormCategory.isbrand[option.getAttribute('isbrand')>0?1:0].checked=true;
  AsyncDialog('编辑分类',FormCategory,300,60,function(ret){
    if(ret){
      FormCategory.cid.value=catid;
      FormCategory.action="?action=edit_save";
      FormCategory.submit();
    }
  });
}

function AddCategory(){
  FormCategory.title.value='';
  FormCategory.sequence.value='';
  FormCategory.recommend[0].value='1';
  FormCategory.recommend[0].checked=true;
  FormCategory.recommend[1].disabled=false;
  FormCategory.isbrand[0].checked=true;
  AsyncDialog('添加分类',FormCategory,300,60,function(ret){
    if(ret){
      FormCategory.action="?action=add_save";
      FormCategory.submit();
    }
  });
}

function DeleteCategory(catid){
  var option=getCateOptionById(catid);
  var title=option.innerText.replace(/^\|-*/,'');//过滤分类名前的|-----
  if(confirm('确定删除分类：【'+title+'】?')){
    FormCategory.cid.value=catid;
    FormCategory.action="?action=del_save";
    FormCategory.submit();
  }
}

function MoveCategory(catid){
  var option=getCateOptionById(catid);
  var title=option.innerText.replace(/^\|-*/,'');//过滤分类名前的|-----
  var html='<div style="width:350px;margin:8px auto;text-align:center;">设置分类【'+title+'】的新父类ID:<br><input id="input_parentid" type="text" maxlength=8 value="<?php echo $pid;?>" style="width:150px;text-align:center;">\
  <div style="margin-top:10px;"><input type="button" value="确定" onclick="closeDialog(true)"> &nbsp; <input type="button" value="取消" onclick="closeDialog()"></div>\
  </div>';
  AsyncDialog('编辑分类',html,350,60,function(ret){
    if(ret){
      var new_pid=document.getElementById('input_parentid').value;
      if(new_pid==="" || isNaN(new_pid) || new_pid<0)alert('父类ID无效');
      else if(new_pid==<?php echo $pid;?>)alert('没有修改父类ID');
      else{
        FormCategory.cid.value=catid;
        FormCategory.sequence.value=new_pid;//复用
        FormCategory.action="?action=pid_save";
        FormCategory.submit();
      }
     
    }
  });
}

</script>
</body>
</html><?php
db_close();?>
