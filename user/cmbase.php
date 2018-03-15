<?php

#将数字转换为IP
function ntoip($n){ 
  $iphex=dechex($n);//将10进制数字转换成16进制
  $len=strlen($iphex);//得到16进制字符串的长度
  if(strlen($iphex)<8){
    $iphex='0'.$iphex;//如果长度小于8，在最前面加0
    $len=strlen($iphex); //重新得到16进制字符串的长度
  }
  //这是因为ipton函数得到的16进制字符串，如果第一位为0，在转换成数字后，是不会显示的
  //所以，如果长度小于8，肯定要把第一位的0加上去
  //为什么一定是第一位的0呢，因为在ipton函数中，后面各段加的'0'都在中间，转换成数字后，不会消失
  for($i=0,$j=0;$j<$len;$i=$i+1,$j=$j+2){//循环截取16进制字符串，每次截取2个长度
    $ippart=substr($iphex,$j,2);//得到每段IP所对应的16进制数
    $fipart=substr($ippart,0,1);//截取16进制数的第一位
    if($fipart=='0'){//如果第一位为0，说明原数只有1位
      $ippart=substr($ippart,1,1);//将0截取掉
    }
    $ip[]=hexdec($ippart);//将每段16进制数转换成对应的10进制数，即IP各段的值
  }
  $ip = array_reverse($ip);
  return implode('.', $ip);//连接各段，返回原IP值
}

function ipton($ip){
  if(empty($ip))return 0;
  $cip=explode('.',$ip);
  if($cip && count($cip)==4)
  { return $cip[0]+($cip[1]<<8)+($cip[2]<<16)+($cip[3]<<24);
  }
}

function GetIP() {
  if(($cip=$_SERVER["HTTP_CLIENT_IP"])) return $cip;
  else if(($cip=$_SERVER["HTTP_X_FORWARDED_FOR"])) return $cip;
  else if(($cip=$_SERVER["REMOTE_ADDR"])) return $cip;
  else return NULL;
}

function _httpParseHeader($header){
  sscanf($header,'HTTP/%*f %d',$code);
  if($code=='200'){
    $offset=strpos($header,"\r\n\r\n");
    if($offset>0)return substr($header,$offset+4);
  }
}

function http_get($url){
  $info = parse_url($url);
  $fp = fsockopen($info['host'], 80, $errno, $errstr, 3);
  $head = 'GET '.$info['path'].'?'.$info['query']." HTTP/1.0\r\nHost: ";
  $head.= $info['host']."\r\n\r\n";
  if(fputs($fp, $head)){
    while (!feof($fp)){
      $ret .= fread($fp,4096);
    }
  }
  return _httpParseHeader($ret);
}

function http_post($url, $query){       
  $info = parse_url($url);
  $fp = fsockopen($info['host'], 80, $errno, $errstr, 3);
  $head = 'POST '.$info['path']."?".$info['query']." HTTP/1.0\r\n";
  $head.= 'Host: '.$info['host']."\r\n";
  $head.= 'Referer: http://'.$info['host'].$info['path']."\r\n";
  $head.= "Content-type: application/x-www-form-urlencoded\r\n";
  $head.= "Content-Length: ".strlen(trim($query))."\r\n\r\n";
  $head.= $query;
  if(fputs($fp, $head)){
    while (!feof($fp)){
      $ret .= fread($fp,4096);
    }
  }
  return _httpParseHeader($ret);
}


function get_location_base(){
  $url=$_SERVER['REQUEST_URI'];
  $url=substr($url,0,1+strrpos($url,'/'));
  $url='http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$url;
  return $url;
}

function document_realpath($name)
{ if(substr($name,0,1)=='/') return $_SERVER['DOCUMENT_ROOT'].$name;
  else return getcwd().'/'.$name;
}

function document_download($url,$savefile){
  $content=file_get_contents($url);
  return ($content && file_put_contents(document_realpath($savefile),$content));     
}


?>


