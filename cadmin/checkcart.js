var PIDArray=[],StockArray=[],PriceArray=[],ScoreArray=[],AmountArray=[],ProductCounter=0,downcounter=0,hCounter=null;

function ResetStat(){
  var TotalCount=0,TotalPrice=0,TotalScore=0;
  for(var i=0;i<ProductCounter;i++){
    TotalCount+=AmountArray[i];
    TotalPrice+=PriceArray[i]*AmountArray[i];
    TotalScore+=ScoreArray[i]*AmountArray[i];
  }
  SetInnerText(document.getElementById("TotalCount"),TotalCount);
  SetInnerText(document.getElementById("TotalPrice"),Math.round(TotalPrice*10)/10);
  SetInnerText(document.getElementById("TotalScore"),TotalScore);
}

function QuitSave(myform){
  if(hCounter){
    clearInterval(hCounter);
    hCounter=null;
    downcounter=10;
  }	 
  document.getElementById("addpreview").style.display="none";
  myform.savebtn.value="保存";
  myform.codetext.focus();
}

function StartDownCounter()
{ var myform=document.forms[0];
	if(!hCounter)  hCounter=setInterval("DoDownCounter()", 1000);
	downcounter=10;
	document.getElementById("addpreview").style.display="";
  document.getElementById("retstatus").innerHTML="";
  myform.codetext.value="";	
  myform.codetext.disabled=false;
	myform.confirmbtn.disabled=false;
	myform.savebtn.disabled=false;
}

function DoDownCounter()
{ if(hCounter)
	{ if(downcounter>0)
		{ downcounter--;
			document.forms[0].savebtn.value="保存("+downcounter+")";
    }
    else
	  { clearInterval(hCounter);
	  	hCounter=null;
	  	downcounter=10;
	  	document.forms[0].savebtn.value="保存";
		  document.forms[0].savebtn.click();
	  }  
	}
}

function SaveToCart(myform){
  var i,infos,amount,productcode,ret;
  amount=myform.productamount.value.trim();
  productcode=myform.productcode.value.trim();
  if( !amount || isNaN(amount)){
    alert("数量无效！");
    myform.productamount.focus();
    myform.productamount.select();
    return false;
  }
  else if( !productcode || isNaN(productcode)){
    alert("商品编号无效！");
    return false;
  }
  myform.savebtn.disabled=true;
  ret=SyncPost("mode=save&productid="+parseInt(productcode,10)+"&amount="+amount+"&userid="+myform.userid.value,"");
  infos=(ret)?ret.split("|"):"";
  if(infos.length==9){ 
    if(infos[1]=="0"){
      PIDArray[ProductCounter]=parseInt(infos[2]);
      PriceArray[ProductCounter]=parseFloat(infos[5]); 
      StockArray[ProductCounter]=parseInt(infos[4]); 
      ScoreArray[ProductCounter]=parseInt(infos[6]);
      AmountArray[ProductCounter]=parseInt(infos[7]);
      AddRow(ProductCounter,infos[3],infos[8]);//append
      ProductCounter++;
    }
    else{
      var productid=parseInt(productcode,10)
      for(i=0;i<ProductCounter;i++){
        if(PIDArray[i]==productid){
  	  AmountArray[i]=parseInt(infos[7]);
	  UpdateRow(i);//modify
	  break;
	}
      }
    } 
    QuitSave(myform); 
    document.getElementById("retstatus").innerHTML="<img src='images/pic21.gif' width=17 height=15>商品成功保存到购物车！";
    ResetStat();
  }
  else{
    alert("保存失败，请重试！"+ret);
  }
  myform.savebtn.disabled=false;
}

 
function FormatProductCode(productid){
   var produccode=productid.toString();
   while(produccode.length<5)produccode="0"+produccode;
   return produccode;
}

function GetTdByCode(codetype,codevalue)
{ var i,allrows,totalrow,celloffset;
  if(ProductCounter>0)
  { totalrow=mytable.rows.length;
    allrows=mytable.rows;
    if(codetype==0)celloffset=2;
    else if(codetype==1)celloffset=1;
    else celloffset=3; 
 	  for(i=0;i<totalrow;i++)
 	  { if(GetInnerText(allrows[i].cells[celloffset])==codevalue)break;
 	  }
 	 return (i<totalrow)?allrows[i].cells:null;
  }else return null;
}

function SetStatusHTML(text){
  var obj=document.getElementById("retstatus");
  if(obj)obj.innerHTML=text;
}

function GetProductInfo(myform,codename,codetext)
{ return SyncPost("mode=search&"+codename+"="+encodeURIComponent(codetext)+"&userid="+myform.userid.value,"");
}
   
function AddProductToCart2(myform,codetype,codetext){
  var codename,mytds,infos,productcount=0,ret=null;
  if(codetype==0){
    codename="barcode";
    ret=GetProductInfo(myform,codename,codetext);
    if(ret){
      infos=ret.split("|");
      productcount=Math.floor(infos.length/3);
    }	 
  }
  else if(codetype==1){
    codename="productcode";
    codetext=FormatProductCode(codetext);
  }
  else codename="productname";

  if(!(codetype==0 && productcount>1)){
    if(codetext==myform.elements[codename].value){
      var amount=myform.productamount.value;
      if(isNaN(amount))myform.productamount.value="1";
      else myform.productamount.value=parseInt(amount)+1;
      StartDownCounter();
      return true;
    }
  }  
  while(hCounter)SaveToCart(myform);
		
  myform.codetext.disabled=true;
  myform.confirmbtn.disabled=true;
  SetStatusHTML('<img src="images/loading1.gif" width=100 height=9>');

  mytds=(codetype==0 && productcount>1)?null:GetTdByCode(codetype,codetext);
  if(mytds){
    myform.productcode.value=GetInnerText(mytds[1]);/*ProductCode的形式*/
    myform.barcode.value=GetInnerText(mytds[2]);
    myform.productname.value=GetInnerText(mytds[3]);
    myform.productamount.value=parseInt(GetInnerText(mytds[4]))+1;
    StartDownCounter();
    return true;
  }
  else{
    if(!ret){
      ret=GetProductInfo(myform,codename,codetext);
      if(ret){
        infos=ret.split("|");
        productcount=Math.floor(infos.length/3);
      }	 
    }
    if(ret){
      if(productcount==1){
        myform.productcode.value=FormatProductCode(infos[1]);/*ProductCode的形式*/
        myform.barcode.value=infos[2];
        myform.productname.value=infos[3];
        myform.productamount.value="1";
        StartDownCounter();
        return true;
      }
      else if(productcount>1){
        var maxproductcount=100;
	var jishu,selections="<select onchange='if(this.value>0)AddProductToCart2(this.form,1,this.value)'><option value='0'>---匹配到"+productcount+((productcount==maxproductcount)?"多":"")+"个产品---</option>";
        for(jishu=0;jishu<productcount;jishu++){
          selections=selections+"<option value='"+infos[jishu*3+1]+"'>"+infos[jishu*3+3]+"</option>"
        }
        if(productcount==maxproductcount)selections+="<option>...更多省略...</option>"
	selections+="</select>";
        SetStatusHTML(selections);
      }
      else if(ret.indexOf("<NONE>")>=0){
        var codetitle;
        if(codetype==0)codetitle="产品条码";
        else if(codetype==1)codetitle="产品编号"
        else codetitle="产品名称";
        SetStatusHTML('<img src="images/linkspic4.gif" width=15 height=16>此'+codetitle+'不存在，或者已经下架！');
      }
      else SetStatusHTML('<img src="images/linkspic4.gif" width=15 height=16>'+ret);
    }else SetStatusHTML('<img src="images/linkspic4.gif" width=15 height=16>服务器请求失败，可能网速太慢，请刷新重试！');
  }
  myform.codetext.disabled=false;
  myform.confirmbtn.disabled=false;
}

function AddProductToCart(myform){
  var codetext,codetype,ret;
  codetext=myform.codetext.value.trim();
  if(!codetext){
    myform.codetext.focus();
    if(hCounter)SaveToCart(myform);
    return false;
  }
  codetype=myform.codetype.selectedIndex;
  ret=AddProductToCart2(myform,codetype,codetext);
  if(ret && codetype>0){
    myform.productamount.focus();
    myform.productamount.select();
  }
  else myform.codetext.focus();
  return ret;
}

function GenProductLink(productid){
  return "/products/"+productid+".htm";
}

function AddRow(rowid,barcode,productname){ /*在表格头部增加一行数据*/
  var ProductCode=FormatProductCode(PIDArray[rowid])
  var newRow   =   DummyTable.rows[0].cloneNode(true);   
  var lastRow   =   mytable.rows[1];   
  newRow.setAttribute("id",ProductCode); 
  mytable.tBodies[0].insertBefore(newRow,lastRow);  
  newRow.cells[0].getElementsByTagName("input")[0].value=PIDArray[rowid];
  newRow.cells[1].innerHTML='<a href="stocklog.php?productid='+PIDArray[rowid]+'" title="商品库存'+StockArray[rowid]+'件，点击查看详情...">'+ProductCode+'</a>';
  newRow.cells[2].innerText=barcode;
  newRow.cells[3].innerHTML='<a href="'+GenProductLink(PIDArray[rowid])+'" target="_blank">'+productname+'</a>';
  newRow.cells[4].innerText=AmountArray[rowid];
  newRow.cells[5].innerText=PriceArray[rowid];
  newRow.cells[6].innerText=ScoreArray[rowid]; 
  CheckAmount(newRow,StockArray[rowid],AmountArray[rowid]);
  if(rowid==0){
     var tds=document.getElementById("dummyrow");
     if(tds)tds.parentNode.removeChild(tds); 
      /*tds.style.display="none"; mytable.deleteRow(2);*/
  } 
}


function  CheckAmount(objRow,stock,amount)
{ var ChildStyle1=objRow.cells[1].children[0].style;
	var ChildStyle2=objRow.cells[3].children[0].style;
	var ParentStyle=objRow.style;
	if(amount==0) 
	{ ParentStyle.color=ChildStyle1.color=ChildStyle2.color="#BFBFBF";
		ChildStyle2.textDecoration="none";
		objRow.cells[3].innerHTML=objRow.cells[3].children[0].outerHTML;
	}
	else if(amount>stock) 
	{ ParentStyle.color=ChildStyle1.color="#000000";
		ChildStyle2.color="#FF0000";
		ChildStyle2.textDecoration="line-through";
		objRow.cells[3].innerHTML=objRow.cells[3].children[0].outerHTML+" <img src='images/lack.gif' border=0 width=16 height=16>";
	}
 	else
 	{ ParentStyle.color=ChildStyle1.color=ChildStyle2.color="#000000";
 		ChildStyle2.textDecoration="none";
 		objRow.cells[3].innerHTML=objRow.cells[3].children[0].outerHTML;
 	}
} 
	
function UpdateRow(rowid){/*更新表格某一行数据，并将其移动到表格第一行*/
  var ProductCode=FormatProductCode(PIDArray[rowid]);
  var srcRow=document.getElementById(ProductCode);
  if(srcRow){
    var newRow=srcRow.cloneNode(true);
    srcRow.parentNode.removeChild(srcRow); /*mytable.deleteRow(i);*/
    mytable.tBodies[0].insertBefore(newRow,mytable.rows[1]);
    newRow.cells[4].innerText=AmountArray[rowid];/*更新数量放到最后*/
    CheckAmount(newRow,StockArray[rowid],AmountArray[rowid]);
  }
}
 
function InitCodetype()
{ var codetype=getCookie("codetype");
  document.forms[0].codetype.selectedIndex=(codetype=="0" || codetype=="1" || codetype=="2")?parseInt(codetype):0; 
}

function ChangeAmount(tableCell){
  var cells=tableCell.parentNode.cells;
  var ProductCode=GetInnerText(cells[1]);
  var ProductName=GetInnerText(cells[3]);
  var defValue=GetInnerText(tableCell);
  var UserID=document.forms[0].userid.value;
  var getresult=function(newValue){
    if(newValue==defValue){
      alert("没有变化！");
      return true;
    }
    else if(newValue  && !isNaN(newValue)){
      newValue=parseInt(newValue);
      if(newValue>=0){
        var ret=SyncPost("mode=amount&userid="+UserID+"&productid="+ProductCode+"&amount="+newValue,"");
        if(ret && ret.indexOf("<OK>")>=0){
          for(var i=0;i<ProductCounter;i++){
            if(PIDArray[i]==ProductCode){
              var srcRow=document.getElementById(ProductCode);
              tableCell.innerHTML=newValue;
              AmountArray[i]=newValue;
              CheckAmount(srcRow,StockArray[i],AmountArray[i]);
              ResetStat();
              break;
            }
          }
          return true;
        }
      }
    } 
    alert("未知错误！"); 
  } 
  AsyncPrompt("设定商品订购数量",ProductName,getresult,parseInt(defValue),8); 
}

function ChangeRemark(tableCell){
  var cells=tableCell.parentNode.cells;
  // var cells=tableCell.parentNode.getElementsByTagName('td');
  var ProductCode=GetInnerText(cells[1]);
  var ProductName=GetInnerText(cells[3]);
  var defValue=GetInnerText(tableCell).trim();
  var userid=document.forms[0].userid.value;
  var getresult = function(newValue){
    if(newValue==defValue){
      alert("没有变化！");
      return true;
    }
    else if(newValue!=null){
       var ret=SyncPost("mode=remark&userid="+userid+"&productid="+ProductCode+"&remark="+encodeURIComponent(newValue),"");
       if(ret && ret.indexOf("<OK>")>=0){
         newValue=newValue.replace(/</g,"&lt;").replace(/>/g,"&gt;");
         tableCell.innerHTML=(newValue.length<10)?newValue:('<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'+newValue+'</MARQUEE>');
         return true;
       }
    }  
  }
  AsyncPrompt("设定商品备注",ProductName,getresult,defValue,255); 
}

function CheckUser(userid)
{ window.open("mg_usrinfo.php?id="+userid,'',"scrollbars=yes,width=500,height=500")
}

function SubmitCart(myform){
  self.location.href="mg_submitcart.php?userid="+myform.userid.value;
}

function DeleteFromMycart(myform)
{ var SelectIDs="";
  var SelectCount=0;
  var a = document.getElementsByName("selectid[]");
  for (var i=0; i<a.length; i++)
  { if (a[i].type == "checkbox" && a[i].checked && a[i].value!="0")
    { if(++SelectCount==1) SelectIDs=a[i].value;
    	else SelectIDs=SelectIDs+","+a[i].value;
    }
  }
  if(SelectCount>0)
  { if(confirm("将从购物车中删除"+SelectCount+"件商品,是否继续此操作？"))
    { var ret=SyncPost("mode=delete&userid="+myform.userid.value+"&selectid="+SelectIDs,"");
      if(ret){
        if( ret.indexOf("<OK>")>=0)
        { alert("选定的商品已经从您的购物车中删除！");
          Checkbox_SelectAll('selectid[]',false);
          self.location.reload();
        }
        else alert(ret);
      }
    }
  }
  else
  { alert("没有选择操作对象！");
  }
}
