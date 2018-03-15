var OpInterval,SecondCounter=0,enable_running=false,QueryURL,CurrentRow,suspend_task=0;

function GetNextRow(row)
{ return (row)?row.parentNode.rows[row.rowIndex+1]:null;   
}

function FilterUpdateResult(strHtmlCode){
  if(strHtmlCode.indexOf('[OK]')>=0) return '<font color="#FF0000">更新完成</font>';
  else{
    var items=strHtmlCode.match(/alert\(\"([^\"]+)/g);
    if(items){
      var i,len=items.length;
      strHtmlCode="";
      for(i=0;i<len;i++){
	if(i>0)strHtmlCode=strHtmlCode+"&nbsp; ";
	strHtmlCode=strHtmlCode+items[i].replace("alert(\"","");
      }
    }
    return strHtmlCode;
  }
} 

function UpdateItem(srcElement,postfields){
  var objrow=srcElement.parentNode.parentNode;
  var col_count=objrow.cells.length;
  var state_label=objrow.cells[col_count-2];
  var got_result=function(ret){
    state_label.innerHTML=FilterUpdateResult(ret);
    suspend_task--;
  };
  if(!postfields)postfields="";
  suspend_task++;
  state_label.innerHTML='Updating...';
  AsyncPost(postfields,QueryURL,got_result);
  if(!enable_running)CurrentRow=GetNextRow(objrow);
}

function UpdateAllItems(){
  if(enable_running && CurrentRow){
    var col_count=CurrentRow.cells.length;
    var srcElement=CurrentRow.cells[col_count-1].children[0];
    if(srcElement && srcElement.tagName=='INPUT' && srcElement.type=='button'){
      if(SecondCounter>0){
        if(!suspend_task) CurrentRow.cells[col_count-2].innerHTML=SecondCounter;
        SecondCounter--;
      }
      else{
        srcElement.click();
        SecondCounter=OpInterval;
        CurrentRow=GetNextRow(CurrentRow);
      }
      setTimeout("UpdateAllItems()",1000); 
    }  
    else{
      CurrentRow=GetNextRow(CurrentRow);
      UpdateAllItems();
    }
  }
}
 
function ControlUpdate(switchvalue){
  if(enable_running!=switchvalue){
    enable_running=switchvalue;
    if(enable_running){
      SecondCounter=OpInterval;
      UpdateAllItems();
    }
  }
}

function InitHtmlUpdate(update_interval,query_url,table_name){
  OpInterval=update_interval;
  QueryURL=query_url;
  CurrentRow=document.getElementById(table_name).rows[0]; 
}
