var CurOrderState=0,CurOrderName,CurUserName,AllowModifyProduct=0,AllowAuditPrice=0,OwnPopedomFinance=0,IsOrderManager=0,NonAudit=0,CostOrScore=false,ChangingOrderState=false,Depot_Options=null,Order_Exporter=0,Order_Importer=0;
const CELL_GID=0,CELL_PID=1,CELL_NAME=2,CELL_AMOUNT=3,CELL_PRICE=4,CELL_SCORE=5,CELL_REMARK=6;

function ChangeAmount(tableCell,OrderGoodsID){
  var cells=tableCell.parentNode.cells;
  var pname=GetInnerText(cells[CELL_NAME]);
  var defValue=GetInnerText(tableCell);
  var getresult = function(newValue){
    if(newValue && newValue!=defValue && !isNaN(newValue)){
      newValue=parseInt(newValue);
      if(newValue>=0){
        var ret=SyncPost("selectid="+OrderGoodsID+"&newvalue="+newValue+"&ordername="+CurOrderName,"?mode=amount");
    	if(ret && ret.indexOf("<OK>")>=0){
  	  var stock=parseInt(tableCell.parentNode.getAttribute("stock"));
  	  tableCell.innerHTML="<font color=#FF0000>"+newValue+"</font>";
  	  //if(!CostOrScore)SetInnerText(cells[CELL_SCORE],"?");
  	  CheckProductAmount(cells[CELL_NAME],stock,newValue);
  	  OrderRestat();
  	}
  	else{
          alert("操作失败，请稍候再试！");
          self.location.reload();
  	}
      }else alert("数字无效！"); 
    } 
    return true;
  }
  AsyncPrompt("设定商品订购数量",pname,getresult,parseInt(defValue),8);  
}

function ChangeScore(tableCell,OrderGoodsID){
  var defValue=GetInnerText(tableCell);
  var pname=GetInnerText(tableCell.parentNode.cells[CELL_NAME]);
  var getresult = function(newValue){
    if(newValue && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("selectid="+OrderGoodsID+"&newvalue="+newValue+"&ordername="+CurOrderName,"?mode=score");
      if(ret){
        if(ret.indexOf("<OK>")>=0){
          tableCell.innerHTML="<font color=#FF0000>"+newValue+"</font>";
          OrderRestat();
        }else alert(ret);
      }
      else alert("操作失败，请稍候再试！");
    } 
    return true;
  }
  AsyncPrompt("设定商品积分",pname,getresult,parseInt(defValue),8);  
}

function ChangePrice(tableCell,OrderGoodsID){
  var defValue=GetInnerText(tableCell);
  var pname=GetInnerText(tableCell.parentNode.cells[CELL_NAME]);
  var getresult = function(newValue){
    if(newValue && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("selectid="+OrderGoodsID+"&newvalue="+newValue+"&ordername="+CurOrderName,"?mode=price");
      if(ret && ret.indexOf("<OK>")>=0){
         var objrow=tableCell.parentNode;
         var audit=objrow.getAttribute("audit");
         var OriginPrice=objrow.getAttribute("price");
         if(newValue==OriginPrice){
           tableCell.style.color="#00AAFF"; //修改成原价后显示成蓝色（页面刷新后会恢复成黑色）
	   tableCell.title="点击修改";
           if(audit=='0'){
             NonAudit--;
             objrow.setAttribute,("audit",'1');
           }
         }
         else{
           tableCell.style.color="#FF0000";
           if(AllowAuditPrice){
	     tableCell.title="原价"+OriginPrice+"元，点击审核";
             tableCell.onclick=new Function("AuditGoods(this,"+OrderGoodsID+");"); 
             if(audit=='1'){
               NonAudit++;
               objrow.setAttribute,("audit",'0');
             }
           }
           else{
	     tableCell.title="原价"+OriginPrice+"元，点击修改";
           }
         }  
         //if(!CostOrScore)SetInnerText(tableCell.parentNode.cells[CELL_SCORE],"?");
	 SetInnerText(tableCell,newValue);
         OrderRestat();
      }
      else if(ret) alert(ret);
      else{ 
        alert("操作失败，请稍候再试！");
   	self.location.reload();
      }
    } 
    return true;
  }
  AsyncPrompt("设定商品单价",pname,getresult,parseFloat(defValue),8);  
}

function AuditGoods(tableCell,OrderGoodsID)
{  var ret=SyncPost("selectid="+OrderGoodsID+"&ordername="+CurOrderName,"?mode=audit");
   if(ret && ret.indexOf("<OK>")>=0){
     var objrow=tableCell.parentNode;
     //tableCell.style.textDecoration="underline";
     //tableCell.style.cursor="default";
     tableCell.style.color="#0000FF";//审核后临时显示成蓝色（刷新后将恢复黑色）
     tableCell.onclick=new Function("ChangePrice(this,"+OrderGoodsID+");"); 
     tableCell.title="原价"+objrow.getAttribute("price")+"元，点击修改";
     NonAudit--;
     objrow.setAttribute,("audit",'1');
   }
   else if(ret)alert(ret);
   else alert("操作失败，请稍候再试！");
}

function ChangeCost(tableCell,OrderGoodsID){
  var defValue=GetInnerText(tableCell);
  var pname=GetInnerText(tableCell.parentNode.cells[CELL_NAME]);
  var getresult = function(newValue){
    if(newValue && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("selectid="+OrderGoodsID+"&newvalue="+newValue+"&ordername="+CurOrderName,"?mode=cost");
      if(ret && ret.indexOf("<OK>")>=0){
	 tableCell.innerHTML="<font color=#FF0000>"+newValue+"</font>";
  	 //if(!CostOrScore)SetInnerText(tableCell.parentNode.cells[CELL_SCORE],"?");
  	  OrderRestat();
      }
      else if(ret) alert(ret);
      else{ 
        alert("操作失败，请稍候再试！");
   	self.location.reload();
      }
    } 
    return true;
  }
  AsyncPrompt("设定商品成本价",pname,getresult,parseFloat(defValue),8);  
}


/*
function ChangeCost(TdCell,ProductID)
{ var ret = window.showModalDialog("changeprice.asp?id="+ProductID+"&handle="+Math.random(),0,"dialogWidth:580px;dialogHeight:"+(100+ModalDialogHeightExt())+"px;status:no;scroll:no")
  if(ret)
  { TdCell.innerHTML="<font color=#FF0000>"+ret[0]+"</font>"
  }
}
*/

function ChangeDepot(step){
  if(step>=CurOrderState) alert("订单已确认，无法修改！"); 
  else{
    var ExportOrImport=(step==-2);
    var OldDepot=(ExportOrImport)?Order_Exporter:Order_Importer;
    var OnGetDepotSelection=function(NewDepot){
       if(NewDepot!=null && NewDepot!=OldDepot){
         var OnSaveDepot=function(ret){
           if(ret && ret.indexOf('<OK>')>=0){
             alert('操作成功！');
             self.location.reload();
           }
           else if(ret)alert(ret);
         };
         AsyncPost("ordername="+CurOrderName+"&newvalue="+NewDepot,(ExportOrImport)?"?mode=changeexporter":"?mode=changeimporter",OnSaveDepot);
       }
       return true;
    };
    var dlgHTML='<form name="selectdepot" style="margin:0px" onsubmit="closeDialog(this.depot.value);return false;"><TABLE width="100%" height="100%"  border="1"  align="center" bordercolor="#FF6600" bgcolor="#FF6600" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"> <TR><TD width="100%"><font color="#FFFFFF"><strong>目标地址选择：</strong></font></TD></TR><TR bgcolor="#f7f7f7"><TD width="100%" align="center"><select name="depot"></select></TD></TR><TR bgcolor="#f7f7f7"><TD align="right" bgcolor="#FFCC00"><input type="submit" value=" 确定 ">&nbsp;</TD></TR></TABLE></form>';
    AsyncDialog('选择仓库',dlgHTML,200,120,OnGetDepotSelection);   
    var myform=document.forms['selectdepot'];
    for(var i=0;i<Depot_Options.length;i++) myform.depot.options.add(Depot_Options[i]);
    myform.depot.value=OldDepot;
  }
}


function ChangeRemark(tableCell,OrderGoodsID){
  var defValue=GetInnerText(tableCell).trim();
  var pName=GetInnerText(tableCell.parentNode.cells[CELL_NAME]);
  var getresult = function(newValue){
    if(newValue!=null && newValue!=defValue){
      var ret=SyncPost("selectid="+OrderGoodsID+"&newvalue="+encodeURIComponent(newValue)+"&ordername="+CurOrderName,"?mode=remark");
      if(ret && ret.indexOf("<OK>")>=0){
        if(newValue!="") tableCell.innerHTML="<MARQUEE onmouseover=\"this.stop()\" onmouseout=\"this.start()\"  style=\"cursor:pointer\" width=100% scrollAmount=2 scrollDelay=100 style=\"color:#FF0000\" >"+newValue+"</MARQUEE>";
   	else tableCell.innerHTML="&nbsp;&nbsp;";
      }
      else{
        alert("操作失败，请稍候再试！");
        self.location.reload();
      }
    } 
    return true;
  }
  AsyncPrompt("设定商品备注",pName,getresult,defValue,255);
}

function ChangeDeliveryFee(panel){
  var defValue=parseFloat(GetInnerText(panel)); //parseFloat可剔除前置的+号
  var getresult = function(newValue){
    if(newValue!=null && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("ordername="+CurOrderName+"&newvalue="+newValue,"?mode=deliveryfee");
  	if(ret && ret.indexOf("<OK>")>=0){
  	  var obj=document.getElementById("TotalPriceCounter");
	  if(obj)obj.innerHTML="<input type=button value='刷新' onclick='self.location.reload()'>"
          if(newValue>0)newValue='+'+newValue;
          SetInnerText(panel,newValue); 
          //alert("订单配送费用修改成功！");
        }
  	else if(ret)alert(ret);
        else{
           alert("操作失败，请稍后重试！");
  	   self.location.reload();
        }
    } 
    return true;
  }
  AsyncPrompt("订单设置","重设订单配送费用:",getresult,defValue,8);  
}

function ChangeAdjustFee(panel){
  var defValue=parseFloat(GetInnerText(panel));//parseFloat可剔除前置的+号
  var getresult = function(newValue){
    if(newValue!=null && newValue!=defValue && !isNaN(newValue)){
      var ret=SyncPost("ordername="+CurOrderName+"&newvalue="+newValue,"?mode=adjust");
      if(ret && ret.indexOf("<OK>")>=0){
	var obj=document.getElementById("TotalPriceCounter");
	if(obj)obj.innerHTML="<input type=button value='刷新' onclick='self.location.reload()'>"
        if(newValue>0)newValue='+'+newValue;
        SetInnerText(panel,newValue); 
        //alert("订单折扣设置成功！");
      }
      else if(ret)alert(ret);
      else{
  	alert("操作失败，请稍后重试！");
  	self.location.reload();
      } 
    }
    return true;
  }
  AsyncPrompt("订单货款调整","（注：正值使订单总额增加，负值使订单总额减少）",getresult,defValue,8);  
}

function OrderRestat()
{ var obj=document.getElementById("OrderStatRow");
	if(obj)obj.style.display="none";
	obj=document.getElementById("TotalPriceCounter");
	if(obj)obj.innerHTML="<input type=button value='刷新' onclick='self.location.reload()'>"
}

function CheckOrderInfo(myform)
{ if(myform.receipt.value.trim()=="")
	{	alert("收货人为空！");
		return false;
	}
	else if(myform.address.value.trim()=="")
	{ alert("收货地址为空！");
		return false;
	}
	else if(myform.usertel.value.trim()=="")
	{ alert("联系电话为空！");
		return false;
	}
	else if(myform.paymethod.value.trim()=="")
	{ alert("支付方式为空！");
		return false;
	}
	return true;
}

function DeliveryTrack(myform){
  var Order_DeliveryCode=myform.deliverycode.value;
  var Order_DeliveryMethod=myform.deliverymethod.value;
  if(Order_DeliveryCode.length>2){
    window.open("../user/deliverytrack.php?method="+Order_DeliveryMethod+"&code="+Order_DeliveryCode);
  }	
  else
  { alert("货单号码无效！");
  }
}


function SignChange(obj)
{ var OrderStateChanged,changeindex,selboxes; 
	selboxes=obj.form.elements;
  if(obj==selboxes[0])changeindex=0;
  else if(obj==selboxes[1])changeindex=1;
  else if(obj==selboxes[2])changeindex=2;
  else if(obj==selboxes[3])changeindex=3;
  else return false;
 		
  if(CurOrderState==2)
  { selboxes[1].disabled=!selboxes[0].checked;
  	selboxes[0].disabled=selboxes[1].checked;
  	OrderStateChanged=(!selboxes[0].checked || selboxes[1].checked);
  }
  else if(CurOrderState==3)
  { selboxes[2].disabled=!selboxes[1].checked;
  	selboxes[1].disabled=selboxes[2].checked;
  	OrderStateChanged=(!selboxes[1].checked || selboxes[2].checked );
  }
  else if(CurOrderState==-2)
  { selboxes[1].disabled=!selboxes[0].checked;
  	selboxes[0].disabled=selboxes[1].checked;
  	OrderStateChanged=(!selboxes[0].checked || selboxes[1].checked);
  }
  else if(CurOrderState==-3)
  { if(changeindex==2 && selboxes[0].value==selboxes[1].value){
      alert('出库与入库点不能相同！');
      return false;
    }
    selboxes[2].disabled=!selboxes[1].checked;
    selboxes[1].disabled=selboxes[2].checked;
    OrderStateChanged=(!selboxes[1].checked || selboxes[2].checked);
  }  
  else
  { OrderStateChanged=obj.checked;
  }
  obj.parentNode.getElementsByTagName("span")[changeindex].style.color=(OrderStateChanged)?"#FF0000":"#000000";
  if( CurOrderState>0 && OrderStateChanged && !OwnPopedomFinance && (selboxes[3].checked || (selboxes[2].checked && !IsOrderManager))  ) OrderStateChanged=false;
	
  if(obj.form.confirmbutton)obj.form.confirmbutton.disabled=!OrderStateChanged;
	
  return true;
}

function ChangeOrderState(myform){
  var orderinfo=document.forms["orderinfo"];
  var newstate=0; 
  var selboxes=myform.elements; 	
  if(CurOrderState==1){
    if(selboxes[0].checked) newstate=2;
  }  
  else if(CurOrderState==2){
    if(selboxes[1].checked) newstate=3;
    else if(!selboxes[0].checked)newstate=1; 
  }  
  else if(CurOrderState==3){
    if(selboxes[2].checked){
      if(orderinfo){
        var deliverycode=orderinfo.deliverycode;
        if(deliverycode){
          deliverycode=deliverycode.getAttribute("savedvalue");
          if(deliverycode===''){
            alert('请先提交物流单号！');
            orderinfo.deliverycode.focus();
            return false;
          }
        }  
      }
      if(confirm("确定要改变订单状态吗？\n\n（注：此操作不可逆）")) newstate=4;
    }
    else if(!selboxes[1].checked)newstate=2; 
  }  
  else if(CurOrderState==4){
    if(selboxes[3].checked){
      if(NonAudit>0) alert("还有"+NonAudit+"件商品价格未审核！");
      else if(confirm('确定要改变订单状态吗？\n\n（注：此操作不可逆）')) newstate=5;
    }
  }
  else if(CurOrderState==-1){
    if(selboxes[0].checked) newstate=-2;
  }
  else if(CurOrderState==-2){
    if(selboxes[1].checked) newstate=-3;
    else if(!selboxes[0].checked)newstate=-1; 
  }
  else if(CurOrderState==-3){
    if(selboxes[2].checked){
      if(confirm("确定要改变订单状态吗？\n\n（注：此操作不可逆）")) newstate=-4;
    }
    else if(!selboxes[1].checked)
    { newstate=-2; 
    }  
  }
  if(newstate!=0 && !ChangingOrderState){
    myform.confirmbutton.disabled=true;
    myform.confirmbutton.value="正在处理,请耐心等待...";
    if(orderinfo)orderinfo.confirmbutton.disabled=true;
    var weight_btn=document.getElementById("checkweightbtn");
    if(weight_btn)weight_btn.disabled=true;
    myform.newstate.value=newstate;
    ChangingOrderState=true;
    return true;
  }else return false;
}

function DeleteMyOrder(){
  if(CurOrderState==1 || CurOrderState==-1){
    if(confirm("确定要删除该订单？") && confirm("此操作将删除该订单及其包含的所有内容，是否继续？") && confirm("点击[确定]完成订单删除！\n 注：此操作不可逆！")){
      var ret=SyncPost('ordername='+CurOrderName,'?mode=delete');
      if(ret && ret.indexOf('<OK>')>=0){
        alert('订单删除成功！');          
        self.location.href=(CurOrderState>0)?'mg_orders.php':'mg_privateorders.php';
      }
      alert(ret);
    }
  }else alert("该订单状态无法删除！");  
}

function CopyProducts(){
  var selarray=Checkbox_SelectedValues("selectid");
  if(selarray){
    var OnGetOrderName=function(NewOrder){
      if(NewOrder){
 	if(NewOrder.trim()==CurOrderName) alert("不能复制到该订单本身,请输入其他订单号码！");
  	else{
          var ret=SyncPost("selectid="+selarray.join(',')+"&ordername="+CurOrderName+"&desorder="+NewOrder,"?mode=copy");
          if(ret){
            alert(ret);
            if(ret.indexOf("成功")>=0)self.location.reload();
          }else alert("操作失败！");
  	} 
      }
      return true;
    }
    AsyncPrompt("复制商品","将选定的"+selarray.length+"件商品复制到其他订单，<br>如果确认操作，请输入目标订单的单号：",OnGetOrderName);
  }
  else alert("没有选择操作对象！");
}

function MigrateProducts(){
  var selarray=Checkbox_SelectedValues("selectid");
  if(selarray){
    var OnGetOrderName=function(NewOrder){
      if(NewOrder){
	if(NewOrder.trim()==CurOrderName) alert("不能转移到该订单本身,请输入其他订单号码！");
	else{
	  var ret=SyncPost("selectid="+selarray.join(',')+"&ordername="+CurOrderName+"&desorder="+NewOrder,"?mode=migrate");
	  if(ret){
	    alert(ret);
	    if(ret.indexOf("成功")>=0)self.location.reload();
	  }else alert("操作失败！");
	}
      }
      return true;
    }
    AsyncPrompt("转移商品","将选定的"+selarray.length+"件商品移动到其他订单，<br>如果确认操作，请输入目标订单的单号：",OnGetOrderName);
  }else alert("没有选择操作对象！");
}
function RemoveProducts(){
  var selarray=Checkbox_SelectedValues("selectid");
  if(selarray){
    if(confirm("将从订单中删除"+selarray.length+"件商品,是否继续此操作？") && confirm("点击[确定]完成商品移除！\n 注：此操作不可逆！")){
      var ret=SyncPost("selectid="+selarray.join(',')+"&ordername="+CurOrderName,"?mode=remove");
      if(ret){
         alert(ret);
         if(ret.indexOf("成功")>=0)self.location.reload();
      }
      else alert("操作失败！");
    }
  }
  else alert("没有选择操作对象！");
}

function AddNewProductToOrder(){
  var GetProductCode=function(pid){
    if(pid && pid!="0" && !isNaN(pid)){
      var SaveProductToOrder=function(ret){
        if(ret && ret.indexOf("<OK>")>=0){
          alert("添加成功！");
          self.location.reload(); 
        }
        else if(ret)alert(ret);  
      } 
      AsyncDialog("添加产品到订单","add2order.php?ordername="+CurOrderName+"&productid="+pid+"&handle="+Math.random(),650,265,SaveProductToOrder);
      return true;
    }else alert("请输入有效的商品号码！");  
  }
  AsyncPrompt("添加产品到订单","请输入新增商品的编号或条码:",GetProductCode,"");
}

function GetOption(bGot,opt)
{ return (bGot)?opt:"";
}

function ShowOrderState()
{	var StateCode='<form name="stateform" method="post" action="?mode=orderstate" style="margin:0px" onsubmit="return ChangeOrderState(this);">';
	//订单状态开始
	StateCode+='<img src="images/pic21.gif" width=17 height=15 align="absmiddle">订单流程：';
		
	StateCode+="<input ";
	StateCode+=GetOption( (CurOrderState>1) , " checked " );
	StateCode+=GetOption( (CurOrderState>2) , " disabled " );
	StateCode+=GetOption( (CurOrderState<=2), " title='订单确认后将被锁定，只有管理员才能修改订单内容！' " );
	StateCode+="  type=\"checkbox\" onclick=\"return SignChange(this)\"><span>订单确认</span>→ "; 
	
	StateCode+="<input ";
	StateCode+=GetOption( (CurOrderState>2) , " checked " );
	StateCode+=GetOption( (CurOrderState<2 || CurOrderState>3) , " disabled " );
	StateCode+="  type=\"checkbox\" onclick=\"return SignChange(this)\"><span>配货打包</span>→ ";
	
	StateCode+="<input ";
	StateCode+=GetOption( (CurOrderState>3) , " checked " );
	StateCode+=GetOption( (CurOrderState!=3) , " disabled " );
	StateCode+=" type=\"checkbox\"  onclick=\"return SignChange(this)\"><span>仓库出货</span>→ ";

	StateCode+="<input ";
	StateCode+=GetOption( (CurOrderState>4) , " checked " );
	StateCode+=GetOption( (CurOrderState!=4) , " disabled " );
  StateCode+=" type=\"checkbox\" onclick=\"return SignChange(this)\"><span>财务结算</span> " 
  
  if(CurOrderState<5){
    StateCode+='&nbsp;<input type="hidden" name="newstate"><input type="hidden" name="username" value="'+CurUserName+'"><input type="hidden" name="ordername" value="'+CurOrderName+'"><input type="submit" name="confirmbutton" value="修改状态" disabled>';
  }
  else{
    StateCode+="→ <input type='checkbox' disabled "+GetOption((CurOrderState>5),"checked")+">客户签收";
  }
  //订单状态结束
  
  StateCode+="</form>";
  document.getElementById("OrderStatePanel").innerHTML=StateCode;
}	

function Show_PrivateOrderState()
{	var tempValue;
	var StateCode='<form name="stateform" method="post" action="?mode=orderstate" onsubmit="return ChangeOrderState(this)" style="margin:0px"><table align="right" cellpadding=0 cellspacing=0 border=0 bordercolor="#FFFFFF" bgcolor="#CCCCCC">';
	StateCode+='<tr><td background="images/topbg.gif" nowrap>';
	StateCode+='<img src="images/pic21.gif" width=17 height=15 align="absmiddle"><b>订单流程</b>：';
	StateCode+='<input name="exporter" ';
	StateCode+=GetOption( (CurOrderState<-1) , " checked " );
	StateCode+=GetOption( (CurOrderState<-2)||(!IsOrderManager), " disabled " );
	if(!AllowModifyProduct)tempValue="style='color:#FF0000'";
	else tempValue='style="color:#0000FF;text-decoration:underline;cursor:pointer" onclick="ChangeDepot(-2)"';
	StateCode+=' type="checkbox" onclick="return SignChange(this)"><font id="dsp_exporter" '+tempValue+'></font><span>出库审核</span>→ '; 
	
	StateCode+='<input name="importer" ';
	StateCode+=GetOption( (CurOrderState<-2) , " checked " );
	StateCode+=GetOption( (CurOrderState>-2 || CurOrderState<-3)||(!IsOrderManager) , " disabled " );
	
	if(!AllowModifyProduct)tempValue="style='color:#FF0000'";
	else tempValue='style="color:#0000FF;text-decoration:underline;cursor:pointer" onclick="ChangeDepot(-3)"';
	StateCode+='  type="checkbox" onclick="return SignChange(this)"><font id="dsp_importer" '+tempValue+'></font><span>入库审核</span>→ '; 
	
	StateCode+="<input ";
	StateCode+=GetOption( (CurOrderState<-3) , " checked " );
	StateCode+=GetOption( (CurOrderState!=-3)||(!IsOrderManager) , " disabled " );
	StateCode+='  type="checkbox" onclick="return SignChange(this)"><span>完成</span>';
  if(CurOrderState>-4 && IsOrderManager)
	{  StateCode+='&nbsp; <input type="hidden" name="newstate"><input type="hidden" name="username" value="'+CurUserName+'"><input type="hidden" name="ordername" value="'+CurOrderName+'"><input type="submit" name="confirmbutton" value="修改状态" disabled >';
  }
  StateCode+="</td>";
  /*
  if(AllowModifyProduct)
  { StateCode+='<td align="right" width="1%" background="images/topbg.gif" nowrap>';
    StateCode+=' &nbsp; <input name="AddWare" type="button"  value="添加商品.." onclick="AddNewProductToOrder()">';
    StateCode+="</td>";
  } */  
  StateCode+="</tr></table></form>";
  document.getElementById("OrderStatePanel").innerHTML=StateCode;
}	

function  CheckProductAmount(tdCell,stock,amount){
  var ChildStyle=tdCell.children[0].style;
  var ParentStyle=tdCell.parentNode.style;
  if(amount==0){
    ParentStyle.color=ChildStyle.color="#BFBFBF";
    ChildStyle.textDecoration="none";
    tdCell.innerHTML=tdCell.children[0].outerHTML;
  }
  else if(amount>stock){
    ParentStyle.color="#000000";ChildStyle.color="#FF0000";
    ChildStyle.textDecoration="line-through";
    tdCell.innerHTML=tdCell.children[0].outerHTML+" <img src='images/lack.gif' border=0 width=16 height=16>";
  }
  else{
    ParentStyle.color=ChildStyle.color="#000000";
    ChildStyle.textDecoration="none";
    tdCell.innerHTML=tdCell.children[0].outerHTML;
  }
} 

     
function InitOrderEditor(){
  var i,obj,mytable,myrow,mycells,stock,amount,NewAddProduct,goodsID,ProductCode,rowcount;
  mytable = document.getElementById("MyTableID");
  if(mytable){
    rowcount=mytable.rows.length;
    NewAddProduct=getCookie("newgoods");
    NewAddProduct=(isNaN(NewAddProduct))?0:parseInt(NewAddProduct);
  }
  for(i=1;i<rowcount-1;i++){//去首尾
    myrow=mytable.rows[i];
    mycells=myrow.cells;
    stock=myrow.getAttribute("stock")
    if(stock!=null && !isNaN(stock)){
      stock=parseInt(stock);
      goodsID=parseInt(mycells[CELL_GID].getElementsByTagName("input")[0].value);
			
      //obj=mycells[CELL_PID].children.tags("a")[0];
      obj=mycells[CELL_PID].getElementsByTagName("a")[0];

      ProductCode=GetInnerText(obj);
      if(goodsID==NewAddProduct){
        obj.style.fontWeight="bold";
	obj.style.color="#FF0000";
      }

      obj.title=((CurOrderState>0)?"当":"本")+"地库存"+stock+"件，点击查看详情...";

      amount=GetInnerText(mycells[CELL_AMOUNT]);
      amount=parseInt(amount);

      if(amount==0){
	myrow.style.color="#BFBFBF";
	mycells[CELL_NAME].children[0].style.color="#BFBFBF";
      }
      else if(amount>stock && CurOrderState<4 && CurOrderState>-4){
	obj=mycells[CELL_NAME];
	obj.innerHTML=obj.innerHTML+" <img src='images/lack.gif' border=0 width=16 height=16>";
	obj.children[0].style.textDecoration="line-through";
	obj.children[0].style.color="#FF0000";
      }

      if(AllowModifyProduct){
	obj=mycells[CELL_AMOUNT];
	obj.title="点击修改";
	obj.style.cursor="pointer";
	obj.style.textDecoration="underline";
	obj.onclick=new Function("ChangeAmount(this,"+goodsID+");"); 

	obj=mycells[CELL_SCORE];
	obj.title="点击修改";
	obj.style.cursor="pointer";
	obj.style.textDecoration="underline";

	if(CostOrScore) obj.onclick=new Function("ChangeCost(this,"+goodsID+");"); 
	else obj.onclick=new Function("ChangeScore(this,"+goodsID+");"); 

	obj=mycells[CELL_REMARK];
	obj.title="点击修改";
	obj.style.cursor="pointer";
	obj.style.textDecoration="underline";
	obj.onclick=new Function("ChangeRemark(this,"+goodsID+");"); 
      }

      if(AllowAuditPrice && myrow.getAttribute("audit")=='0'){
	  obj=mycells[CELL_PRICE];
	  obj.style.color="#FF0000";
	  obj.title="原价"+myrow.getAttribute("price")+"元，点击审核";
	  obj.style.cursor="pointer";
	  obj.style.textDecoration="underline";
	  obj.onclick=new Function("AuditGoods(this,"+goodsID+");"); 
	  NonAudit++;
      }
      else if(AllowAuditPrice || AllowModifyProduct){
        var origin=myrow.getAttribute("price");
        obj=mycells[CELL_PRICE];
        obj.style.cursor="pointer";
        obj.style.textDecoration="underline";
        if(typeof(origin)=='string' && origin!=GetInnerText(obj)){
          obj.style.color='#FF0000';
          obj.title="原价"+origin+"元，点击修改,";
        }
        else{
          obj.style.color='#000000';
          obj.title="点击修改";
        }
        obj.onclick=new Function("ChangePrice(this,"+goodsID+");"); 
      }
    }	
  }
} 

function GetDepotName(id){
  for(var i=0;i<Depot_Options.length;i++){
    if(id==Depot_Options[i].value)return Depot_Options[i].text;
  }
  return null;
}

function InitDepot(importer,exporter,depots){
  var stateform=document.forms["stateform"];
  Depot_Options=depots;
  Order_Exporter=exporter;
  Order_Importer=importer;
  if(stateform){
    if(stateform.exporter){
      stateform.exporter.value=exporter;
      document.getElementById("dsp_exporter").innerHTML=GetDepotName(exporter); 
    }
    if(stateform.importer){
      stateform.importer.value=importer;
      document.getElementById("dsp_importer").innerHTML=GetDepotName(importer); 
    }
  }
}
   
function InitMyOrder(order_name,order_user,order_state,is_OrderManager,own_PopedomFinance,is_CostOrScore)
{	CurOrderName=order_name;
	CurUserName=order_user; 
	CurOrderState=order_state;
	IsOrderManager=is_OrderManager; /*当前客服是否允许处理订单*/
	OwnPopedomFinance=own_PopedomFinance;
	AllowModifyProduct=IsOrderManager && order_state<3 && order_state>-2;
        AllowAuditPrice=OwnPopedomFinance && CurOrderState>2 && CurOrderState<5; 
	if(order_state>0)ShowOrderState();
	else Show_PrivateOrderState();
        CostOrScore=is_CostOrScore;
	if(CurOrderState>-4 && CurOrderState<5)InitOrderEditor();
}   
