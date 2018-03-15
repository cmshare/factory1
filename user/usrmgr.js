var cur_usr_page=1;

function show_logout()
{ var obj=document.getElementById("userbox");
  if(obj)obj.innerHTML='<table border=0 width="100%" height="100%"><tr valign="middle"><td width="47%" align="right"><img src="images/nologin.gif"></td><td width="53%">请先登录网站！！！</td></tr></table>';
}
 
function UpdateUserInfo()
{ if(OnlineUserID)
  { var ret=SyncPost("",WebRoot+"user/login.php?mode=getinfo&userid="+OnlineUserID);
    if(Check_Loginfo(ret))
    { Write_Loginfo(ret);
      login_show_usrinfo(ret);
    }
  }
}

function CheckFormText(ElementTitle,obj,NoEmpty)
{  var  tmpvalue=obj.value.trim();
   if(NoEmpty)
   { if (tmpvalue=="") { obj.focus();alert(ElementTitle+"为空！");return false;} 
   }  
   tmpvalue=CheckBanChar(tmpvalue,"<>'\"");
   if(tmpvalue) 
   { obj.focus();alert(ElementTitle+"包含非法字符 "+tmpvalue); return false;
   }
   return true;
}   
	
function Check_UserStuff(myform)
{ if(!CheckFormText("真实姓名",myform.realname,true))return false;    
  if (myform.district.value.trim()=='0')
  { myform.district.focus();
    alert("请选择所在地区！"); 
    return false;
  }
  myform.usermail.value=myform.usermail.value.trim();
  if(myform.usermail.value.length>0)
  { var re = new RegExp('^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$');
    if(!re.test(myform.usermail.value))
    { alert("Email地址格式不正确！");
      myform.usermail.focus();
      return false;
    }
  }
  else 
  { alert("Email不能为空！"); 
    myform.usermail.focus();
    return false;
  }
  if(!CheckFormText("电话号码",myform.usermobile,true))return false; 
  else myform.usermobile.value=DBC2SBC(myform.usermobile.value);
  if(!CheckFormText("QQ号码",myform.userqq,false))return false;
  return true;
}
	
function Check_PswQuestion(psw_answer,username)
{ if(!psw_answer) alert("请输入答案！");
  else
  { var formcontent="useranswer="+encodeURIComponent(psw_answer)+"&username="+encodeURIComponent(username);
    var ret=SyncPost(formcontent,WebRoot+"user/disuser.php?action=resetpsw&state=2");
    if(ret && ret.indexOf("新密码")>=0) document.getElementById("userbox").innerHTML=ret;
    else if(ret && ret.indexOf("不正确")>=0) alert(ret);
    else alert("系统正忙，请稍候再试！");
  } 
}

function Resetpsw_ModifyPsw(objform)
{ var userpassword1=objform.userpassword1.value.trim();
  if(userpassword1=="") 
  { objform.userpassword1.focus();
    alert("对不起，请填写新密码！");
    return false;
  }
  var userpassword2=objform.userpassword2.value.trim();
  if(userpassword2=="") 
  { objform.userpassword2.focus();
    alert("对不起，请确认新密码！");
    return false;
  }
  if(userpassword1!=userpassword2)
  { alert("两次输入的密码不一致！");
    return false;
  }
  var formcontent="username="+encodeURIComponent(objform.username.value)+"&newpassword="+encodeURIComponent(userpassword1)+"&useranswer="+encodeURIComponent(objform.useranswer.value);
  var ret=SyncPost(formcontent,WebRoot+"user/saveuserinfo.php?action=resetpsw");
  if(ret && ret.indexOf("成功")>=0)
  { alert(ret);
    self.location.href="?";
  }
  else
  { alert("操作失败！");
  }
}

function show_customerinfo()
{ if(OnlineUserID)
  { var OnLoadUserInfo=function(info)
    { var UserInfoBox=document.getElementById("userbox");
      if(info)
      { UserInfoBox.innerHTML=info;
        var obj=document.forms["userinfo"];
        if(obj)InitDistrictSelection(obj);
      } 
      else UserInfoBox.innerHTML="<p align=center>服务器请求失败，可能是您的网速太慢，请刷新重试!</p>";
    }  
    //document.getElementById("manage_item").innerHTML=" &gt;&gt; 个人资料";
    document.getElementById("userbox").innerHTML="<p align=center style='color:#FF0000'>正在加载数据，请稍候...</p>";;
    AsyncPost("?",WebRoot+"user/disuser.php?action=customerinfo",OnLoadUserInfo);
  }else show_logout();
  return false;
}    	


function check_if_user_exist(username)
{ if(!username) alert("请输入用户名！");
  else
  { var formcontent="username="+encodeURIComponent(username);
    var ret=SyncPost(formcontent,WebRoot+"user/disuser.php?action=resetpsw&state=1");
    if(ret && ret.indexOf("提问")>=0)document.getElementById("userbox").innerHTML=ret;
    else if(ret && ret.indexOf("不存在")>=0) alert(ret);
    else alert("系统正忙，请稍候再试！");
  } 
}
  
function show_resetpsw()
{ AsyncPost("",WebRoot+"user/disuser.php?action=resetpsw","userbox"); 
  return false;
}      
      
function show_accountinfo()
{ if(OnlineUserID)
  { AsyncPost("",WebRoot+"user/disuser.php?action=accountinfo","userbox"); 
    UpdateUserInfo();//更新用户信息
  }else show_logout();
  return false;
}
 
function show_accountlog(page)
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/disuser.php?action=accountlog&page="+page,"userbox");
  else show_logout();
  return false;
}
 
function show_mycart()
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/mycart.php?action=getlist","userbox"); 
  else show_logout();
  return false
}
 
function show_myfav()
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/favorites.php?action=getlist","userbox");
  else show_logout();
  return false
}
function show_changepass()
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/disuser.php?action=changepass","userbox");
  else show_logout();
  return false
} 

function show_myorders(page)
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/disuser.php?action=myorders&page="+page,"userbox");
  else show_logout();
  return false;
} 

function show_onlinepay()
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/disuser.php?action=onlinepay","userbox");
  else show_logout();
  return false
} 

function show_receiveaddr()
{ if(OnlineUserID) AsyncPost("",WebRoot+"user/disuser.php?action=receiveaddr","userbox");
  else show_logout();
  return false
} 

function show_msg(page)
{ if(OnlineUserID)
  { cur_usr_page=page;
    AsyncPost("",WebRoot+"user/usrmsg.php?page="+page+"&handle="+Math.random(),"userbox");
  }else show_logout();
  return false
}
function msg_delete(msgid)
{ if(confirm("确定要删除该消息？"))
  { var ret=SyncPost("id="+msgid,WebRoot+"user/usrmsg.php?action=delete");
    if(ret=="OK") alert("消息删除成功！");else alert("有错误发生！"+ret);
    show_msg(cur_usr_page);
  }
}
function CheckSubmitMsg(myform)
{ var msg=myform.content.value;
  if(msg.trim())
  { if(confirm("确定要提交该留言？"))
    { var ret=SyncPost("content="+encodeURIComponent(msg),WebRoot+"user/usrmsg.php?action=new");
      if(ret=="OK") alert("留言发表成功，请耐心等待管理员回复！");else alert("有错误发生！");
      show_msg(1);
    }
  }else alert("留言内容为空！");
  return false;
}
 
function save_receiver_address(myform)
{ var formcontent,ret,tmpvalue;
  if(!CheckFormText("收货人姓名",myform.receipt,false))return false;
  if(!CheckFormText("详细地址",myform.address,false))return false;
  if(!CheckFormText("联系电话",myform.usertel,false))return false;
  else myform.usertel.value=DBC2SBC(myform.usertel.value);
	  
  formcontent="receipt="+encodeURIComponent(myform.receipt.value)+"&address="+encodeURIComponent(myform.address.value)+"&usertel="+encodeURIComponent(myform.usertel.value);
  ret=SyncPost(formcontent,WebRoot+"user/saveuserinfo.php?action=receiveaddr");
  if(ret.indexOf("ok")>=0)  alert("您的收货信息保存成功！");
  else alert("保存失败！"); 
}
 
function show_ConfirmPay(mode,money)
{ var formcontent="pay_amount="+money+"&paymethod="+mode; 
  // document.getElementById("manage_item").innerHTML=" &gt;&gt; 确认支付";
  AsyncPost(formcontent,WebRoot+"user/disuser.php?action=confirmpay","userbox");
}

function check_changepsw(objform)
{ var userpassword=objform.userpassword.value.trim();
  if(userpassword=="") 
  { objform.userpassword.focus();
    alert("对不起，请填写您的原密码！");
    return false;
  }
  var userpassword1=objform.userpassword1.value.trim();
  if(userpassword1=="") 
  { objform.userpassword1.focus();
    alert("对不起，您还没有填新密码！");
    return false;
  }
  var userpassword2=objform.userpassword2.value.trim();
  if(userpassword2=="")
  { objform.userpassword2.focus();
    alert("对不起，您还没有填确认密码！");
    return false;
  }
   if(userpassword1 != userpassword2)
   { alert("两次输入的密码不同，请重新输入！");
     return false;
   }
	 	
   var formcontent="userpassword="+encodeURIComponent(userpassword)+"&newpassword="+encodeURIComponent(userpassword1);
   var ret=SyncPost(formcontent,WebRoot+"user/saveuserinfo.php?action=changepass");
   if(ret && ret.trim()) 
   { alert(ret);
     if(ret.indexOf("成功")>=0)self.location.reload();
   }
   else 
   { alert("系统忙，请稍候再试！");
   }
}

function check_changequestion(objform)
{ var userpassword=objform.userpassword.value.trim();
  if(userpassword=="") 
  { objform.userpassword.focus();
    alert("对不起，请填写原登录密码！");
    return false;
  }
  var userquestion=objform.userquestion.value.trim();
  if(userquestion=="") 
  { objform.userquestion.focus();
    alert("对不起，密码提问不能为空！");
    return false;
  }
  var useranswer=objform.useranswer.value.trim();
  if(useranswer=="")
  { objform.useranswer.focus();
    alert("对不起，问题答案不能为空！");
    return false;
  }
  var formcontent="userpassword="+encodeURIComponent(userpassword)+"&userquestion="+encodeURIComponent(userquestion)+"&useranswer="+encodeURIComponent(useranswer);
  var ret=SyncPost(formcontent,WebRoot+"user/saveuserinfo.php?action=changequestion");
  if(ret && ret.trim()) 
  { alert(ret);
    if(ret.indexOf("成功")>=0)self.location.reload();
  }
  else 
  { alert("系统忙，请稍候再试！");
  }
}
             	
function SaveProductInCart(myform)
{ var selcount= Checkbox_SelectedCount("selectid[]");
  if(selcount==0)
  { alert("▲ 操作失败！\r\n请在需要修改的商品前打勾！");
  }
  else if(confirm('确定要保存购物车中的商品信息？'))
 { myform.action=WebRoot+"user/mycart.php?action=save";
   myform.submit();
 }
}
function DeleteProductInCart(myform)
{ var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0)
  { alert("没有选择操作对象！");
  }
  else if(confirm("确定要将选定的商品从购物车中删除吗？"))
  { myform.action=WebRoot+"user/mycart.php?action=del";
    myform.submit();
  }
}   
 
function SelToFav(myform)
{ var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0)
  { alert("没有选择操作对象！");
  }
  else if(confirm("确定要将选定的商品加入收藏架？"))
  { myform.action=WebRoot+"user/mycart.php?action=seltofav";
    myform.submit();
  }
}   
 
function DeleteFromFav(myform)
{  var selcount=Checkbox_SelectedCount("selectid[]");
   if(selcount==0)
   { alert("没有选择操作对象！");
   }
   else if(confirm("确定要从收藏架中删除选定的商品吗？"))
   { myform.action=WebRoot+"user/favorites.php?action=del";
     myform.submit();
   }
}   
 
function SelToCart(myform)
{  var selcount=Checkbox_SelectedCount("selectid[]");
   if(selcount==0)
   { alert("没有选择操作对象！");
   }
   else if(confirm("确定要将选定的商品加入购物车？"))
   { myform.action=WebRoot+"user/favorites.php?action=seltocart";
     myform.submit();
   }
}   
 
function ChangeSupport()
{ var ret=window.showModalDialog(WebRoot+"user/changesupport.php?handle="+Math.random(),"","dialogWidth:350px;dialogHeight:120px;status:no;scroll:no");
  if(ret=="ok")self.location.reload();
}

function process_request(){
  if(OnlineUserID){
    var ref=htmRequest("ref");
    if(ref){
      self.location.href=decodeURIComponent(ref);
      return;
    }
  }
  var do_action=htmRequest("action");
  if(!do_action)do_action=GetLinkLabel();
  switch(do_action){
    case   "payonline":    show_onlinepay();break;
    case   "myorders":     show_myorders();break;
    case   "resetpsw":     show_resetpsw();break; 
    case   "customerinfo": show_customerinfo();break;
    case   "receiveaddr":  show_receiveaddr();break; 
    case   "accountlog":   show_accountlog(1);break; 
    case   "changepass":   show_changepass();break; 
    case   "mycart":       show_mycart();break; 
    case   "myfav":        show_myfav();break;
    case   "msg":          show_msg(1);break;    
    default:               show_accountinfo();
  }
}
