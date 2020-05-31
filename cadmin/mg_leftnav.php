<?php require("includes/dbconn.php");
 if(!CheckLogin(0)){
   echo ' Not login! <a href="mg_leftnav.php">[Refresh]</a>';
   exit(0);
 }
 db_open();
 $res_menu=$conn->query("select * from `mg_popedom` where parent=0 order by sort",PDO::FETCH_ASSOC);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>管理中心-导航</title>
<STYLE type=text/css>
BODY {BACKGROUND: #799ae1; MARGIN: 0px; FONT: 9pt 宋体}
TABLE {BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px}
TD {FONT: 12px 宋体}
IMG {	BORDER-RIGHT: 0px; BORDER-TOP: 0px; VERTICAL-ALIGN: bottom; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px}
A {	FONT: 12px 宋体; COLOR: #215dc6; TEXT-DECORATION: none}
A:hover {	COLOR: #428eff}
.menubar{height:20px;padding-left:38px;BACKGROUND-POSITION: 26px 5px; BACKGROUND-IMAGE:url(images/arrow2.gif); BACKGROUND-REPEAT: no-repeat}
.sec_menu {	BORDER-RIGHT: white 1px solid; BACKGROUND: #d6dff7; OVERFLOW: hidden; BORDER-LEFT: white 1px solid; BORDER-BOTTOM: white 1px solid}
.menu_title_normal SPAN {FONT-WEIGHT: bold; LEFT: 8px; padding-left:38px; COLOR: #215dc6; POSITION: relative; TOP: 2px; CURSOR: pointer;}
.menu_title_active SPAN {FONT-WEIGHT: bold; LEFT: 8px; padding-left:38px; COLOR: #428eff; POSITION: relative; TOP: 2px}
</STYLE>
<SCRIPT language=javascript>
var whichOpen="",whichContinue='';
  
function menuShow(obj,maxh,obj2)
{ var ietype=document.all; //判断IE内核类型
	var blockHeiht=(ietype)?obj.style.pixelHeight:parseInt(obj.style.height.replace('px',''),10);
	if(blockHeiht<maxh)
  { blockHeiht+=maxh/20;
  	if(ietype)obj.style.pixelHeight=blockHeiht;
  	else obj.style.height =blockHeiht+'px';
	  if(obj.filters)obj.filters.alpha.opacity+=5;
	  obj2.background="images/title_bg_hide.gif";
    if(blockHeiht==maxh/10) obj.style.display='block';
	  myObj=obj;
	  myMaxh=maxh;
	  myObj2=obj2;
	  setTimeout('menuShow(myObj,myMaxh,myObj2)','5');
  }
}
function menuHide(obj,maxh,obj2)
{ var ietype=document.all; //判断IE内核类型
	var blockHeiht=(ietype)?obj.style.pixelHeight:parseInt(obj.style.height.replace('px',''),10);
	if(blockHeiht>0)
  { if(blockHeiht==maxh/20) obj.style.display='none';
  	blockHeiht-=maxh/20;
  	if(ietype)obj.style.pixelHeight=blockHeiht;
  	else obj.style.height =blockHeiht+'px';
	  if(obj.filters)obj.filters.alpha.opacity-=5;
	  obj2.background="images/title_bg_show.gif";
	  myObj=obj;
	  myMaxh=maxh
	  myObj2=obj2;
	  setTimeout('menuHide(myObj,myMaxh,myObj2)','5');
  }
  else if(whichContinue)
  { if(ietype) whichContinue.click();
		else
		{	var evt = document.createEvent("HTMLEvents");
      evt.initEvent("click", true, true);
      whichContinue.dispatchEvent(evt);
    }
  }
}
function menuChange(obj,maxh,obj2)
{	var ietype=document.all; //判断IE内核类型
	var blockHeiht=(ietype)?obj.style.pixelHeight:parseInt(obj.style.height.replace('px',''),10);
	if(blockHeiht)
  { menuHide(obj,maxh,obj2);
	  whichOpen='';
	  whichcontinue='';

  }
  else if(whichOpen)
	{ whichContinue=obj2;
		if(ietype) whichOpen.click();
		else
		{	var evt = document.createEvent("HTMLEvents");
      evt.initEvent("click", true, true);
      whichOpen.dispatchEvent(evt);
    }
	}
	else
	{ menuShow(obj,maxh,obj2);
	  whichOpen=obj2;
	  whichContinue='';
	}
}

function GetHomePage()
{ var LocalURL=self.location.href;
  var LastSign = LocalURL.lastIndexOf('/');
	LocalURL=LocalURL.substring(0, LastSign)
	LastSign = LocalURL.lastIndexOf('/');
	document.all("ClientPageURL").href=LocalURL.substring(0, LastSign+1);
} 

top.window.onunload=function(){
  var pre_url = this.frames[1].location;
  pre_url=pre_url.pathname+pre_url.search;
  document.cookie = "meray[home]="+ encodeURIComponent(pre_url);
}
</SCRIPT>
<base target="mainFrame">
</head>
<body>
<TABLE cellSpacing="0" cellPadding="0" width="100%" align="left" border="0"> 
<TR>
   <TD valign="top">
   	
      <TABLE cellSpacing="0" cellPadding="0" width="158" align="center" style="margin-bottom:2px">
      <TR>
         <TD vAlign=bottom height=26>
           <a href="admincenter.php"><IMG src="images/admin_title.gif" width="158" height="26" border="0"></a>
         </TD>
      </TR>
      <TR>
         <TD class="menu_title_normal"  background="images/title_bg_quit.gif"  height=25 align="center">
         	 <a href="/" target="_blank" ><B>前台首页</B></A> | <A href="admlogout.php" target="_TOP"><B>退出</B>&nbsp;&nbsp;&nbsp;</A>
         </TD>
      </TR>
      </TABLE>
<!---------------------------------------------------------------------------------------->      
<?php 
  foreach($res_menu as $row_menu){
    if(CheckPopedom($row_menu["id"])){
      $menu_items="";
      $menu_count=0;
      $res_sub=$conn->query("select * from `mg_popedom` where parent={$row_menu['id']} order by sort",PDO::FETCH_ASSOC);
      foreach($res_sub as $row_sub){
        if(CheckPopedom($row_sub["id"])){
          $menu_count=$menu_count+1;
       	  $menu_items=$menu_items."<TR><TD class='menubar'><a href='{$row_sub['path']}'>{$row_sub['title']}</a></TD></TR>";
       	}
      }
      if($menu_count>0){
        $menu_height=($menu_count+1)*20;?>
        <TABLE cellSpacing="0" cellPadding="0" width="158" align="center">
        <TR>
           <TD id="menuTitle<?php echo $row_menu['id'];?>" onclick="<?php echo "menuChange(menu{$row_menu['id']},$menu_height,menuTitle{$row_menu['id']})";?>" class="menu_title_normal"  background="images/title_bg_hide.gif"
             height="25" onmouseover="this.children[0].style.color='#ee8eff';"  onmouseout="this.children[0].style.color='#215dc6';">  
             <SPAN><?php echo $row_menu["title"];?></SPAN></TD>
        </TR>
        <TR>
           <TD>
              <DIV class="sec_menu" id="menu<?php echo $row_menu['id'];?>"  style="DISPLAY: none; FILTER: alpha(Opacity=0); WIDTH: 158px; HEIGHT: 0px">
                <TABLE style="POSITION: relative; top: 10px;" cellSpacing=0  cellPadding=0 width=135 align=center>
                  <?php echo $menu_items;?>
                </TABLE>
              </DIV>
           </TD>
        </TR>
        </TABLE><?php
      }
    }  
  }
  db_close();?>
 <!----------------------------------------------------------------------------------------->    
      <TABLE cellSpacing=0 cellPadding=0 width=158 align=center>
      <TR>
         <TD id="menuTitle_0" onclick="menuChange(menu_0,40,menuTitle_0);"  class="menu_title_normal"  background="images/title_bg_hide.gif"  
             height="25"  onmouseover="this.children[0].style.color='#ee8eff';"  onmouseout="this.children[0].style.color='#215dc6';">
             <SPAN>登录信息</SPAN> </TD>
       </TR>
       <TR>
         <TD>
           <DIV class="sec_menu" id="menu_0"  style="FILTER: alpha(Opacity=100); WIDTH: 158px; HEIGHT:30px">
           	 
           	  <TABLE style="POSITION: relative; TOP: 4px;" cellSpacing=0 cellPadding=0 width=135 align=center>
              <TR>
                <TD align="center" height="20" style="line-height:120%"><font color="#FF0000"><?php echo $AdminUsername;?></font><font color="#6f6f6f"></font>@涵若铭妆</TD>
              </TR>
              </TABLE>
           </DIV>
         </TD>
       </TR>
       </TABLE>
 <!----------------------------------------------------------------------------------------->    
</body>  
</html>
