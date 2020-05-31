<?php require('../include/conn.php');
$action=@$_GET['action'];
if($action!='resetpsw'){
  if(!CheckLogin(0)){
    echo '<br><br><br><p align="center">请先登录</p><br><br><br>';
    exit(0);
  }
}

db_open();
switch($action){
  case 'receiveaddr':
        receiveaddr();
        break;
  case 'customerinfo':
        customerinfo();
        break;
  case 'changequestion':
        changequestion();
        break;
  case 'changepass':
        changepass();
        break;
  case 'resetpsw':
        resetpsw();
        break;
}
db_close();

#收货人信息
function receiveaddr(){
  global $conn,$LoginUserID;
  $receipt=FilterText(trim(@$_POST['receipt']));
  $address=FilterText(trim(@$_POST['address']));
  $usertel=FilterText(trim(@$_POST['usertel']));
  $conn->exec("update `mg_users` set receipt='$receipt',address='$address',usertel='$usertel' where id=$LoginUserID");
  echo 'ok';
}


#用户资料
function customerinfo(){
  global $conn,$LoginUserID;
  $usermail=FilterText(trim(@$_POST['usermail']));
  if(empty($usermail)){
    echo '<script language=javascript>alert("请填写电子邮件！");history.go(-1);</script>';
    return false;
  }
  $usermail=FilterText(trim(@$_POST['usermail']));
  $usermobile=FilterText(trim(@$_POST['usermobile']));
  $realname=FilterText(trim(@$_POST['realname']));
  $usersex=@$_POST['usersex'];
  $district=@$_POST['district'];
  $userqq=FilterText(trim(@$_POST['userqq']));
  $useranswer=FilterText(trim(@$_POST['useranswer']));
  if($useranswer)$set_useranswer=",useranswer=md5('$useranswer')";
  else $set_useranswer='';
  $conn->exec("update `mg_users` set usermail='$usermail',usermobile='$usermobile',userqq='$userqq',usersex=$usersex,realname='$realname',district=$district $set_useranswer where id=$LoginUserID");
  echo '<script language=javascript>alert("您的个人资料修改成功！");history.go(-1);</script>';
}

#修改安全问题
function changequestion(){
  global $conn,$LoginUserID;
  $userquestion=FilterText(trim(@$_POST['userquestion']));
  $useranswer=FilterText(trim(@$_POST['useranswer']));
  $userpassword=FilterText(trim(@$_POST['userpassword']));
  if(empty($userquestion) || empty($useranswer)){
    echo '提示问题或答案不能为空！';
    return false;
  }
  else if(empty($userpassword)){
    echo '用户密码不能为空！';
    return false;
  }
  else{
    $originpwd=$conn->query('select password from `mg_users` where id='.$LoginUserID)->fetchColumn(0);
    if($originpwd!=md5($userpassword))
    { echo '对不起，您输入的用户密码验证错误！';
      return false;
    }
    $conn->exec("update `mg_users` set userquestion='$userquestion',useranswer=md5('$useranswer') where id=$LoginUserID");
    echo '密码安全问题更改成功！';
  }
}


function changepass(){
  global $conn,$LoginUserID;
  $userpassword=FilterText(trim(@$_POST['userpassword']));
  $newpassword=FilterText(trim(@$_POST['newpassword']));
  if(empty($userpassword)||empty($newpassword)){
    echo '用户原密码或新密码不能为空！';
    return false;
  }
  $originpwd=$conn->query('select password from `mg_users` where id='.$LoginUserID)->fetchColumn(0);
  if($originpwd!=md5($userpassword)){
    echo '对不起，您输入的原密码验证错误！';
    return false;
  }
  else{
    $conn->exec("update `mg_users` set password=md5('$newpassword') where id=$LoginUserID");
    echo '密码更改成功！';
  }
}



function resetpsw(){
  global $conn;
  $username=FilterText(trim(@$_POST['username']));
  $newpwd=FilterText(trim(@$_POST['newpassword']));
  $useranswer=FilterText(trim(@$_POST['useranswer']));
  if($username && $useranswer && $newpwd){
    $userid=$conn->query("select id from `mg_users` where username='$username' and useranswer=md5('$useranswer')")->fetchColumn(0);
    if($userid){ 
      $conn->exec("update `mg_users` set password=md5('$newpwd') where id=$userid");
      echo '您的新密码已成功设置，请重新登录！';
    }
    else{
      echo '您输入的问题答案不正确！';
    }
    return false;
  }
}
?>
