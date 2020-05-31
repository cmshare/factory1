const PREFIX_BRAND='brand_',PREFIX_CATEGORY='cat_';
var catsel_data=[];
function cids_label_delete(cid){
  catdata_delete(cid);
  cids_label_reload();
}

function cids_label_reload(){
  var cat_title,labels_html='';
  for(var index=0,count=catsel_data.length;index<count;index++){
     labels_html+='<li><span>'+catsel_data[index].title+'</span><a onclick="cids_label_delete('+catsel_data[index].id+')"></a></li>';
  }
  document.getElementById('cid_labels').innerHTML=labels_html?labels_html:'<span style="color:#ccc;padding-left:6px;">尚未设置分类</span>';
}


function AppendCatSelector(stage,category,defauleValue,prefix){
  var selector=document.createElement("select");
  var options=selector.options;
  options.add(new Option("----分类选择----","0"));
  for(var i=0,count=category.length;i<count;i++){
    var cid=category[i].id;
    if(cid>1)options.add(new Option(category[i].title,cid));
  }
  selector.setAttribute("stage",stage);
  selector.setAttribute("prefix",prefix);
  selector.id=prefix+stage;
  selector.value=defauleValue;
  selector.onchange=category_change;
  if(prefix==PREFIX_CATEGORY)selector.style.color="#888";
  document.getElementById(prefix+"navigation").appendChild(selector);
}

function category_change(event){
  var selector=event.target,sel_cid=selector.value;
  var cur_stage=parseInt(selector.getAttribute("stage"));
  var prefix=selector.getAttribute("prefix");
  var cat_navigation=document.getElementById(prefix+"navigation");
  for(var i=cur_stage+1;i<10;i++){
    var del_selector=document.getElementById(prefix+i);
    if(del_selector)cat_navigation.removeChild(del_selector);
    else break;
  }
  if(sel_cid>0){
    var sel_cat=GetCategoryById(sel_cid);
    if(sel_cat){
      if(sel_cat.children){
        AppendCatSelector(cur_stage+1,sel_cat.children,0,prefix);
      }
      else if(prefix==PREFIX_CATEGORY){//添加选择
        for(var i=0,pid=0;i<=cur_stage;i++){
          selector=document.getElementById(prefix+i);
          catdata_check_insert(selector.value,pid,selector.options[selector.selectedIndex].text);
          pid=selector.value;
          if(i==0)selector.selectedIndex=0;
          else cat_navigation.removeChild(selector);
        }
        cids_label_reload();
      }
    }
  }   
}
 
function catdata_delete(cid){
  for(var i=0,count=catsel_data.length;i<count;i++){
    if(catsel_data[i].id==cid){
      catsel_data.splice(i,1);
      var subcat=GetCategoryById(cid);
      if(subcat && (subcat=subcat.children)){
        for(var j=0,subcount=subcat.length;j<subcount;j++) catdata_delete(subcat[j].id);
      }
      break;
    }
  }
}

function catdata_check_insert(cid,pid,title){
  catdata_delete(cid);
  catsel_data.push({id:cid,pid:pid,title:title});
}

function catdata_valid_entry(index){
  var catdata=catsel_data[index];
  if(catdata && catdata.id>1){
    var pid=catdata.pid;
    if(pid==0)return true;
    else if(pid>1){
      for(var i=0,count=catsel_data.length;i<count;i++){
        if(catsel_data[i].id==pid)return catdata_valid_entry(i);
      }
    }
  }
  return false;
}

function catdata_resort(){
  catsel_data.sort(function(a, b){return a.si - b.si}); 
}

function InitCategory(brandid,cids){
  var brand_ids=[],cids_array=cids.split(',');
  for(var i=0,count=cids_array.length;i<count;i++){
    var cid=cids_array[i];
    if(!isNaN(cid) && cid>1){
      var catdata=GetCategoryById(cid);
      if(catdata)catsel_data.push({id:catdata.id,pid:catdata.pid,si:catdata.si,title:catdata.title});
    }
  }
  for(var i=0,count=catsel_data.length;i<count;i++){
    if(!catdata_valid_entry(i)) {catsel_data.splice(i,1);i--;count--;}
  }
  catdata_resort();
  
  while(brandid>1){
    var catdata=GetCategoryById(brandid);
    if(catdata){
      brand_ids.splice(0,0,catdata.id);
      brandid=catdata.pid;
    }
    else break;
  }
  var stage_count=brand_ids.length||1;
  for(var stage=0,category=CategoryMap[0];stage<stage_count;stage++){
    var selectedIndex=0,catid=brand_ids[stage];
    category=category.children;
    for(var i=0,count=category.length;i<count;i++){
      if(category[i].id==catid){
        selectedIndex=i;
        break;
      }
    }
    AppendCatSelector(stage,category,catid,PREFIX_BRAND);
    category=category[selectedIndex];
  }
  cids_label_reload();
  AppendCatSelector(0,CategoryMap,0,PREFIX_CATEGORY);
}

function cids_data_generate(myform){
  var cids=[],brandid=0;
  for(var i=0;i<10;i++){
    var selector=document.getElementById(PREFIX_BRAND+i);
    if(selector){
      brandid=selector.value;
       if(brandid>0)cids.push(brandid);
       else {alert('请设置品牌分类');return false;}
    }
    else break;
  }
  var navcat_count=catsel_data.length;
  if(!navcat_count){
    alert('请设置导航分类');
    return false;
  }
  for(var i=0;i<navcat_count;i++){
    cids.push(catsel_data[i].id);
  }
  myform.brand.value=brandid;
  myform.cids.value=','+cids.join()+',';
  return true;
}


function UpdateProductHTML(productid){
  AsyncPost('id='+productid,'mg_htmgen.php?mode=product');
}

function ShowImagePreview(productcode){
  document.getElementById("preview_img").style.backgroundImage="url(/uploadfiles/ware/"+((productcode)?productcode:"nopic")+".jpg?"+Math.random()+")";	
}	

function UploadPicture(productcode){
  var onupload=function(ret){
    if(ret)ShowImagePreview(productcode);
    return true;
  }
  AsyncDialog('文件上传','includes/upload.php?type=ware&filenamed='+productcode+'.jpg&handle='+Math.random(), 500,150,onupload);
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
   /*
   if(myform.category.selectedIndex==0)
   { alert("请选择功能分类！");
	   return false;
   }

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
