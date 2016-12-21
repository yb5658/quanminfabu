<?php
/**
 *    共用函数库  2015年6月29日 16:15:03
 */

const CMS_VERSION    = '0.2_20150916';

 /**
  * 通用图片获取方法
  * @param  string $img 图片
  * @param  string $width 截图宽
  * @param  string $height 截图高
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
function image($img = '', $widht = 0, $height = 0) {
    if (!empty($img)) {
        $file =  'Uploads/' . $img;
        if (file_exists($file)) {
            return __ROOT__ .'/' . $file;
        } else {
            return __ROOT__ .'/Uploads/default.jpg';
        }
    } else {
        return __ROOT__ .'/Uploads/default.jpg';
    }
}

 /**
  * 系统用户MD5+sha1+key加密方法
  * @param  string $str 要加密的字符串
  * @param  string $key 加密key
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function password_md5($str, $key = 'bjjfsd123456'){
 	return '' === $str ? '' : md5(sha1($str) . $key);
 }

 /**
  * 字符串截取，支持中文和其他编码
  * @static
  * @access public
  * @param string $str 需要转换的字符串
  * @param string $start 开始位置
  * @param string $length 截取长度
  * @param string $charset 编码格式
  * @param string $suffix 截断显示字符
  * @return string
  */
 function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
     if(function_exists("mb_substr"))
         $slice = mb_substr($str, $start, $length, $charset);
     elseif(function_exists('iconv_substr')) {
         $slice = iconv_substr($str,$start,$length,$charset);
         if(false === $slice) {
             $slice = '';
         }
     }else{
         $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
         $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
         $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
         $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
         preg_match_all($re[$charset], $str, $match);
         $slice = join("",array_slice($match[0], $start, $length));
     }
     return $suffix ? $slice.'...' : $slice;
 }

 /**
  * 系统加密方法
  * @param string $data 要加密的字符串
  * @param string $key  加密密钥
  * @param int $expire  过期时间 单位 秒
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function encrypt($data, $key = '', $expire = 0) {
     $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
     $data = base64_encode($data);
     $x    = 0;
     $len  = strlen($data);
     $l    = strlen($key);
     $char = '';

     for ($i = 0; $i < $len; $i++) {
         if ($x == $l) $x = 0;
         $char .= substr($key, $x, 1);
         $x++;
     }

     $str = sprintf('%010d', $expire ? $expire + time():0);

     for ($i = 0; $i < $len; $i++) {
         $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
     }
     return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
 }

 /**
  * 系统解密方法
  * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
  * @param  string $key  加密密钥
  * @return string
  * @author 拥抱 <572300808@qq.com>
  */
 function decrypt($data, $key = ''){
     $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
     $data   = str_replace(array('-','_'),array('+','/'),$data);
     $mod4   = strlen($data) % 4;
     if ($mod4) {
        $data .= substr('====', $mod4);
     }
     $data   = base64_decode($data);
     $expire = substr($data,0,10);
     $data   = substr($data,10);

     if($expire > 0 && $expire < time()) {
         return '';
     }
     $x      = 0;
     $len    = strlen($data);
     $l      = strlen($key);
     $char   = $str = '';

     for ($i = 0; $i < $len; $i++) {
         if ($x == $l) $x = 0;
         $char .= substr($key, $x, 1);
         $x++;
     }

     for ($i = 0; $i < $len; $i++) {
         if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
             $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
         }else{
             $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
         }
     }
     return base64_decode($str);
 }

 /**
  * 检测输入的验证码是否正确
  * @param  string  $code 为用户输入的验证码字符串
  * @return bool
  * @author 拥抱 <572300808@qq.com>
  */
 function check_verify($code, $id = ''){
     $verify = new \Think\Verify();
     return $verify->check($code, $id);
 }
