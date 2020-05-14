<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();

$EndDate=trim(@$_GET['enddate']);
if($EndDate && ($EndDate=strtotime($EndDate))){
  $BeginDate=trim(@$_GET['begindate']);
  if(!$BeginDate || !($BeginDate=strtotime($BeginDate)) || $BeginDate>=$EndDate) $BeginDate=$EndDate-30*24*60*60;
}
else{
  $EndDate=time();
  $BeginDate=time()-30*24*60*60;
}

$str_begindate=date('Y-m-d',$BeginDate);
$str_enddate=date('Y-m-d',$EndDate);

$LastRetrench=$conn->query('select min(actiontime) from mg_orders where state>3 and state<8')->fetchColumn(0);
$LastRetrench=date('Y-m-d',$LastRetrench);


$brandid=@$_GET['brand'];
if(!is_numeric($brandid))$brandid=0;

//遍历父类 	
function GenLinkGuider($PID){
  global $conn,$str_begindate,$str_enddate;
  $Links='';
  while($PID){
    $row=$conn->query('select id,title,parent from mg_category where id='.$PID,PDO::FETCH_ASSOC)->fetch();
    if($row){
      $Links = '&nbsp;&gt;&gt;&nbsp;<a href="?brand='.$row['id'].'&begindate='.$str_begindate.'&enddate='.$str_enddate.'"><font color="#FF6600">'.$row['title'].'</font></a>'.$Links;
      $PID = $row['parent'];
    }
    else PageReturn('参数错误！',0);
  }
  return '<a href="?begindate='.$str_begindate.'&enddate='.$str_enddate.'"><font color="#FF6600">所有产品</font></a>'.$Links;
}
 
//遍历子类 	
function GetSubBrands($selec){
  global $conn;
  $args = func_get_args();
  $list=($args && count($args)>1)?$args[1]:$selec;
  $res=$conn->query('select id from mg_category where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
  foreach($res as $row){
    $list=GetSubBrands($row[0],$list.','.$row[0]);
  }
  return $list;
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="editproduct.js" type="text/javascript"></SCRIPT>
<title>商品分类销售统计</title>
</head>
<body leftmargin="0" topmargin="0">
<form name="cartform" method="post" action="?brand=<?php echo $brandid;?>" style="margin:0px"> 
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td height=25 background="images/topbg.gif">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.asp">管理首页</a> -&gt; <font color=#FF0000>商品分类销售统计</font></b>
    &nbsp; （<b>时间统计范围</b>：&nbsp;<input name="begindate" type="text" value="<?php echo $str_begindate;?>" style="width:75px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 1px solid #CCCCCC;background-color:transparent;cursor:pointer;"> ～ <input name="enddate" type="text" value="<?php echo $str_enddate;?>" style="width:75px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 1px solid #CCCCCC;background-color:transparent;cursor:pointer;">）</td>
      <td nowrap align="right"><input type="button" value="重新统计" onclick="CheckSearch(this.form)">&nbsp;</td>
    </tr>
    </table>
  </td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
    <table width="98%" border="0" align="center" cellpadding="1" cellspacing="1" >
    <tr><td><b>分类统计对象:</b>&nbsp; <?php echo GenLinkGuider($brandid);?></td>
    </tr>
    </table>	

    <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr bgcolor="#F7F7F7" align="center"> 
      <td width="55%" background="images/topbg.gif"><strong>商品分类名称</strong></td>
      <td width="15%" background="images/topbg.gif" nowrap title="是指当前时间统计范围内售出的商品数量"><strong>本期售出(个)</strong></td>
      <td width="15%" background="images/topbg.gif" nowrap title="是指历史上累计售出的商品数量"><strong>累计售出(个)</strong></td>
      <td width="15%" background="images/topbg.gif" nowrap title="各子库存统计总和"><strong>库存(个)</strong></td>
    </tr><?php

$SubbrandCount=0;
$res=$conn->query('select id,sortindex,title from mg_category where parent='.$brandid.' order by sortindex',PDO::FETCH_ASSOC);
foreach($res as $row){
  $SubbrandCount++; 
  $CatList=GetSubBrands($row['id']);
  $TotalAmount=$conn->query('SELECT sum(b.amount) FROM ((mg_product as a inner join mg_ordergoods AS b on a.id=b.productid) inner join mg_orders AS c on b.ordername=c.ordername) WHERE c.state>3 and c.actiontime>'.$BeginDate.' and c.actiontime<'.$EndDate.' and a.brand in ('.$CatList.')')->fetchColumn(0);
  if($TotalAmount===null)$TotalAmount=0;
  echo '<tr align=center height=25 bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"><td align="left"><a href="?brand='.$row['id'].'&begindate='.$str_begindate.'&enddate='.$str_enddate.'">&nbsp; <img border=0 src="images/pic24.gif" align=absMiddle >'.$row['title'].'</a></td><td><font color="#FF0000">'.$TotalAmount.'</font></td><td>?</td><td>?</td></tr>';
}
$res=page_query('SELECT a.id, a.name,a.stock0,a.solded,sum(b.amount) AS salenum','FROM ((mg_product as a inner join mg_ordergoods AS b on a.id=b.productid) inner join mg_orders AS c on b.ordername=c.ordername)','WHERE c.state>3 and c.actiontime>'.$BeginDate.' and c.actiontime<'.$EndDate.' and a.brand='.$brandid.' GROUP BY a.id, a.name,a.stock0,a.solded','order by salenum desc,a.solded desc',20);
if($total_records>0){
  foreach($res as $row){
    echo '<tr height="25" align="center" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
           <td align="left">&nbsp; <img border=0 src="images/pic26.gif" align=absMiddle ><a href="mg_stocklog.php?id='.$row['id'].'" style="color:#FF6600">'.GenProductCode($row['id']).'</a>&nbsp; &nbsp; <a href="'.GenProductLink($row['id']).'" target="_blank">'.$row['name'].'</a></td>
           <td><font color=#FF0000>'.$row['salenum'].'</font></td>
	   <td>'.$row['solded'].'</td>
	   <td>'.$row['stock0'].'</td>
	  </tr>';
  }
   $total_records+=$SubbrandCount;
  echo '<tr><td align="center" colspan="4"><script language="javascript">GeneratePageGuider("'."brand=$brandid&begindate=$str_begindate&enddate=$str_enddate\",$total_records,$page,$total_pages);</script></TD></tr>";
}
else if($SubbrandCount==0){
  echo '<tr><td colspan=4 height=50 bgcolor="#FFFFFF" align="center"> 对不起，找不到该分类的统计信息！</td></tr>';
}?>
    </table>
  </td>
</tr>
</table></form>
<script language=javascript>
function StrToDate(strDate){
  var  sd=strDate.split("-"); 
  return new Date(sd[0],sd[1],sd[2]);   
}
function CheckSearch(myform){
  var begindate=myform.begindate.value;
  if( StrToDate(begindate) < StrToDate("<?php echo $LastRetrench;?>") ){
    alert("对不起，只能查询<?php echo $LastRetrench;?>后的统计明细信息！");
  }
  else self.location.href="?begindate="+begindate+"&enddate="+myform.enddate.value; 
}
</script>	     
</body>
</html><?php CloseDB();?>
