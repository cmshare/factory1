<?php require('../include/conn.php');
$mode=@$_GET['mode'];
if($mode=='logout'){
  setcookie('cmshop[userid]','',time()-1,'/');
  echo 'ok';
  exit(0);
}
else if($mode=='getinfo'){
  $userid=@$_COOKIE['cmshop']['userid'];
  if($_GET['userid']==$userid && is_numeric($_COOKIE['cmshop']['usergrade'])){
    db_open();
    echo GetUserInfo($userid);
    db_close();
  }
  else setcookie('cmshop[userid]','',time(),'/');
  exit(0);
}
 
function GetUserInfo($userid){ 
  $row=$GLOBALS['conn']->query('select `mg_users`.username,`mg_users`.deposit,`mg_users`.score,`mg_users`.grade,`mg_usrgrade`.title from `mg_users`,`mg_usrgrade` where `mg_users`.id='.$userid.' and `mg_users`.grade=`mg_usrgrade`.id',PDO::FETCH_ASSOC)->fetch(); 
  if($row){
    $user_title=$row['title'];
    if($row['grade']>1 && round($row['deposit'],2)==0 && $row['score']==0) $user_title='<SUP style="font-size:14px;color:#FF0000">准</SUP>'.$user_title;
    return  '<a href="'.WEB_ROOT.'usrmgr.htm">欢迎<font color=FF6600>'.$row['username'].'</font>，您是<font color=#FF6600>'.$user_title.'</font>，预存款余额<font color=#FF0000>'.round($row['deposit'],1).'</font>元，可用积分<font color=#FF000>'.$row['score'].'</font>分！</a>&nbsp; &nbsp;<a href="#" onclick="userlogoff()"><img src="'.WEB_ROOT.'images/icon_exit.gif" border=0 width=16 height=16 align="absMiddle"><u>退出</u>';
  } 
  return false;
}

function GetUnreadMsgCount($username){
  $msg_filter=@$_COOKIE[$username]['msgread'].@$_COOKIE[$username]['msgdelete'];
  if($msg_filter) $msg_sql="(sendto='all' and id not in (".FilterText($msg_filter)."))";
  else $msg_sql="sendto='all'";
  $msg_sql="select count(id)  from `mg_message` where property=1 and (sendto='$username' or $msg_sql)";
  $ret=$GLOBALS['conn']->query($msg_sql)->fetchColumn(0);
  return (empty($ret))?0:$ret;
}


function GetIP(){
  if(($cip=@$_SERVER["HTTP_CLIENT_IP"])) return $cip;
  else if(($cip=@$_SERVER["HTTP_X_FORWARDED_FOR"])) return $cip;
  else if(($cip=@$_SERVER["REMOTE_ADDR"])) return $cip;
  else return NULL;
}


session_start();
$username=FilterText(trim(@$_POST['username']));
$password=@$_POST['password'];
$verifycode=trim(@$_POST['verifycode']);
if(empty($username)) $errormsg='用户名不能为空！';
else if(empty($password)) $errormsg='密码不能为空！';
else if(empty($verifycode) || strtolower($verifycode)!=strtolower($_SESSION['VerifyCode']) ) $errormsg='验.证码无效！';
else{
  $errormsg='';
  db_open();
  $row=$conn->query("select id,grade,password,lastlogin from `mg_users` where username='$username'",PDO::FETCH_ASSOC)->fetch();
  if(empty($row)) $errormsg='该用户名不存在！';
  else if(($password_md5=md5($password))!=$row['password']){
    if(substr($password_md5,8,16)==$row['password']){
      #update md5 of password from 16 to 32 bits
      $conn->exec("update `mg_users` set password=md5('$password') where id=".$row['id']);
      goto label_ok; 
    }
    else $errormsg='密码不正确！';
  }
  else{
    label_ok:
    $userid=$row['id'];
    $cookieTimeout=time()+3600;
    setcookie('cmshop[userid]',$userid,$cookieTimeout,'/');
    setcookie('cmshop[username]',$username,$cookieTimeout,'/');
    setcookie('cmshop[usergrade]',$row['grade'],$cookieTimeout,'/');
    setcookie('cmshop[lastlogin]',$row['lastlogin'],$cookieTimeout,'/');
    $conn->exec('update mg_users set lastlogin=unix_timestamp(),logincount=logincount+1,lastip=\''.GetIP().'\' where id='.$userid);
    setcookie('cmshop[unreadmsg]',GetUnreadMsgCount($username),time()+3600,'/');
    echo GetUserInfo($userid);
  }    
  db_close();
} 
if($errormsg)echo $errormsg;
