<?php
switch(@$_GET['action']){
    case 'checkusername': check_username();break;
    case 'selectusername': select_username();break;
    case 'yes': fill_details();break;
    case 'save': save_user();break;
    default: reg_home();break;

}

function check_username(){
  $username=trim($_POST['username']);
  if(strlen($username)>3){ 
    if(FilterText($username)==$username){
       $userid=$GLOBALS['conn']->query("select id from `mg_users` where username='$username'",PDO::FETCH_NUM)->fetchColumn(0);
       if($userid){ 
           echo '<script>alert("该用户名已经存在，请确认您是否已经在本站注册过！");history.go(-1);</script>';
       }
       else{
         db_close();
         echo '<script>self.location.href="?action=yes&username='.$username.'";</script>';
       }
    }   
    else echo '<script>alert("用户名包含非法字符！");history.go(-1);</script>';  
  }
  else echo '<script>alert("用户名长度必须大于3！");history.go(-1);</script>';   
}

function reg_home(){?>
   <TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="0" CELLSPACING="0">
     <TR>
       <TD HEIGHT="18" ALIGN="center"><B>欢迎您阅读我司服务条款！</B><br>
           <br>
         请仔细阅读本下述文本，我司将依据以下服务条款提供您所享有的服务。<br>
         如果您接受，请点按<span class="style1"><strong>“我同意”</strong></span>进入注册页面。<br></TD>
     </TR>
     <TR>
       <TD align=center valign="top"><table width="100%"  border="0" cellspacing="5" cellpadding="5">
           <tr>
             <td bgcolor="#F7F7F7">欢迎阅读我司服务条款协议(下称“本协议”)。<br>
               本协议阐述之条款和条件适用于您使用<?php echo WEB_NAME;?>所提供的服务(下称“服务”)。</td>
           </tr>
         </table>
           <table width="100%"  border="0" cellspacing="5" cellpadding="5">
             <tr>
               <td><?php echo $GLOBALS['conn']->query('select license from `mg_configs`',PDO::FETCH_NUM)->fetchColumn(0);?></td>
             </tr>
             <tr>
               <td bgcolor="#f7f7f7"><FONT style="FONT-SIZE: 9pt" color=#333333>·如果你同意以上所列的条款，请按本页最下方的【我同意】按钮<BR> ·如果不同意，请直接关闭页面</FONT></td>
             </tr>
         </table></TD>
     </TR>
     <TR>
       <FORM NAME="form1" METHOD="post" ACTION="reg.php?action=selectusername">
         <TD ALIGN="center"><input NAME="Submit4" type="image" src="images/tongyi.gif" width="37" height="47" border="0">
           &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <img src="images/butongyi.gif" width="37" height="47"></TD>
       </FORM>
     </TR>
   </TABLE>
   <br><?php
}


function select_username(){?>
  <TABLE WIDTH="95%" BORDER="0" ALIGN="center" CELLPADDING="4" CELLSPACING="1" bgcolor="#ffffff">
  <tr><td><p><span class="style4">关于注册本商城会员的特别提示：</span></p>
      <UL>
        <LI>本商城并不刻意要求您必须先注册成为本商店用户才能购买商品，您完全可以不用注册也可用普通会员价购买本商城所有商品。
        <LI>本商城仅仅需要能送货的必要信息：收货人姓名、地址、邮政编码和联系电话。对于像性别、身份证号码等对于送货没有任何帮助的信息，我们都不会要求您填写。 
        <LI>本商城将最大限度保护您的隐私，这一点请您放心。 
        <LI>当然您不购买商品也可以成为本站用户，请填写下面的信息注册。 
        <LI>如果您要将订购的商品寄给朋友，也请填写您自己的资料，您仅需在收银台处填写您朋友资料。</LI>
      </UL></td>
  </TR>
  </table>
  <script>
  function CheckUserName(objForm)
  { var FillUserName=objForm.username.value.trim();
    if(FillUserName.length>1)
    { var FilterChars=new Array(' ','(',')','<','>','&','\\','/','\'','"');
      var i,FilterLength=FilterChars.length;
      for(i=0;i<FilterLength;i++)
      { if(FillUserName.indexOf(FilterChars[i]) !=-1)
        { alert("用户名包含非法字符！");
          return false;
        } 
      }
      return true;
    }
    else
    { alert("用户名太短");
    }
    objForm.username.focus();
    return false;
  }            
   </script>
   <TABLE WIDTH="50%" BORDER="0" ALIGN="center" CELLPADDING="4" CELLSPACING="1" bgcolor="#ffffff">
   <FORM NAME="userinfo" METHOD="post" ACTION="reg.php?action=checkusername" onsubmit="return CheckUserName(this)">
   <TR>
      <TD width="13%" height="25" nowrap>选择用户名：</TD>
      <TD width="87%" height="25"><INPUT NAME="username" TYPE="text" class="input_sr" id="username" size="15" maxlength="16"> &nbsp; &nbsp; &nbsp;<input type="submit" value="下一步"></TD>
   </TR>
   <TR>
      <TD height="30" colspan="2">注：用户名长度必须大于3，可由数字、英文字母、中文字组成。<br>不允许包含非法字符，如标点、空格、括号、制表符号等。</TD>
   </TR></FORM>
   </TABLE>
   <br><?php
}


function fill_details(){
  $username=FilterText(trim($_GET['username']));?>
  <TABLE WIDTH="100%" BORDER="0" ALIGN="center" CELLPADDING="4" CELLSPACING="1" bgcolor="#ffffff">
  <FORM NAME="userinfo" METHOD="post" ACTION="reg.php?action=save" onsubmit="return CheckUserInfo(this)">
  <TR>
     <TD width="20%" height="25" align="right" bgcolor="#f7f7f7">用 户 名：</TD>
     <TD width="80%" height="25"><input type="hidden" name="username" value="<?php echo $username;?>"><INPUT NAME="username_display" TYPE="text" class="input_sr" disabled size="15" maxlength="16" value="<?php echo $username;?>"></TD>
  </TR>
  <TR>
     <TD height="25" bgcolor="#f7f7f7"><div align="right">密　　码：</div></TD>
     <TD height="25"><INPUT NAME="userpassword1" type="Password" class="input_sr" size="15" maxlength="16"><span class="style1">*</span> 长度必须大于6个字符。</TD>
  </TR>
  <TR>
     <TD height="25" bgcolor="#f7f7f7"><div align="right">确认密码： </div></TD>
     <TD height="25"><INPUT NAME="userpassword2" type="Password" class="input_sr" size="15" maxlength="16"><span class="style1">*</span> </TD>
  </TR>
  <TR>
     <TD height="25" bgcolor="#f7f7f7"><div align="right">电子信箱： </div></TD>
     <TD height="25"><INPUT NAME="usermail" TYPE="text" class="input_sr" size="15" maxlength="50"><span class="style1">*</span> 请您务必填写正确的E-mail地址，便于我们与您联系。</TD>
  </TR>
  <TR>
     <TD height="25" bgcolor="#f7f7f7"><div align="right">密码提问： </div></TD>
     <TD height="25"><INPUT NAME="userquestion" TYPE="text" class="input_sr" size="15" maxlength="16"><span class="style1">*</span> 在您忘记密码需要取回的时候，您需要回答该密码提示问题。 </TD>
  </TR>
  <TR>
     <TD height="25" bgcolor="#f7f7f7"><div align="right">密码答案： </div></TD>
     <TD height="25"><INPUT NAME="useranswer" TYPE="text" class="input_sr" size="15" maxlength="16"><span class="style1">*</span> 密码提示问题的答案。 </TD>
  </TR>
  <TR>
     <TD height="25" align="right" bgcolor="#f7f7f7">真实姓名： </TD>
     <TD height="25"><INPUT NAME="realname" TYPE="text" class="input_sr" size="15" maxlength="8">
        <select name="usersex" size="1"><option value="x" selected>选择性别..</option><option value="1"> &nbsp;女</option><option value="0"> &nbsp;男</option></select>
        <span class="style1">*</span>&nbsp;便于我们与您联系发货或者退款</TD>
  </TR>
  <TR>
     <TD height="25" align="right" bgcolor="#f7f7f7">所在地区： </TD>
     <TD height="25">
        <select name="provincelist" size="1"><option value="0" selected>请选择省份……</option></select>
        <select size="1" name="district"><option value='0'>请选择城市……</option></select> <span class="style1">*</span></TD>
  </TR>
  <TR>
     <TD height="25" align="right" bgcolor="#f7f7f7">电话号码： </FONT></TD>
     <TD height="25"><INPUT NAME="usermobile" TYPE="text" class="input_sr" size="15" maxlength="32"><span class="style1">*</span>请填写正确的号码，以便通知您订单信息或其它问题确认。</TD>
  </TR>
  <TR>
     <TD height="25" align="right" bgcolor="#f7f7f7">QQ 号 码：</TD>
     <TD height="25"><INPUT NAME="userqq" TYPE="text" class="input_sr" size="15" maxlength="16">&nbsp;便于我们网上联系</TD>
  </TR>
  <TR>
     <TD></TD>
     <TD height="30" valign="bottom"><INPUT  TYPE="submit"  VALUE="提  交" > &nbsp; &nbsp; &nbsp; <input type="reset"  value="清  除"></TD>
  </TR></FORM>
  </TABLE>
  <SCRIPT language="JavaScript" src="user/district.js" type="text/javascript"></SCRIPT>
  <script>
  var obj=document.forms["userinfo"];
  if(obj)InitDistrictSelection(obj);

  function CheckFormText(ElementTitle,obj,NoEmpty){ 
    var  tmpvalue=obj.value.trim();
    if(NoEmpty){
      if (tmpvalue=="") { obj.focus();alert(ElementTitle+"为空！");return false;} 
    }  
    tmpvalue=CheckBanChar(tmpvalue,"<>'\"");
    if(tmpvalue){
      obj.focus();alert(ElementTitle+"包含非法字符 "+tmpvalue); return false;
    }
    return true;
  }   

  function CheckUserInfo(myform){               
    if(myform.username.value.trim()==""){
      alert("用户名不能为空！");
      return false;
    }
    if(myform.userpassword1.value.trim().length < 6){
      myform.userpassword1.focus();
      alert("密码长度不能小于6，请重新输入！");
      return false;
    }
    if(myform.userpassword1.value != myform.userpassword2.value){
      myform.userpassword2.focus();
      myform.userpassword2.value = '';
      alert("两次输入的密码不同，请重新输入！");
      return false;
    }
    myform.usermail.value=myform.usermail.value.trim();
    if(myform.usermail.value.length>0){
      var re = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$");
      if(!re.test(myform.usermail.value)){
        alert("Email地址格式不正确！");
        myform.usermail.focus();
        return false;
      }
    }
    else{
      alert("Email不能为空！");
      myform.usermail.focus();
      return false;
    }
    if(!CheckFormText("密码提问",myform.userquestion,true))return false;
    if(!CheckFormText("密码答案",myform.useranswer,true))return false;
    if(!CheckFormText("真实名字",myform.realname,true))return false;
    if (isNaN(myform.usersex.value)){
      alert("请选择性别！");
      return false;
    }
    if(myform.district.value.trim()=="0"){
      myform.district.focus();
      alert("所在地区为空！");
      return false;
    }
    if(!CheckFormText("电话号码",myform.usermobile,true))return false; 
    else myform.usermobile.value=DBC2SBC(myform.usermobile.value);
    if(!CheckFormText("QQ号码",myform.userqq,false))return false;
    return true; 
  }
  </script>
   <br><?php
}

function usererr($errmsg){ ?>
  <table width=100% height=80 border=0 align=center cellpadding=0 cellspacing=1>
  <tr>
    <td width="100%" bgcolor=#FFFFFF>
     <table width=700 border=0 align=center cellpadding=2 cellspacing=0>
     <tr>
       <td><font color=#FF6600>用户注册失败</font></td>
     </tr>
     <tr>
       <td> ·<?php echo $errmsg;?><br>·<a href=javascript:history.go(-1)><font color=red>点击返回上一页</font></a> </td>
     </tr>
     </table>
    </td>
  </tr>
  </table><?php
}

function GetIP(){
  if(($cip=@$_SERVER["HTTP_CLIENT_IP"])) return $cip;
  else if(($cip=@$_SERVER["HTTP_X_FORWARDED_FOR"])) return $cip;
  else if(($cip=@$_SERVER["REMOTE_ADDR"])) return $cip;
  else return NULL;
}


function save_user(){
  if(@$_COOKIE['cmshop']['regtimes']==1){ 
    echo '<div align=center><br><br>对不起，您刚注册过用户。<br>请稍后再进行注册！</font></div><br>';
    return false;
  }
  $username=trim($_POST['username']);
  $userpassword=FilterText($_POST['userpassword1']);
  $usermail=FilterText($_POST['usermail']);
  $realname=FilterText(trim($_POST['realname']));
  if($username!=FilterText($username) || strlen($username)<4 || empty($usermail) || empty($userpassword) ){
    usererr('对不起，服务器出错了！<br>请稍后再进行注册！');
    return false;
  }
  $userid=$GLOBALS['conn']->query("select id from `mg_users` where username='$username'",PDO::FETCH_NUM)->fetchColumn(0);
  if($userid){
    usererr('您输入的用户名已经被注册，请选用其他的用户名！');
    return false;
  }
  
  $userquestion = FilterText(trim($_POST['userquestion']));
  $useranswer = trim($_POST['useranswer']);
  $usergrade=1;
  $usersex = $_POST['usersex'];
  $usermobile = FilterText(trim($_POST['usermobile']));
  $userqq = FilterText(trim($_POST['userqq']));
  $district = $_POST['district'];
  $remoteip = GetIP();
  $sql = "`mg_users` set username='$username',password=md5('$userpassword'),usermail='$usermail',userquestion='$userquestion',useranswer=md5('$useranswer'),realname='$realname',usersex=$usersex,usermobile='$usermobile',userqq='$userqq',district=$district,logincount=1,deposit=0,score=0,grade=$usergrade,property=0,address='',usertel='',receipt='',remark='',lastip='$remoteip',addtime=unix_timestamp(),lastlogin=unix_timestamp()";
  if ($GLOBALS['conn']->exec("update $sql where grade=0 limit 1") || $GLOBALS['conn']->exec("insert into " . $sql)){
   /*if(WEB_SITE===0){
     $row=$GLOBALS['conn']->query('select smtp,mailserverusername,mailserverpassword,sendfrommail,sendfromname from `mg_configs`',PDO::FETCH_ASSOC)->fetch();
     if($row){ 
      include('include/email.php');
      $MailSubject='您在'.WEB_NAME.'的注册信息！';
      $MailBody='<TABLE border=0 width="95%" align=center><TR><TD valign=middle align=top>'.$realname.' 您好：<br>您在<a href="http://'.WEB_DOMAIN.'/"><font color="#0000FF">'.WEB_NAME.'</font></a>的注册信息：<br>用户名：'.$username.'<br>密　码：'.$userpassword.'<br><center><font color=red>祝您购物愉快，我们将竭诚为您服务！</font></TD></TR></TABLE>';
     SendMail($usermail,$row['smtp'],$row['mailserverusername'],$row['mailserverpassword'],$MailSubject,$MailBody,$row['sendfrommail'],$row['sendfromname']);
    }
   }*/
  }
  else{
    echo 'error occur';
    exit(0);
  }

  ?>
  <table width=100% border=0 align=center cellpadding=0 cellspacing=0>
  <tr>
    <td width="100%" height=100>
      <table width=700 border=0 align=center cellpadding=0 cellspacing=0>
      <tr>
        <td height=80><br><p><font color=#FF6600>用户注册成功</font></p>
           恭喜 <font color="#FF6600"><?php echo  $username;?></font>，您已注册成为【<?php echo  WEB_NAME;?>】正式用户，请使用新注册的用户名/密码登录。<br><br>
           ·<a href="<?php echo WEB_ROOT;?>#"><font color=#FF0000>返回首页</font></a><br><br></td>
      </tr>
      </table></td>
  </tr>
  </table><?php
}?>
