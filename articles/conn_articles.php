<?php
header("content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");#�������������������������


function db_open(){
  global $conn;
  if(empty($conn)){
    try{
      $conn = new PDO("sqlite:data/articles.db");
      $conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
      $conn->exec('SET NAMES utf8'); 
    }
    catch (Exception $e){
      exit("Failed:".$e->getMessage());
    }
  }

}

function db_close(){
  global $conn;
  if(isset($conn))unset($conn);
}

function FilterText($strText){
  if($strText)  return htmlspecialchars($strText,ENT_QUOTES);
  else return $strText;
}

function page_query($select,$from,$where,$orderby,$pagesize){
  global $conn,$page,$total_pages,$total_records;
  if($where){
    $from.=' '.$where;
    if(stripos($where,' group ')) $count_sql='select count(*) from ('.$select.' '.$from.') as stat_table';
    else $count_sql='select count(*) '.$from;
  }
  else{
    $count_sql='select count(*) '.$from;
  }
  $total_records=$conn->query($count_sql)->fetchColumn(0);
  if(empty($total_records)) return false;
  $total_pages=(int)(($total_records+$pagesize-1)/$pagesize);
  $page=@$_GET['page'];
  if(is_numeric($page)){
    if($page<1)$page=1;
    else if($page>$total_pages)$page=$total_pages;
  }else $page=1;
  return $conn->query($select.' '.$from.' '.$orderby.' limit '.($pagesize*($page-1)).','.$pagesize,PDO::FETCH_ASSOC);
}

function PageReturn(){
  $args = func_get_args();
  if($args){
    if(count($args)>1){
      if($args[1]===-1)$script='history.go(-1);';
      else if($args[1]===-2)$script='self.location.href="admlogout.php";';
      else if(is_string($args[1]))$script='self.location.href="'.$args[1].'";';
      else{if($args[0])echo $args[0];db_close();exit(0);}
    }
    else $script='self.location.href="'.$_SERVER['HTTP_REFERER'].'";';
    if($args[0]) echo '<script>alert("'.$args[0].'");'.$script.'</script>';
    else echo '<script>'.$script.'</script>';
  }
  db_close();
  exit();
}

?>


