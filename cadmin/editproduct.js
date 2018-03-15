function CheckProductPrice(myform)
{  var tempdata,price=Array(5);//results=[];

   tempdata=myform.price1.value.trim();
   price[1]=(tempdata && !isNaN(tempdata))?Math.round(parseFloat(tempdata)*100)/100:0;

   tempdata=myform.price2.value.trim();
   price[2]=(tempdata && !isNaN(tempdata))?Math.round(parseFloat(tempdata)*100)/100:0;

   tempdata=myform.price3.value.trim();
   price[3]=(tempdata && !isNaN(tempdata))?Math.round(parseFloat(tempdata)*100)/100:0;
   if(price[3]==0)
   { alert("请正确输入批发价！");
   	 myform.price3.focus();
   	 return null;
   }
    
   tempdata=myform.price4.value.trim();
   price[4]=(tempdata && !isNaN(tempdata))?Math.round(parseFloat(tempdata)*100)/100:0;
   if(price[4]==0)
   { alert("请正确输入大客户批发价！");
   	 myform.price4.focus();
   	 return null;
   } 

   if(myform.cost){
     tempdata=myform.cost.value.trim();
     if(tempdata=="")price[0]=null; //成本价留空表示不作改变
     else if(!isNaN(tempdata))price[0]=Math.round(parseFloat(tempdata)*100)/100;
     else
     { alert("成本价输入有误！");
       return null;
     }
   }
   else price[0]=null; //成本价留空表示不作改变
       
   if(price[1]<price[2] || price[2]<price[3] || price[3]<price[4] || (price[5]!=null && price[4]<price[0]))
   { alert("价格输入不完整或者价格分等不合理，请核实！");
   	 return null;
   }
   
   return price;
}

function CheckSaveProductInfo(myform)
{  var tempdata=myform.productname.value.trim();
   if(tempdata=="")
   { myform.productname.focus();
     alert("请输入商品名称！");
	   return false;
   }
   
   if(myform.brand.selectedIndex==0)
   { alert("请选择品牌分类！");
	   return false;
   }
   
   if(myform.category.selectedIndex==0)
   { alert("请选择功能分类！");
	   return false;
   }
/*
   tempdata=myform.barcode.value.trim();
   if(tempdata=="")
   { myform.barcode.focus();
     alert("请准确填写商品条形码，若此商品没有条码请填0！");
	   return false;
   }
 */
   if(myform.score)
   { tempdata=myform.score.value.trim();
     if(tempdata=="" || isNaN(tempdata))
     { myform.score.focus();
       alert("请正确输入商品积分！");
       return false;
     }
   }
   if(myform.weight)
   { tempdata=myform.weight.value.trim();
       //if(tempdata=="" || isNaN(tempdata))
     if(tempdata!="" && isNaN(tempdata))
     { myform.weight.focus();
       alert("请正确输入商品重量！");
       return false;
     }
   }

   if(!CheckProductPrice(myform))return false;
   
   myform.ConfirmButton.disabled=true;  
}


function UpdateProductHTML(productid){
  AsyncPost('id='+productid,'mg_htmgen.php?mode=product');
}

function AutoPriceClear(myform){
  myform.price1.value="";
  myform.price1.style.color="#000000"; 
  myform.price2.value="";
  myform.price2.style.color="#000000";
  myform.price4.value="";	
  myform.price4.style.color="#000000";
}

function AutoPriceFinish(myform){
  var tempdata,price1=0,price2=0,price3=0,price4=0,cost=0,price2_x;
  tempdata=myform.price3.value.trim();
  if(tempdata && !isNaN(tempdata))price3=parseFloat(tempdata);
  if(price3==0){
    alert("必须先输入有效的商品批发价！");
    return false;
  }

  if(myform.cost){
    tempdata=myform.cost.value.trim();
    if(tempdata && !isNaN(tempdata)) cost=parseFloat(tempdata);  	 
  }
  if(cost>0){ //在可取到有效成本条件下
    if(cost>price3){
      alert("成本价不能高于批发价！");
      return false;
    }
    tempdata=myform.price4.value.trim();
    if(tempdata && !isNaN(tempdata))price4=parseFloat(tempdata);
    if(price4>price3 || price4<=cost){//需要调整price4
      price4=cost+(price3-cost)*0.75;
      if(price4>10)price4=Math.round(price4);
      else price4=Math.round(price4 * 10) / 10;
      myform.price4.value=price4; 
      myform.price4.style.color="#FF0000";//改变颜色以警示 
    }
  }

  tempdata=myform.price1.value.trim();
  if(tempdata && !isNaN(tempdata))price1=parseFloat(tempdata);
      
  if(price1<price3){
    var multiples;
    if(price3<1)multiples=6;
    else if(price3<5)multiples=5.81;
    else if(price3<10)multiples=5.52;
    else if(price3<15)multiples=5.23;
    else if(price3<20)multiples=4.91;
    else if(price3<25)multiples=4.62;
    else if(price3<30)multiples=4.23;
    else if(price3<40)multiples=3.81;
    else if(price3<50)multiples=3.42;
    else if(price3<60)multiples=3.23;
    else if(price3<70)multiples=3.11;
    else if(price3<80)multiples=3.02;
    else if(price3<90)multiples=2.93;
    else if(price3<100)multiples=2.81;
    else if(price3<120)multiples=2.72;
    else if(price3<150)multiples=2.63;
    else multiples=2.51
    price1=price3*multiples;
    if(price1>10)price1=Math.round(price1);
    else price1=Math.round(price1 * 10) / 10;
    if(myform.price1.value!=price1){
      myform.price1.value=price1;
      myform.price1.style.color="#FF0000";	//改变颜色以警示 
    }  
  }
  price2=price3+(price1-price3)*0.35;
  price2_x=price3/0.82;
  if(price2<price2_x)price2=price2_x;
  else price2=(price2+price2_x)/2;
    
  if(price2>10)price2=Math.round(price2);
  else price2=Math.round(price2 * 10) / 10;

  if(myform.price2.value!=price2){
    myform.price2.value=price2;
    myform.price2.style.color="#FF0000";	//改变颜色以警示
  }   
}

function UploadPicture(productcode){
  var onupload=function(ret){
    if(ret)ShowImagePreview(productcode);
    return true;
  }
  AsyncDialog('文件上传','includes/upload.php?type=ware&filenamed='+productcode+'.jpg&handle='+Math.random(), 500,150,onupload);
}	

function ShowImagePreview(productcode){
  document.getElementById("preview_img").src="/uploadfiles/ware/"+((productcode)?productcode:"nopic")+".jpg?"+Math.random();	
}	

function DrawImage(ImgD)
{ var MaxWidth=260;
	var image2=new Image();
  image2.src=ImgD.src;
  if(image2.width>0 && image2.height>0)
  { if(image2.width>=MaxWidth)
    { ImgD.width=MaxWidth;
      ImgD.height=(image2.height*MaxWidth)/image2.width;
    }
    else if(image2.height>=MaxWidth)
    { ImgD.height=MaxWidth;
      ImgD.width=(image2.width*MaxWidth)/image2.height; 
    }
    else
    { ImgD.height=image2.height;
    	 ImgD.width=image2.width;
    }
  }
} 

function ProductResort(sort_name)
{ if(sort_name==getCookie("sort_name"))
	{ if(getCookie("sort_order")=="asc")
		  setCookie("sort_order","desc")
		else
		  setCookie("sort_order","asc")  
	}
	else
	{ setCookie("sort_name",sort_name)
		setCookie("sort_order","desc")
	}
	self.location.reload();
}


function ChangeStock(productID){
  var callback=function(ret){
    if(ret){
      if(ret)self.location.reload();
      return true;	
    } 	
  };
  AsyncDialog('商品库存调整',"changestock.php?productid="+productID+"&handle="+Math.random(),500,200,callback);
}

function InitStock(productid,panel){
  var onclose=function(ret){
    if(!isNaN(ret)){
      panel.innerHTML=ret;
      alert("库存修改成功！");
    }
    return true;
  }
  AsyncDialog("商品库存明细", "checkstock.php?id="+productid+"&mode=edit&handle="+Math.random(),600,130,onclose);
}

function ChangeScore(tdcell,productID){
 var defvalue=(tdcell.innerText||tdcell.textContent).trim();
 var OnGetValue=function(newvalue){
    if(newvalue!=null){
      if(isNaN(newvalue) || newvalue<0)alert("输入数字无效！");
      else if(newvalue!=defvalue){
        var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"?mode=score");
        if(ret){
          if(ret.indexOf("<OK")>=0){
            tdcell.innerHTML=newvalue;
            self.closePrompt();
          }
          else alert(ret);
        }
      }
      else{
        alert("没有变化！");
        self.closePrompt();
      }
    }
  }
  var titlecell=tdcell.parentNode.cells[2];
  var productname=(titlecell.innerText||titlecell.textContent);
  AsyncPrompt(productname,"请重新设该商品积分：",OnGetValue,defvalue,4);
}

function ChangeWeight(tdcell,productID){
  var defvalue=(tdcell.innerText||tdcell.textContent).trim();
  var OnGetValue=function(newvalue){
    if(newvalue!=null){
      if(isNaN(newvalue) || newvalue<0)alert("设置的重量无效！");
      else if(newvalue!=defvalue){
        var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"mg_products.php?mode=weight");
        if(ret){
          if(ret.indexOf("<OK")>=0){
            tdcell.innerHTML=newvalue;
            self.closePrompt();
          }
          else alert(ret);
        }
      }
      else{
        alert("没有变化！");
        self.closePrompt();
      }
    }
  }
  var titlecell=tdcell.parentNode.cells[2];
  var productname=(titlecell.innerText||titlecell.textContent);
  AsyncPrompt(productname,"重新设定该商品重量(单位:克):",OnGetValue,defvalue,4);
}

function ChangeRecommend(tdcell,productID){
 var defvalue=(tdcell.innerText||tdcell.textContent).trim();
 var OnGetValue=function(newvalue){
    if(newvalue!=null){
      if(isNaN(newvalue) || newvalue<1 || newvalue>25)alert("推荐指数无效！");
      else if(newvalue!=defvalue){
        var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"mg_products.php?mode=recommend");
        if(ret){
          if(ret.indexOf("<OK")>=0){
            tdcell.innerHTML=newvalue;
            self.closePrompt();
          }
          else alert(ret);
        }
      }
      else{
        alert("没有变化！");
        self.closePrompt();
      }
    }
  }
  var titlecell=tdcell.parentNode.cells[2];
  var productname=(titlecell.innerText||titlecell.textContent);
  AsyncPrompt(productname,"请重新设定推荐指数(1～25),指数越大推荐度越高：",OnGetValue,defvalue,2);
}

function ChangeBarcode(tdcell,productID){
  var defvalue=(tdcell.innerText||tdcell.textContent).trim();
  var OnGetValue=function(newvalue){
    if(newvalue!=null){
      if(isNaN(newvalue))alert("条码数字无效！");
      else if(newvalue!=defvalue){
        var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"mg_products.php?mode=barcode");
        if(ret){
          if(ret.indexOf("<OK")>=0){
            tdcell.innerHTML=newvalue;
            self.closePrompt();
          }
          else alert(ret);
        }
      }
      else{
        alert("没有变化！");
        self.closePrompt();
      }
    }
  }
  var titlecell=tdcell.parentNode.cells[2];
  var productname=(titlecell.innerText||titlecell.textContent);
  AsyncPrompt(productname,"请重新设该商品条码:",OnGetValue,defvalue,20);
}

function ChangeOnsale(tdcell,productID,minValue){
  var defvalue=(tdcell.innerText||tdcell.textContent).trim();
  var OnGetValue=function(newvalue){
    if(newvalue!=null){
      if(isNaN(newvalue) || newvalue<minValue || newvalue>5)alert("特价指数无效！");
      else if(newvalue!=defvalue){
        var ret=SyncPost("selectid="+productID+"&newvalue="+newvalue,"mg_products.php?mode=onsale");
        if(ret){
          if(ret.indexOf("<OK")>=0){
            tdcell.innerHTML=newvalue;
            self.closePrompt();
          }
          else alert(ret);
        }
      }
      else{
        alert("没有变化！");
        self.closePrompt();
      }
    }
  }
  var titlecell=tdcell.parentNode.cells[2];
  var productname=(titlecell.innerText||titlecell.textContent);
  if(isNaN(minValue))minValue=0;
  AsyncPrompt(productname,"请重新设该商品特价指数（"+minValue+"~5）:",OnGetValue,defvalue,1);
}

function BatchOnsale(myform,OnOff)
{ var onsale,selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else{
    if(OnOff){
      onsale=window.prompt("重新设定所选"+selcount+"件商品的特价指数(1～5):\n注：指数越高特价排行越靠前\n", "");
      if(onsale!=null){
          if(isNaN(onsale) || onsale<1 || onsale>5){
             alert('特价指数值必须在1～5范围内！');
	     return;
	  }
          else{
	    onsale=parseInt(onsale); 
	    if(!confirm("确定要将所选"+selcount+"件商品的特价指数设为"+onsale+"吗？"))return;
          }
      }else return;
    }
    else{
       onsale=0;
       if(!confirm("确定要将所选的"+selcount+"件商品取消特价吗？"))return;
    }
    myform.action = "mg_products.php?mode=batchonsale&onsale="+onsale;
    myform.submit();
  }
}

      
function BatchForwardProduct(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else if(confirm("确定要将所选的"+selcount+"件商品上架吗？")){
    myform.action = "mg_products.php?mode=forward";
    myform.submit();
  }
}

function BatchWithdrawProduct(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0){
     alert("没有选择操作对象！");
  }
  else if(confirm("确定要将所选的"+selcount+"件商品下架吗？"))
  { myform.action = "mg_products.php?mode=withdraw";
    myform.submit();
  }
}

function OnAddProduct(ret)
{ alert(ret);
  if(ret.indexOf("<OK")>=0)Checkbox_SelectAll('selectid[]',false);
}

function BatchChangeBrand(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(!selcount){
    alert("先选中待修改分类的商品！");
  }
  else{
    var onclose=function(ret){
      if(!isNaN(ret)){
         myform.action = "mg_products.php?mode=batchbrand&newvalue="+ret;
         myform.submit();
      }
    }
    AsyncDialog("修改产品分类","batchbrand.html?selcount="+selcount,475,80,onclose);
  }
}

function AddToCart(myform){
  var selarray=Checkbox_SelectedValues("selectid[]");
  if(!selarray){
    alert("先选中待加入购物车的商品！");
  }
  else{
    var callback=function(ret){
      if(ret){
        AsyncPost("selectid="+selarray.join(',')+"&username="+encodeURIComponent(ret[0])+"&amount="+ret[1]+"&remark="+encodeURIComponent(ret[2]),"mg_stock.php?mode=addtocart",OnAddProduct);
        return true;	
      } 	
    };
    AsyncDialog('添加到购物车',"add2cart.html?count="+selarray.length,550,200,callback);
  }
}

function AddToOrder(myform){
  var selarray=Checkbox_SelectedValues("selectid[]");
  if(!selarray){
     alert("先选中待加入订单的商品！");
  }
  else{
    var callback=function(ret){
      if(ret){
       AsyncPost("selectid="+selarray.join(',')+"&ordername="+encodeURIComponent(ret[0])+"&amount="+ret[1]+"&remark="+encodeURIComponent(ret[2]),"mg_stock.php?mode=addtoorder",OnAddProduct);
       return true;	
      }
    };
    AsyncDialog('添加到订单',"add2order.html?count="+selarray.length,550,200,callback);
  }
}

function ChangeShelf(value){
  setCookie("onshelf",value)
  self.location.reload();
}

function ChangeCustomField(newfield){
  setCookie("customfield",newfield);
  self.location.reload();
}

function BatchDeleteProduct(myform){
  var selcount=Checkbox_SelectedCount("selectid[]");
  if(selcount==0) alert("没有选择操作对象！");
  else if(confirm("确定要永久删除所选的"+selcount+"件商品吗？")){
    myform.action = "mg_products.php?mode=delete";
    myform.submit();
  } 
}

function ProSearchAutoSelect(selectm,selectkey){
    if(selectm){
      var targetform=document.forms["schform"];
      var serchoptions=targetform.scm;
      var optionlength=serchoptions.options.length;
      var i;
      for(i=0;i<optionlength;i++){
        if(selectm==serchoptions.options[i].value){
          serchoptions.options[i].selected=true;
          targetform.sck.value=selectkey;
          break;
        }
      }
    }
}

