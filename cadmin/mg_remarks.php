<?php require('includes/dbconn.php');
 CheckLogin();
 OpenDB();
 
 $action=@$_GET['action']; 
 if($action=='save'){
   $remarkid=$_POST['remarkid'];
   $audit=$_POST['audit'];
   if(is_numeric($remarkid) && $remarkid>0 && ($audit=='0' || $audit=='1')){
     $reply=FilterText(trim($_POST['reply']));
     $conn->exec("update mg_review set reply='$reply',audit=$audit where id=$remarkid");
     echo '<script>parent.window.closeDialog(true);</script>';        
   }
   CloseDB();
   exit(0);
 }
 else if($action=='del'){
  $selectid=$_POST['selectid'];
  if(empty($selectid)) PageReturn("没有选择操作对象！",-1);
  else{
    $idlist=implode(',',$selectid);
    $conn->exec('update mg_review set productid=0 where id in ('.$idlist.') and audit=0'); 
    PageReturn("所选用户评论删除成功！");
  }
 }?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>评论管理</title>
</head>
<body leftmargin="0" topmargin="0">
<?php
if($action=='audit'){ 
  $remarkid=$_GET['remarkid'];
  if(is_numeric($remarkid)&&$remarkid>0){
    $row=$conn->query('select mg_review.*,mg_product.name from mg_review inner join mg_product on mg_product.id=mg_review.productid where  mg_review.id='.$remarkid,PDO::FETCH_ASSOC)->fetch();?>
<form method="post" action="?action=save">
    	<table width="100%" height="100%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr height="25">
          <td width="10%" nowrap height="25" align="right" background="images/topbg.gif" bgcolor="#f7f7f7" >评论商品：<input type=hidden name="remarkid" value="<?php echo $remarkid;?>"/></td>
          <td width="90%" height="25" bgcolor="#FFFFFF"><?php echo $row['name'];?></a></td>
        </tr>
        <tr height="25">
          <td height="25" nowrap align="right" valign="middle" background="images/topbg.gif" bgcolor="#f7f7f7" >来访客户：</td>
          <td height="25" bgcolor="#FFFFFF" ><a href="mg_usrinfo.php?user=<?php echo $row['username'];?>"><?php echo $row['username'];?></a> &nbsp; &nbsp; [From IP：<?php echo $row['ip'];?>]</td>
        </tr>
        <tr height="30">
          <td height="25" nowrap align="right" valign="top" background="images/topbg.gif" bgcolor="#f7f7f7" >评论内容：</td>
          <td height="25" bgcolor="#FFFFFF">
          	<textarea style="width:100%" rows="9" disabled><?php echo $row['remark'];?></textarea></td>
        </tr>
        <tr height="25">
          <td height="25" nowrap align="right" background="images/topbg.gif" bgcolor="#f7f7f7" >评论时间：</td>
          <td height="25" bgcolor="#FFFFFF" ><?php echo date('Y-m-d H:i:s',$row['actiontime']);?></td>
        </tr>
        <tr height="30">
          <td height="25" align="right" valign="middle" background="images/topbg.gif" bgcolor="#f7f7f7" >审核状态：</td>
          <td height="25" bgcolor="#FFFFFF" >
            <select name="audit" id="audit">
              <option value="0" <?php if($row['audit']==0) echo 'selected';?>/>未审核</option>
              <option value="1" <?php if($row['audit']==1) echo 'selected';?>/>已审核</option>
            </select>
          </td>
        </tr>
        <tr>
          <td align="right" valign="top" background="images/topbg.gif" bgcolor="#f7f7f7" >回复内容：<br>(256字以内) </td>
          <td bgcolor="#FFFFFF"  style="border-bottom: 1px solid #FF6600" ><textarea name="reply" cols="80" rows="9" style="width:100%"><?php echo $row['reply'];?></textarea></td>
        </tr>
        <tr>
          <td colspan="2"  align="center" bgcolor="#FFFFFF"><input name="action" type="hidden" value="save" />
             <input type="submit" value=" 递 交 "></td>
        </tr>
    </table></form></body></html><?php
  }
  CloseDB();
  exit(0);
}?>

<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">  	
      <tr>
      	<td>
    	    <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>商品评论管理</font></b>
    	  </td>
        <td>
     	  </td>  
    	</tr>
      </table>  
    </td>
  </tr>

  <tr bgcolor="#FFFFFF"> 
    <td align="center"><form  method="post"><?php 
    $sql='';
    $res=page_query('select mg_product.name,mg_review.*','from mg_review inner join mg_product on mg_product.id=mg_review.productid','','order by mg_review.audit asc,mg_review.actiontime desc',10);
    if(!$res) echo '<p align="center" class="contents"> 目前还没有任何评论！</p>';
    else{?>
      <table width="96%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr bgcolor="#F7F7F7">
        <td width="5%" height="25" align="center" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)" ></td>
        <td width="25%" height="25" align="center" background="images/topbg.gif"><strong>评论商品名称</strong></td>
        <td width="30%" height="25" align="center" background="images/topbg.gif"><strong>评论正文</strong></td>
        <td width="12%" height="25" align="center" background="images/topbg.gif"><strong>用 户</strong></td>
        <td width="18%" height="25" align="center" background="images/topbg.gif"><strong>评论时间</strong></td>
        <td width="10%" height="25" align="center" background="images/topbg.gif"><strong>状态</strong></td>
      </tr><?php
      foreach($res as $row){
        echo '<tr bgcolor="#FFFFFF" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> <td height="25" align="center"><input name="selectid[]" type="checkbox" value="'.$row['id'].'" onclick="mChk(this)"></td>';
        echo '<td height="25" <a href="/product.php?id='.$row['productid'].'" target="_blank" title="'.$row['name'].'">'.$row['name'].'</a></td>';
        echo '<td height="25" valign="top"><textarea style="width:100%;background-color:transparent;overflow:auto;word-break:break-all;font-size:11pt" readOnly>'.$row['remark'].'</textarea>';
        if($row['reply']) echo '<br><img border=0 src="images/dot.gif" WIDTH=10 HEIGHT=10>&nbsp;<font color="#FF8000"><u><b>回复</b></u>：</font>'.$row['reply'];
        echo '</td>';
        echo '<td height="25" align="center"><a href="mg_usrinfo.php?user='.$row['username'].'">'.$row['username'].'</a></td>';
        echo '<td height="25" align="center">'.date('Y-m-d H:i:s',$row['actiontime']).'</td>';
        echo '<td height="25" align="center" onclick="AuditRemark('.$row['id'].')" style="cursor:pointer;TEXT-DECORATION:underline" title="点击查看详情">';
        if($row['audit']=='1') echo '<font color=GREEN>已审核</font>';
        else echo '<font color=RED>未审核</font>';
        echo '</td></tr>';
      }
      echo '<tr bgcolor="#FFFFFF"><td height="30" colspan="6" align="center"><input type="button" class="input_bot" onClick="BatchDelete(this.form)" value=" 删 除 ">&nbsp; &nbsp; <script language="javascript">GeneratePageGuider("",'.$total_records.','.$page.','.$total_pages.');</script></td></tr></table>';
    }?>
    </form>
    </td>
  </tr>
</table>
<script>
 function AuditRemark(remarkid){
   var OnCloseDlg=function(ret){
       if(ret){
         alert('保存成功！');
         self.location.reload();
       }
       return true;
   }
   AsyncDialog("商品评论","?action=audit&remarkid="+remarkid,800,550,OnCloseDlg);
 }
 function BatchDelete(myform){
   var selcount=Checkbox_SelectedCount("selectid[]");
   if(selcount==0)alert("没有选择操作对象！");
   else{
     if(confirm("确定删除选中的"+selcount+"条评?")){
       myform.action="?action=del";
       myform.submit();
     }
   }	
 }	
</script>
</body>
</html><?php
CloseDB();?>
