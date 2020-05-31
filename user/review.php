<?php require('../include/conn.php');

if(!CheckLogin(0)){ 
  echo '<script>alert("请先登录！")</script>'; 
  exit(0);
}

switch(@$_POST['mode']){
  case 'get':
	$ProductID=$_POST['id'];
        if(is_numeric($ProductID)){
          include('m_reviews.php');
          db_open();
          show_product_reviews($LoginUserID,$ProductID);
          db_close();
        }
        break;
  case 'del':
        $ReviewID=$_POST['reviewid'];
  	if(is_numeric($ReviewID) && $ReviewID>0){
          $errmsg='';
          db_open();
  	  $rs=$conn->query('select productid,audit from `mg_review` where productid>0 and id='.$ReviewID,PDO::FETCH_ASSOC)->fetch();
          if($rs){
  	    if($rs['audit']==0){
              mark_product($rs['productid']);  
              $conn->exec('update `mg_review` set productid=0 where id='.$ReviewID);
  	      $errmsg="评论删除成功！";
            }
   	    else{
  	      $errmsg='对不起，无法删除已经审核的评论！';
            }         
          }
          db_close();
          echo $errmsg;
          exit(0); 
       }
       break;
  case 'add': 
  	$ProductID=$_POST['productid'];
        $remark=FilterText(trim($_POST['remark']));	
        $vote=$_POST['vote'];
        if(is_numeric($ProductID) && is_numeric($vote) && $ProductID>0 && $remark){
          db_open();
  	  $ShopUserName=$conn->query('select username from `mg_users` where id='.$LoginUserID)->fetchColumn(0);
          if($ShopUserName){
            $ip=GetIP();
            $sql="`mg_review` set productid=$ProductID,username='$ShopUserName',remark='$remark',vote=$vote,reply='',audit=0,actiontime=unix_timestamp(),ip='$ip'";
            if($conn->exec('update '.$sql.' where productid=0 limit 1') || $conn->exec('insert into '.$sql)){
  	      echo '<script>alert("评论发表成功！");top.window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
  	      mark_product($ProductID);
            }
          }
          db_close();
        }
        break;
}
function mark_product($ProductID){
  $key='productreviews';
  $viewlist=$_COOKIE[$key];
  if(!$viewlist) setcookie($key,'|'.$ProductID.'|',time()+7*24*68*60,'/');
  else if (!strstr($viewlist,'|'.$ProductID.'|')) setcookie($key,$viewlist.$ProductID.'|',time()+7*24*68*60,'/');
}

function GetIP(){
  if(($cip=@$_SERVER["HTTP_CLIENT_IP"])) return $cip;
  else if(($cip=@$_SERVER["HTTP_X_FORWARDED_FOR"])) return $cip;
  else if(($cip=@$_SERVER["REMOTE_ADDR"])) return $cip;
  else return NULL;
}
?> 
