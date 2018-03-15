<?php header("content-Type: text/html; charset=utf-8");
date_default_timezone_set("PRC");
InitUpload("cmupload||||||office2003|||/uploadfiles/|||550|||350|||rar|zip|pdf|doc|xls|ppt|chm|hlp|||swf|||gif|png|jpg|jpeg|bmp|||rm|mp3|wav|mid|midi|ra|avi|mpg|mpeg|asf|asx|wma|mov|||gif|png|jpg|bmp|||1024|||2048|||2048|||100|||2048|||1|||1|||EDIT|||1|||0|||0|||||||||1|||0|||V4.x版保留,standard550标配,office2003皮肤|||||||||1|||300|||120|||1|||涵若铭妆|||BBBBBB|||32|||simkai.ttf||||||0|||jpg|jpeg|||100|||888888|||1|||1|||bin|tar|gz|apk|||32768|||100|||5|||66|||17|||5|||5|||0|||100|||100|||5|||5|||5|||88|||31|||1|||0|||1|||1|||1|||1|||0|||");
DoCreateNewDir();//创建分类子目录
DoSave(); 

function DoSave()
{ global $sFilenamed,$sSaveFileName,$nAllowSize,$sOriginalFileName;
  echo "<html><head><title>eWebEditor</title><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><body>";
  if($_FILES['uploadfile']['error'] > 0)
  { switch((int)$_FILES['uploadfile']['error'])
  	{ case UPLOAD_ERR_NO_FILE:throwUploadError("ErrUploadInvalidFile");break;
      case UPLOAD_ERR_FORM_SIZE:throwUploadError("ErrUploadSizeLimit");break;
    }
    exit;
  }
  if ($_FILES['uploadfile']['size'] > (int)$nAllowSize*1024)
  { throwUploadError("ErrUploadSizeLimit");
    exit;
  }

  preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES['uploadfile']['name'],$exts);
  if(!IsAllowedExt($exts[1]))
  { throwUploadError("ErrUploadInvalidExt");
    exit;
  }

  $sOriginalFileName = $_FILES['uploadfile']['name'];
  if(empty($sFilenamed)) $sSaveFileName = GetRndFileName(strtolower($exts[1]));
  else{
    $dotpos=strpos($sFilenamed,'.');
    if($dotpos===false)$sSaveFileName = $sFilenamed.".".strtolower($exts[1]);
    else if($dotpos>0) $sSaveFileName = $sFilenamed;
    else $sSaveFileName = $sOriginalFileName;
  } 
  if(!move_uploaded_file($_FILES['uploadfile']['tmp_name'],$GLOBALS["sUploadDir"].$GLOBALS["sSaveFileName"]))
  { throwUploadError("Error");
    exit;
  }
  
  if($GLOBALS['sType']=='WARE'){ 
    $s_SmallImageFile = getSmallImageFile($GLOBALS["sSaveFileName"]);
    if (makeImageSLT($GLOBALS["sUploadDir"], $GLOBALS["sSaveFileName"], $s_SmallImageFile)){//generate thumbnail
      $s_SLFilePath=$GLOBALS["sUploadDir"].'bigger/';
      if(@copy($GLOBALS["sUploadDir"].$GLOBALS["sSaveFileName"],$s_SLFilePath.$GLOBALS["sSaveFileName"])){ 
         makeImageSY($s_SLFilePath, $GLOBALS["sSaveFileName"]);//add water print
      }
    }
  }
  $GLOBALS["sPathFileName"] = $GLOBALS["sContentPath"].$GLOBALS["sSaveFileName"];
  echo '<table width=100% height=100% border=0><tr><td align=center valign=middle>文件成功上传：<a href="'.$GLOBALS['sPathFileName'].'" style="color:#FF6600" target="_blank">'.$GLOBALS['sPathFileName'].'</a><br><br><input type=button onclick="parent.closeDialog(\''.$GLOBALS['sPathFileName'].'\');" value="上传完成"></td></tr></table>';
}

function throwUploadError($errcode){
  switch($errcode){
    case "ErrUploadInvalidFile":$ermsg="请选择有效的上传文件！";break;
    case "ErrUploadSizeLimit":$ermsg="您上传的文件总大小超出了最大（".number_format($GLOBALS["nAllowSize"])."KB）限制！";break;
    case "ErrUploadInvalidExt":$ermsg="提示：\n\n请选择一个有效的文件，\n支持的格式有".$GLOBALS["sAllowExt"];break;
    case "ErrUploadMcufwVer":$ermsg="单片机升级文件版本无效！";break;
    default:$ermsg="未知错误！";break;
  }
  echo "<table width=100% height=100% border=0><tr><td align=center valign=middle>【Error】：$ermsg<br><br><a href='javascript:history.back()'>返回</a></td></tr></table>";
}

//图片生成水印
function makeImageSY($s_Path, $s_File){
  if(($GLOBALS["nSYWZFlag"]==0)&&($GLOBALS["nSYTPFlag"]==0))return false;
  if(!isValidSLTSYExt($s_File))return false;
  if($GLOBALS["nSLTSYObject"]==0){
      $groundImage = $s_Path.$s_File;
      if(!file_exists($groundImage))return false;
      $ground_info = getimagesize($groundImage); 
      $ground_w = $ground_info[0];
      $ground_h = $ground_info[1];
      switch($ground_info[2]){
        case 1: $ground_im = imagecreatefromgif($groundImage); break; 
        case 2: $ground_im = imagecreatefromjpeg($groundImage); break;
        case 3: $ground_im = imagecreatefrompng($groundImage); break;
        default: return false;
      }
      imagealphablending($ground_im, true);
      if($GLOBALS["nSYWZFlag"]==1){
        if(($ground_w<$GLOBALS["nSYWZMinWidth"])||($ground_h<$GLOBALS["nSYWZMinHeight"]))return false;
        $posX = getSYPosX($GLOBALS["nSYWZPosition"], $ground_w, $GLOBALS["nSYWZTextWidth"]+$GLOBALS["nSYShadowOffset"], $GLOBALS["nSYWZPaddingH"]);
        $posY = getSYPosY($GLOBALS["nSYWZPosition"], $ground_h, $GLOBALS["nSYWZTextHeight"]+$GLOBALS["nSYShadowOffset"], $GLOBALS["nSYWZPaddingV"]);
        if($GLOBALS["sSYFontName"]){
          $s_SYText = $GLOBALS["sSYText"];
          $fontSize = imagettfbbox($GLOBALS["nSYFontSize"], 0, $GLOBALS["sSYFontName"], $s_SYText);
          $n_SYWidth = $fontSize[2] - $fontSize[0];
          $n_SYHeight = $fontSize[1] - $fontSize[7];
        }
        if($GLOBALS["sSYShadowColor"]==""){
          $GLOBALS["sSYShadowColor"] = "ffffff";
        }
        $R = hexdec(substr($GLOBALS["sSYShadowColor"],0,2));
        $G = hexdec(substr($GLOBALS["sSYShadowColor"],2,2));
        $B = hexdec(substr($GLOBALS["sSYShadowColor"],4));
        $textcolor = imagecolorallocate($ground_im, $R, $G, $B);

        if($GLOBALS["sSYFontName"]){
          imagettftext($ground_im, $GLOBALS["nSYFontSize"], 0, $posX+$GLOBALS["nSYShadowOffset"], $posY+$n_SYHeight+$GLOBALS["nSYShadowOffset"], $textcolor, $GLOBALS["sSYFontName"],  $s_SYText);
        }
        else{
          imagestring($ground_im, $GLOBALS["nSYFontSize"], $posX+$GLOBALS["nSYShadowOffset"], $posY+$GLOBALS["nSYShadowOffset"], $GLOBALS["sSYText"], $textcolor);
        }
        if($GLOBALS["sSYFontColor"]==""){
          $GLOBALS["sSYFontColor"] = "000000";
        }
        $R = hexdec(substr($GLOBALS["sSYFontColor"],0,2));
        $G = hexdec(substr($GLOBALS["sSYFontColor"],2,2));
        $B = hexdec(substr($GLOBALS["sSYFontColor"],4));
        $textcolor = imagecolorallocate($ground_im, $R, $G, $B);
        if($GLOBALS["sSYFontName"]){
          imagettftext($ground_im, $GLOBALS["nSYFontSize"], 0, $posX, $posY+$n_SYHeight, $textcolor, $GLOBALS["sSYFontName"],  $s_SYText);
        }
        else{
          imagestring($ground_im, $GLOBALS["nSYFontSize"], $posX, $posY, $GLOBALS["sSYText"], $textcolor);
        }
      }
      if($GLOBALS["nSYTPFlag"]==1){
        $waterImage = $GLOBALS["sSYPicPath"];
        if(!file_exists($waterImage))return false;
        $water_info = getimagesize($waterImage); 
        $water_w = $water_info[0];
        $water_h = $water_info[1];
        switch($water_info[2]){
          case 1: $water_im = imagecreatefromgif($waterImage); break;
          case 2: $water_im = imagecreatefromjpeg($waterImage); break;
          case 3: $water_im = imagecreatefrompng($waterImage); break;
          default: return false;
        }
        //if(($ground_w<$water_w)||($ground_h<$water_h)){return false;}
        if(($ground_w<$GLOBALS["nSYTPMinWidth"])||($ground_h<$GLOBALS["nSYTPMinHeight"]))return false;
        $posX = getSYPosX($GLOBALS["nSYTPPosition"], $ground_w, $GLOBALS["nSYTPImageWidth"], $GLOBALS["nSYTPPaddingH"]);
        $posY = getSYPosY($GLOBALS["nSYTPPosition"], $ground_h, $GLOBALS["nSYTPImageHeight"], $GLOBALS["nSYTPPaddingV"]);
        imagecopymerge($ground_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h, $GLOBALS["nSYTPOpacity"]*100);
      }
      //@unlink($groundImage);
      switch($ground_info[2]){
        case 1: imagegif($ground_im,$groundImage); break;
        case 2: imagejpeg($ground_im,$groundImage); break;
        case 3: imagepng($ground_im,$groundImage); break;
      }
      if(isset($water_info)) unset($water_info);
      if(isset($water_im)) imagedestroy($water_im);
      unset($ground_info);
      imagedestroy($ground_im);
  }
  return true;
}

function getSYPosX($posFlag, $originalW, $syW, $paddingH){
  switch($posFlag){
    case 1: case 2: case 3: return $paddingH; break;
    case 4: case 5: case 6: return floor(($originalW - $syW) / 2); break;
    case 7: case 8: case 9: return ($originalW - $paddingH - $syW); break;
  }
}

function getSYPosY($posFlag, $originalH, $syH, $paddingV){
  switch($posFlag){
    case 1: case 4: case 7: return $paddingV; break;
    case 2: case 5: case 8: return floor(($originalH - $syH) / 2); break;
    case 3: case 6: case 9: return ($originalH - $paddingV - $syH); break;
  }
}

function makeImageSLT($s_Path, $s_File, $s_SmallFile){
  if($GLOBALS["nSLTFlag"] == 0)return false;
  if(!isValidSLTSYExt($s_File))return false;
  switch($GLOBALS["nSLTSYObject"]){
    case 0:
      $s_Ext = substr(strrchr($s_File, "."), 1);
      switch($s_Ext){
        case "png": $im = imagecreatefrompng($s_Path.$s_File); break;
        case "gif": $im = imagecreatefromgif($s_Path.$s_File); break;
        default: $im = imagecreatefromjpeg($s_Path.$s_File); break;
      }
      if(!$im)return false;
      $n_OriginalWidth = imagesx($im);
      $n_OriginalHeight = imagesy($im);
      if(($n_OriginalWidth < $GLOBALS["nSLTMinSize"]) && ($n_OriginalHeight < $GLOBALS["nSLTMinSize"])) {return false;}
      if($n_OriginalWidth > $n_OriginalHeight){
        $n_Width = $GLOBALS["nSLTOkSize"];
        $n_Height = ($GLOBALS["nSLTOkSize"] / $n_OriginalWidth) * $n_OriginalHeight;}else{
        $n_Height = $GLOBALS["nSLTOkSize"];
        $n_Width = ($GLOBALS["nSLTOkSize"] / $n_OriginalHeight) * $n_OriginalWidth;}
        if(function_exists("imagecopyresampled")){
          $newim = imagecreatetruecolor($n_Width, $n_Height);
          imagecopyresampled($newim, $im, 0, 0, 0, 0, $n_Width, $n_Height, $n_OriginalWidth, $n_OriginalHeight);
        }
        else{
          $newim = imagecreate($n_Width, $n_Height);
          imagecopyresized($newim, $im, 0, 0, 0, 0, $n_Width, $n_Height, $n_OriginalWidth, $n_OriginalHeight);
        }
        touch($s_Path.$s_SmallFile);
        switch($s_Ext){
           case "png": imagepng($newim,$s_Path.$s_SmallFile); break;
           case "gif": imagegif($newim,$s_Path.$s_SmallFile); break;
           default: imagejpeg($newim,$s_Path.$s_SmallFile); break;
        }
        imagedestroy($newim);
        imagedestroy($im);
        break;
     default: break;
  }
  return true;
}

function isValidSLTSYExt($s_File)
{ $sExt = substr(strrchr($s_File, "."), 1);
  $aExt = explode('|',strtoupper($GLOBALS["sSLTSYExt"]));
  return in_array(strtoupper($sExt),$aExt);
}

function getSmallImageFile($s_File)
{ $exts = explode(".",$s_File);
  return $exts[0]."_small.".$exts[1];
}


function DoCreateNewDir()
{ if ($GLOBALS["nCusDirFlag"]==1)
	{ $a = explode("/", $GLOBALS["sCusDir"]);
    for($i=0; $i<count($a); $i++)
    { if ($a[$i]!="") CreateFolder($a[$i]);
    	
    }
  }
  switch($GLOBALS["nAutoDir"])
  { Case 1:$s_DateDir = date("Y");break;
    Case 2:$s_DateDir = date("Ym");break;
    Case 3:$s_DateDir = date("Ymd");break;
    default:$s_DateDir = "";break;
  }
  if ($s_DateDir!="") CreateFolder($s_DateDir);
}

function CreateFolder($s_Folder)
{ $GLOBALS["sUploadDir"] = $GLOBALS["sUploadDir"].$s_Folder."/";
  $GLOBALS["sContentPath"] = $GLOBALS["sContentPath"].$s_Folder."/";
  if (!is_dir($GLOBALS["sUploadDir"]))
  { mkdir($GLOBALS["sUploadDir"]);
  }
}

function GetRndFileName($sExt)
{ return date("YmdHis").rand(1,999).".".$sExt;
}

function IsAllowedExt($sExt){
  global $sAllowExt;
  if($sExt && $sAllowExt){
    if($sAllowExt=='*')return true;
    else if(in_array(strtoupper($sExt),explode('|',$sAllowExt)))return true;
  }
  return false;
}

function RelativePath2RootPath($url)
{ $sTempUrl = $url;
  if (substr($sTempUrl, 0, 1) == "/")
  { return $sTempUrl;
  }
  if (isset($_SERVER["REQUEST_URI"]))
  { $sWebEditorPath = $_SERVER["REQUEST_URI"];
  }
  else
  { $sWebEditorPath = $_SERVER["SCRIPT_NAME"];
  }
  $sWebEditorPath = substr($sWebEditorPath, 0, strrpos($sWebEditorPath, "/"));
  while (substr($sTempUrl, 0, 3) == "../")
  { $sTempUrl = substr($sTempUrl, 3, strlen($sTempUrl));
    $sWebEditorPath = substr($sWebEditorPath, 0, strrpos($sWebEditorPath, "/"));
  }
  return $sWebEditorPath."/".$sTempUrl;
}

function makeUplodform($acceptFileType,$maxFileSize)
{?><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>文件上传</title></head>
   <body topmargin="0" leftmargin="0"> 
   <table width=100% height=100% border=0 style="font-size:10pt">
   <form method="post" enctype="multipart/form-data" onsubmit="this.submitbtn.style.display='none';document.getElementById('loadingprogress').style.display='';this.action=window.location.search;">
   <tr><td><input type=hidden name='action' value='save'><b>请选择要上传的文件：</b></td></tr>
   <tr><td align="center"><input type="file" accept="<?php echo $acceptFileType;?>" name="uploadfile" size=35><input type=hidden name="MAX_FILE_SIZE" value="<?php echo $maxFileSize;?>"><input type="submit" name="submitbtn"  value="上传"><img src="../images/loading1.gif" style="display:none;" id="loadingprogress"></td></tr></form>
   <tr><td>	
   1. 点击“上传”后，请耐心等待，上传时间视文件大小和网络状况而定。<br>
   2. 为节省空间，请尽量优化上传文件的大小，建议单个文件不要超过<font color="#FF0000"><?php if($maxFileSize<1024)echo number_format($maxFileSize)."B"; else if($maxFileSize<1048576)echo number_format($maxFileSize/1024)."KB"; else echo number_format($maxFileSize/1048576)."MB";?></font>。<br>
   3. 传送大文件时，可能导致服务器变慢或者不稳定。建议使用FTP上传大文件。
   </td></tr></table>
   </body></html><?php
}


function  InitUpload($UploadStyles)
{ global $sType, $sCusDir, $sParamSYFlag,$sFilenamed;
  global $sAllowExt, $nAllowSize, $sUploadDir, $nUploadObject, $nAutoDir, $sBaseUrl, $sContentPath;
  global $sFileExt, $sOriginalFileName, $sSaveFileName, $sPathFileName, $nFileNum;
  global $nSLTFlag, $nSLTMinSize, $nSLTOkSize, $nSYWZFlag, $sSYText, $sSYFontColor, $nSYFontSize, $sSYFontName, $sSYPicPath, $nSLTSYObject, $sSLTSYExt, $nSYWZMinWidth, $sSYShadowColor, $nSYShadowOffset, $nSYWZMinHeight, $nSYWZPosition, $nSYWZTextWidth, $nSYWZTextHeight, $nSYWZPaddingH, $nSYWZPaddingV, $nSYTPFlag, $nSYTPMinWidth, $nSYTPMinHeight, $nSYTPPosition, $nSYTPPaddingH, $nSYTPPaddingV, $nSYTPImageWidth, $nSYTPImageHeight, $nSYTPOpacity, $nCusDirFlag;
 
  $sType = strtoupper(trim($_GET["type"]));
  $aStyleConfig = explode("|||", $UploadStyles);
  switch ($sType){
    case 'APP':   $cataDir="upgrade/";$sAllowExt=$aStyleConfig[44]; $nAllowSize = $aStyleConfig[45];$acceptFileType="*";break; //原-本地类型
    case 'FILE':  $cataDir="fs/"; $sAllowExt='*';$nAllowSize=1024*1024*5/*$aStyleConfig[11]*/;$acceptFileType="*";break;//原-其他类型（附件文档）
    case 'FLASH': $cataDir="flash/";  $sAllowExt=$aStyleConfig[7];  $nAllowSize = $aStyleConfig[12];$acceptFileType="video/*,audio/*";break;//原-FLASH动画类型
    case 'MEDIA': $cataDir="media/";  $sAllowExt=$aStyleConfig[9];  $nAllowSize = $aStyleConfig[14];break;//原-媒体类型（附件文档）
    case 'WARE':  $cataDir='ware/';  $sAllowExt=$aStyleConfig[8];  $nAllowSize = $aStyleConfig[13];$acceptFileType="image/*";break; //图片类型
    default:      $cataDir=$sType.'/';  $sAllowExt=$aStyleConfig[8];  $nAllowSize = $aStyleConfig[13];$acceptFileType="image/*";break; //图片类型
  }

  if(@$_POST["action"]!="save")
  { makeUplodform($acceptFileType,(int)$nAllowSize*1024);
	  exit;
  }
  
  $sFilenamed= trim(@$_GET["filenamed"]);
  $sParamSYFlag = trim(@$_GET["syflag"]);
  
  $sCusDir = trim(@$_GET["cusdir"]); //自动创建的分类子目录名
  $sCusDir = str_replace("\\", "/", $sCusDir);
  if ((substr($sCusDir,0,1)=="/") || (substr($sCusDir,0,1)==".") || (substr($sCusDir, -1)==".") || (strstr($sCusDir, "./")) || (strstr($sCusDir, "/.")) || (strstr($sCusDir, "//")))
  { $sCusDir = "";
  }
 
  $nUploadObject = (int)$aStyleConfig[20];
  $nAutoDir = (int)$aStyleConfig[21]; //是否允许自动创建日期子目录（包括日期类型）
  $sContentPath = $aStyleConfig[3];

  if (substr($sContentPath, 0, 1) == "/")
  { $sUploadDir = $_SERVER['DOCUMENT_ROOT'].$sContentPath;
  }
  else
  { $sUploadDir = realpath($sContentPath);
    $sContentPath = RelativePath2RootPath($sUploadDir);
  }
  if ((substr($sUploadDir,-1)!="\\") && (substr($sUploadDir,-1)!="/"))
  { $sUploadDir .= "/";
  }

  $sUploadDir.=$cataDir;
  $sContentPath.=$cataDir;

  $sAllowExt = strtoupper($sAllowExt);
  $nSLTFlag = (int)$aStyleConfig[29];
  $nSLTMinSize = (int)$aStyleConfig[30];
  $nSLTOkSize = (int)$aStyleConfig[31];
  $nSYWZFlag = (int)$aStyleConfig[32];
  $sSYText = $aStyleConfig[33];
  $sSYFontColor = $aStyleConfig[34];
  $nSYFontSize = (int)$aStyleConfig[35];
  $sSYFontName = $aStyleConfig[36];
  $sSYPicPath = $aStyleConfig[37];
  $nSLTSYObject = (int)$aStyleConfig[38];
  $sSLTSYExt = $aStyleConfig[39];
  $nSYWZMinWidth = (int)$aStyleConfig[40];
  $sSYShadowColor = $aStyleConfig[41];
  $nSYShadowOffset = (int)$aStyleConfig[42];
  $nSYWZMinHeight = (int)$aStyleConfig[46];
  $nSYWZPosition = (int)$aStyleConfig[47];
  $nSYWZTextWidth = (int)$aStyleConfig[48];
  $nSYWZTextHeight = (int)$aStyleConfig[49];
  $nSYWZPaddingH = (int)$aStyleConfig[50];
  $nSYWZPaddingV = (int)$aStyleConfig[51];
  $nSYTPFlag = (int)$aStyleConfig[52];
  $nSYTPMinWidth = (int)$aStyleConfig[53];
  $nSYTPMinHeight = (int)$aStyleConfig[54];
  $nSYTPPosition = (int)$aStyleConfig[55];
  $nSYTPPaddingH = (int)$aStyleConfig[56];
  $nSYTPPaddingV = (int)$aStyleConfig[57];
  $nSYTPImageWidth = (int)$aStyleConfig[58];
  $nSYTPImageHeight = (int)$aStyleConfig[59];
  $nSYTPOpacity = (float)$aStyleConfig[60];
  $nCusDirFlag = (int)$aStyleConfig[61];//允许创建自命令分类子目录
  if ($nSYWZFlag==2)$nSYWZFlag = ($sParamSYFlag == "1")?1:0;
  if ($nSYTPFlag==2)$nSYTPFlag = ($sParamSYFlag == "1")?1:0;
}
?>
