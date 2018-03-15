<?php require('includes/dbconn.php');
CheckLogin(); 
OpenDB();
$MaxProductID=$conn->query('select max(id) from mg_product')->fetchColumn(0);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
  <td height="20" colspan="3" width="100%" background="images/topbg.gif" bgcolor="#F7F7F7">
   <img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_htmupdate.php">前台页面更新管理</a> -&gt; <font color=#FF0000>商品SEO秀</font></b>
  </td>
</tr>
<script>
var OpInterval=1,SecondCounter,enable_running=0,ProductID=0,MaxProductID=<?php echo $MaxProductID;?>,Generating=false;

function UpdateProductShow(){
  if(enable_running){
    var statusLabel=document.getElementById("process_status");
    if(SecondCounter>0){
      if(Generating){
        var statustext=statusLabel.innerHTML;
        if(statustext.indexOf("完成")>=0){
          Generating=false;
    	  ProductID++; 
 	  document.forms[0]["productid"].value=ProductID; 
        }
      }
      if(!Generating){
        statusLabel.innerHTML="<font color=#FF0000>"+SecondCounter+"秒</font>";
        SecondCounter--;
      }
    }
    else if(ProductID<=MaxProductID){
      SecondCounter=OpInterval;
      Generating=true;
      AsyncPost("id="+ProductID,"mg_htmgen.php?mode=product","process_status");
    }
    setTimeout("UpdateProductShow()",1000); 
  }  
}
function ControlProcess(myform){
  if(enable_running){
    enable_running=false;
    document.getElementById("process_status").innerHTML="stoped";
    myform.ControlButton.value="开始";
  }
  else{
    ProductID=myform.productid.value.trim();
    ProductID=( !ProductID || isNaN(ProductID) )?0:parseInt(ProductID);
    if(ProductID<1) alert("无效的ID");
    else{
      SecondCounter=OpInterval;
      setTimeout("UpdateProductShow()",1000);
      enable_running=true;
      Generating=false;
      myform.ControlButton.value="停止";
    }
  }
}
</script>
<tr>
	<td align="center">

<table border=0 width="100%">
<tr>
	<td width="50%" align="right" id="process_status" style="padding-right:10px;">
	</td><form>
	<td width="50%" align="left">
	<input type="text" value="0" name="productid" style="width:60px;text-align:center" maxlength=4 onkeyup="if(isNaN(value))execCommand('undo')">&nbsp;<input type="button" name="ControlButton" id="ControlButton" value="开始" onclick="ControlProcess(this.form)" >
	</td></form>
</tr>
</table>

  </td>
</tr>
</table>
</body>
</html><?php CloseDB();?>
