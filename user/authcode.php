<?php
        session_start ();
        header ( 'Content-type: image/png' );
        //创建图片
        $im = imagecreate($x=64,$y=20 );
        if(1){
          $bg = imagecolorallocatealpha($im , 0 , 0 , 0 , 127);
          $fontColor = imageColorAllocate ( $im,rand(50,200),rand(0,155),rand(0,155) );   //字体颜色
        }
        else{
          $bg = imagecolorallocate($im,rand(50,200),rand(0,155),rand(0,155)); //第一次对 imagecolorallocate() 的调用会给基于调色板的图像填充背景色
          $fontColor = imageColorAllocate ( $im, 255, 255, 255 );   //字体颜色
        }

        $fontstyle = 'times.ttf';                   //字体样式，这个可以从c:\windows\Fonts\文件夹下找到，我把它放到和authcode.php文件同一个目录，这里可以替换其他的字体样式
        $authcode="";
        $fontsize=16;
        //产生随机字符
        for($i = 0; $i < 4; $i ++) {
                $randAsciiNumArray         = array (rand(48,57),rand(65,90));
                $randAsciiNum                 = $randAsciiNumArray [rand ( 0, 1 )];
                $randStr                         = chr ( $randAsciiNum );
                imagettftext($im,$fontsize,rand(0,20)-rand(0,25),0+$i*($fontsize+0),16,-$fontColor,$fontstyle,$randStr);
                $authcode                        .= $randStr; 
        }
        $_SESSION['VerifyCode']  = $authcode;//用户和用户输入的验证码做比较
        
       /* 
        //干扰线
        for ($i=0;$i<8;$i++){
                $lineColor        = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
                imageline ($im,rand(0,$x),0,rand(0,$x),$y,$lineColor);
        }
        //干扰点
        for ($i=0;$i<250;$i++){
                imagesetpixel($im,rand(0,$x),rand(0,$y),$fontColor);
        }
       */
        
        imagepng($im);
        imagedestroy($im);                
?>
