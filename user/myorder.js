var MyOrderName=null,MyOrderState=0;

 function UpdatePagePosition(mode)
 { if(mode==0)  /*save position*/
     setCookie("PagePosition",document.body.scrollTop);
  else if(mode==1) /*load and restore last position*/
  {  document.body.scrollTop=getCookie("PagePosition");
  	 //if(QQServiceCode) document.all("QQService").innerHTML="<b>客服支持</b>："+QQServiceCode+"&nbsp;";
  }
 }
  
function DeleteOrder()
{ if(MyOrderState>1)
	{ alert("无法删除已确认的订单！");
	}
	else if(MyOrderState==1)
	{if (confirm("您确定要删除该订单?"))
	 { MyTestForm.action="?mode=delete&ordername="+MyOrderName;
     MyTestForm.submit();
	 }
	}
}

function ChangeRemark(tableCell,OrderGoodsID,ProductName){
  var defvalue=(tableCell.children.length>0)?tableCell.children[0].innerHTML:"";
  var pname=tableCell.parentNode.cells[1].children[0].innerHTML;
  var getresult = function(ret){
    if(ret!=null && ret!=defvalue){
      MyTestForm.action="?mode=remark&selectid="+OrderGoodsID+"&ordername="+MyOrderName;
      MyTestForm.newvalue.value=ret;
      MyTestForm.submit();
     }
     return true;
  }
  AsyncPrompt("设定商品备注",pname,getresult,defvalue,255);
}

function ChangeAmount(tableCell,OrderGoodsID)
{ var defvalue=tableCell.innerHTML;
	var pname=tableCell.parentNode.cells[1].children[0].innerHTML;
	var getresult = function(ret)
	{ if(ret && ret!=defvalue && !isNaN(ret))
	  { var newvalue=parseInt(ret);
		  if(newvalue>=0)
		  { MyTestForm.action="?mode=amount&selectid="+OrderGoodsID+"&ordername="+MyOrderName;
  	  	MyTestForm.newvalue.value=newvalue;
  	  	MyTestForm.submit();
	    }else alert("数字无效！"); 
	  } 
	  return true;
	}
	AsyncPrompt("设定商品订购数量",pname,getresult,parseInt(defvalue),8);  
}

function AddNewProductToOrder(){
  var GetProductCode=function(pid){
    if(pid && pid!="0" && !isNaN(pid)){
      var SaveProductToOrder=function(ret){
        if(ret){
           MyTestForm.action="?mode=addnew&ordername="+MyOrderName+"&amount="+ret[1]+"&productid="+ret[0];
           MyTestForm.newvalue.value=ret[2];
           MyTestForm.submit();	 
         }  
      } 
      AsyncDialog("添加产品到订单","add2order.php?ordername="+MyOrderName+"&productid="+pid+"&handle="+Math.random(),650,265,SaveProductToOrder); 
      return true;
    }  
  }
  AsyncPrompt("添加产品到订单","请输入新增商品的编号:",GetProductCode,0);
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
	return true;
}
 
function DeliveryTrack(Order_DeliveryMethod,Order_DeliveryCode)
{	self.location.href="deliverytrack.php?DeliveryMethod="+Order_DeliveryMethod+"&DeliveryCode="+Order_DeliveryCode;
}

function ShowHelp()
{ alert("修改数量： 找到您要修改的商品，在右边点击该商品的当前数量，在弹出的对话框中填上新的数值。\n删除商品： 将商品数量改为0即可。\n添加商品： 点击右上角的按钮---\"新增商品到订单...\"，在弹出的对话框中填上您所需添加的商品编号。\n修改备注： 点击对应商品右边的备注区，在弹出的对话框中进行修改。");
}

function CompleteOrder()
{ if (confirm("您是否确认已收到货?"))
  { MyTestForm.action="?mode=complete&ordername="+MyOrderName;
    MyTestForm.submit();
 }
}

function InitMyOrder(order_name,order_state)
{	MyOrderState=order_state;
	MyOrderName=order_name;
  InitOrderTable()
}


function InitOrderTable()
{ var i,obj,mytable,allrows,stock,amount,NewAddProduct,goodsID,ProductCode,rowcount=0;
	mytable = document.getElementById("MyTableID");
	if(mytable)
	{ allrows=mytable.rows;
		rowcount=mytable.rows.length;
		NewAddProduct=getCookie("NewAddProduct");
		NewAddProduct=(isNaN(NewAddProduct))?0:parseInt(NewAddProduct);
	}
	for(i=0;i<rowcount;i++)
	{ stock=allrows[i].getAttribute("stock")
		goodsID=allrows[i].getAttribute("goodsID")
		
		if(stock!=null && goodsID!=null)
		{ stock=parseInt(stock);
			
			goodsID=parseInt(goodsID);
			
			obj=allrows[i].cells[0];
			ProductCode=obj.innerHTML;
			if(ProductCode.length<5)
			{ do{ProductCode="0"+ProductCode;}while(ProductCode.length<5);
				obj.innerHTML=ProductCode;
			}
			
			if(goodsID==NewAddProduct) 
			{ obj.style.fontWeight="bold";
				obj.style.color="#FF0000";
			}
			
			amount=allrows[i].cells[2].innerHTML;
			amount=parseInt(amount);
			
			if(amount==0)
			{
				allrows[i].style.color="#BFBFBF";
				allrows[i].cells[1].childNodes[0].style.color="#BFBFBF";
			}
			else if(amount>stock && MyOrderState>0 && MyOrderState<4)
			{ obj=allrows[i].cells[1];
				
				obj.innerHTML=obj.innerHTML+" <img src='../images/lack.gif' border=0 width=16 height=16>";
        obj.childNodes[0].style.textDecoration="line-through";
        obj.childNodes[0].style.color="#FF0000";
        obj.title="库存"+stock+"件，请联系客服核实！";
			}
			
			if(MyOrderState==1)
			{ obj=allrows[i].cells[2];
				obj.title="点击修改";
				obj.style.cursor="pointer";
				obj.style.textDecoration="underline";
				obj.onclick=new Function("ChangeAmount(this,"+goodsID+");"); 
				obj=allrows[i].cells[4];
				obj.title="点击修改";
				obj.style.cursor="pointer";
				obj.style.textDecoration="underline";
				obj.onclick=new Function("ChangeRemark(this,"+goodsID+");"); 
		  }	
	  }	
		
	}
} 
       
