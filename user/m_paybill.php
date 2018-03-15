<?php
function ProductURL($pid){
  if(WEB_SITE>1) return WEB_ROOT.'product.htm?id='.$pid; else return '/products/'.$pid.'.htm';
}
$product_count=$conn->query('select count(*) from `mg_favorites` where userid='.$LoginUserID.' and state>1 and amount>0')->fetchColumn(0);
if(empty($product_count)) PageReturn('对不起，您的购物车中还没有商品！',WEB_ROOT.'usrmgr.htm?action=mycart'); 

$rs_user=$conn->query('select * from `mg_users` where id='.$LoginUserID,PDO::FETCH_ASSOC)->fetch();
if(empty($rs_user))PageReturn('参数错误',-1) ;?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckOrderInfo(myform)
{ var i,count,chkbox,tmpvalue;
  tmpvalue=myform.receipt.value.trim();
  if(tmpvalue=="")
  { myform.receipt.focus();
    alert("对不起，请填写收货人姓名！");
    return false;
  }
  else
  { tmpvalue=CheckBanChar(tmpvalue,"<>'\"");
    if(tmpvalue) 
    { alert("收货人姓名包含非法字符 "+tmpvalue);
      return false;
    }
  }
  tmpvalue=myform.address.value.trim();
  if(tmpvalue=="")
  { myform.address.focus();
    alert("对不起，请填写收货人详细收货地址！");
    return false;
  }
  else
  { tmpvalue=CheckBanChar(tmpvalue,"<>'\"");
    if(tmpvalue) 
    { alert("收货人地址包含非法字符 "+tmpvalue);
      return false;
    }
  }  	
  tmpvalue=myform.usertel.value.trim();
  if(tmpvalue=="")
  { myform.usertel.focus();
    alert("对不起，请留下收货人联系电话！");
    return false;
  }
  else
  { tmpvalue=CheckBanChar(tmpvalue,"<>'\"");
    if(tmpvalue) 
    { alert("联系电话包含非法字符 "+tmpvalue);
      return false;
    }
  }  	  
  if(myform.usermail.value.trim()=="")
  { alert("Email不能为空！");
    myform.usermail.focus();
    return false;
　}
  else
  { var re = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$");
    if(!re.test(myform.usermail.value.trim()))
    { alert("无效的Email格式！");
      myform.usermail.focus();
      return false;
    }
  }
  /*if(myform.paymethod.value=="")
  { alert("对不起，您还没有选择支付方式！");
    return false;
  }*/
  if(myform.deliveryid.value=="0")
  { alert("对不起，您还没有选择配送方式！");
    return false;
  }
  if(!myform.readprotol.checked)
  { alert("您还没有鉴阅异地发货协议！");
    return false;
  }
  myform.confirmbtn.disabled=true;   
}
//-->
</SCRIPT>

<table width="100%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
  <td>
  	<table border="0" cellpadding="3" cellspacing="1" align="center" width="100%"  bgcolor="#CCCCCC">
    <tr bgcolor="#f7f7f7" height="25" align="center">
      <td width="75">编 号</td>
      <td width="600">商 品 名 称</td>
      <td width="75">数 量</td>
      <td width="75">单 价</td>
      <td width="75">合 计</td>
      <td width="100">备 注</td>
    </tr><?php
$TotalPrice = 0;
$TotalScore = 0;

$sql='select mg_product.id,mg_product.name,mg_product.score,mg_product.price0,mg_product.price'.$LoginUserGrade.' as myprice,mg_product.stock0,mg_product.onsale,mg_favorites.remark,mg_favorites.amount from (mg_product inner join mg_favorites on mg_product.id=mg_favorites.productid) where mg_favorites.userid='.$LoginUserID.' and mg_favorites.state>1 and mg_favorites.amount>0 order by mg_product.id';
$res=$conn->query($sql,PDO::FETCH_ASSOC);
foreach($res as $row){   
  $Amount=$row['amount'];
  $ProScore = $row['score'];
  $myprice =  $row['myprice'];
  if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
                
  $GoodsPaid=round($myprice*$Amount,2);
  $TotalPrice = $TotalPrice + $GoodsPaid;
  $TotalScore = $TotalScore + $ProScore*$Amount;
  $WareRemarks=$row['remark'];
      
  $ProductTitle='<a href="'.ProductURL($row['id']).'" target="_blank"';

  #if $Amount>$row['stock0']{ 
  if(1>$row['stock0'])$ProductTitle.=' style="text-decoration:line-through" title="库存不足，目前该商品库存'.$row['stock0'].'件，请联系客服核实">'.$row['name'].' <img src="images/lack.gif" border=0 width=16 height=16></a>';
  else $ProductTitle.='>'.$row['name'].'</a>';?>

  <tr bgcolor="#FFFFFF" height="25" align="center">
    <td><?php echo substr('0000'.$row['id'],-5);?></td>
    <td align="left">&nbsp;<?php echo $ProductTitle;?></td>
    <td><?php echo $Amount;?></td>
    <td><?php echo round($myprice,2);?></td>
    <td><?php echo round($GoodsPaid,2);?></td>
    <td><?php if($WareRemarks)echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$WareRemarks.'</MARQUEE>'; else echo '无';?></td>
    </tr><?php
  }
  
?> 
  <tr align="right" bgcolor="#FFFFFF">
     <td height="30" colspan="8">商品总计：<font color="#FF6600"><?php echo round($TotalPrice,2);?></font>&nbsp;元&nbsp; 获得积分：<font color="#FF6600"><?php echo $TotalScore;?></font>&nbsp;分 </td>
  </tr>
  </table></td></tr >
  <tr><td height="5"></td></tr>
  <tr>
     <td><form method="post" action="confirmbill.php" onsubmit="return CheckOrderInfo(this)" style="margin:0px">
        <table width="100%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
        <tr align="center" bgcolor="#FDDFEF">
          <td height="30" colspan="2" bgcolor="#f7f7f7"><strong>第一步 写订单基本信息</strong></td></tr>
        <tr>
          <td width="17%" align="right" bgcolor="#f7f7f7">收 货 人：</td>
          <td width="83%" bgcolor="#FFFFFF"><input name="receipt" type="text" class="input_sr" id="receipt" maxlength="16" value="<?php echo $rs_user['receipt'];?>" size="40">
            * 填写收货人真实姓名</td></tr>
        <tr>
          <td align="right" bgcolor="#f7f7f7">收货地址：</td>
          <td bgcolor="#FFFFFF"><input name="address" type="text" class="input_sr" maxlength="64" value="<?php echo $rs_user['address'];?>" size="40">
            * 明细：省市-区-街道-小区-门牌号数-楼层-房间</td></tr>
        <tr>
          <td align="right" bgcolor="#f7f7f7">联系电话：</td>
          <td bgcolor="#FFFFFF"><input name="usertel" type="text" class="input_sr" maxlength="50" value="<?php echo $rs_user['usertel'];?>" size="40" maxlength="50">
            * 收货人的联系电话，可填多个号码(空格隔开)</td></tr>
        <tr>
          <td  align="right" bgcolor="#f7f7f7">电子邮箱：</td>
          <td bgcolor="#FFFFFF"><input name="usermail" type="text" class="input_sr" maxlength="50" value="<?php echo $rs_user['usermail'];?>" size="40">
            * 很重要！订单、发货等信息都会发送到此信箱
          </td></tr>
        <tr> 
          <td align="right" bgcolor="#f7f7f7">支付方式：</td>
          <td bgcolor="#FFFFFF"><select name="paymethod" style="width:51%"><option value="" selected>...</option><?php
          $res=$conn->query('select * from `mg_delivery` where method=1 order by sortorder',PDO::FETCH_ASSOC);
          foreach($res as $row)echo '<option value="'.$row['subject'].'">'.$row['subject'].'</option>';?></select>   货款支付方式，作为财务查帐参考依据
          </td></tr>        
        <tr>
          <td align="right" bgcolor="#f7f7f7">配送方式：</td>
          <td bgcolor="#FFFFFF"><select name="deliveryid" style="width:51%">
            <option value="0" selected>...</option><?php  
              $res=$conn->query('select * from `mg_delivery` where method=0 order by sortorder',PDO::FETCH_ASSOC);
              foreach($res as $row)echo '<option value="'.$row['id'].'">'.$row['subject'].'</option>';?></select> * 运费按实际收取
          </td>        
      </tr> 
      <tr>
          <td align="right" bgcolor="#f7f7f7">订单附言：<br>(256个字以内) </td>
          <td bgcolor="#FFFFFF"><textarea name="userremark" cols="76" rows="5" id="userremark" maxlength="255" style="width:100%"></textarea></td>
        </tr>
        <tr align="center">
          <td colspan="2" bgcolor="#FFFFFF"><font color="#FF0000">关于代发货说明:  代发货者请在附言中注明。</font></td>
        </tr>
       <tr align="center" bgcolor="#F7F7F7">
          <td height="30" colspan="2"><strong>第二步 阅读异地发货协议</strong></td>
          </tr>
        <tr>
          <td align="right" valign="top" bgcolor="#FFFFFF"><img src="images/pspic2.gif" width="106" height="173"></td>
          <td bgcolor="#FFFFFF" style="padding:10px;line-height: 150%;">
              (1) 请收件人在快递员或物流处领取包裹时，一定要注意<font color=#FF0000>在当场验收包裹内的物品数量、配件是否齐全；商品外表面是否有明显的因摔、撞、挤、压引起的损伤。请在确认无误后再签字签收</font>，否则若在签收之后再提出异议，本公司概不负责。如在验收当场发现商品存在以上问题，请直接电话联系本公司或快递公司开出此类证明。若随意签收给您带来损失，本公司一律不负责！<br> 
            	(2) 我们不负责快件的查询和催送问题。请您自己根据货单号码，通过该<a href="deliverytrack.php" target="_blank"><font color=#FF0000>快递公司的官方网站</font></a>查询快件的行踪，或者根据快递公司网站上的客服电话进行查询。若派送延误，请尽量自行与快递公司协调解决，谢谢配合。
            	<br>
<br><img src="images/bgline.gif" width="600" height="5"><br><input type="checkbox" name="readprotol">我已经阅读并且同意发货协议 <font color=#FF0000>＊</font>
	        </td>
        </tr>
       <?php if(WEB_SITE==-1){?> 
       <tr align="center" bgcolor="#F7F7F7">
          <td height="30" colspan="2"><strong><font color=#FF0000>第三步</font>&nbsp; 选择订单处理客服</strong></td>
          </tr>
        <tr>
          <td align="right" valign="top" bgcolor="#FFFFFF"><img src="images/pspic3.gif" width="106" height="173"></td>
          <td bgcolor="#FFFFFF"  style="padding:10px;line-height: 150%;">
            	为明确职责分工、落实责任到位制，以保证我们工作的高效性，请在递交订单时选择负责处理您订单的客服人员。递交订单后，请及时与我们的客服人员联系，确认您的订单中没有缺货的产品，以及其他要注意事项。
            	<br>
<br><img src="images/bgline.gif" width="600" height="5"><br>
              客服专员： <select name="support">
             	<option value="">...</option><?php
               $DefaultSupport=$rs_user['support'];
               $res=$conn->query('select idnumber,idnumber2 from `mg_admins` where ordercoordinator and depot='.MAIN_DEPOT.' order by idnumber',PDO::FETCH_NUM);
               foreach($res as $row){
                 for($id=$row[0];$id<=$row[1];$id++){
                   if($id==$DefaultSupport) echo '<option selected value="'.$id.'">'.$id.'</option>';
                   else echo '<option value="'.$id.'">'.$id.'</option>';
                 }
               }?><option value="0">随机</option>
              </select> &nbsp;
            
	        </td>
        </tr><?php
       }?>
    <tr>
      <td colspan="2" align="right" bgcolor="#FFFFFF"><input name="confirmbtn" type="submit" class="input_bot"  value="提交订单" ></td>
    </tr>
    </table></form>
</tr>
</table>
