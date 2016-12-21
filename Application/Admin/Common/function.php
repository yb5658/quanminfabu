<?php
/**
 *    后台共用函数库  2015年6月29日 16:15:03
 */

 /**
  * 获取配置的类型
  * @param string $type 配置类型
  * @return string
  */
 function get_config_type($type=0){
     $list = C('CONFIG_TYPE_LIST');
     return $list[$type];
 }

 /**
  * 获取配置的分组
  * @param string $group 配置分组
  * @return string
  */
 function get_config_group($group=0){
     $list = C('CONFIG_GROUP_LIST');
     return $group ? $list[$group] : '';
 }

 /**
  * 获取数据类型的分组
  * @param string $group 类型
  * @return string
  */
 function get_category_type($type=''){
     $list = C('CATEGORY_TYPE');
     return $type ? $list[$type] : '';
 }

 /**
  * 获取新闻图片尺寸
  * @param string $field 属性
  * @return string
  */
 function get_news_px($field = ''){
     $list = C('NEWS_IMAGE_PX');
     return $field ? $list[$field] : '';
 }

 /**
  * 获取产品图片尺寸
  * @param string $field 属性
  * @return string
  */
 function get_product_px($field = ''){
     $list = C('PRODUCT_IMAGE_PX');
     return $field ? $list[$field] : '';
 }

 /**
  * 获取广告类型
  * @param string $field 属性
  * @return string
  */
 function get_banner_type($field = ''){
     $list = C('BANNER_TYPE');
     return $field ? $list[$field] : '';
 }

 //枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 行为记录
 * @param string
 * @return string
 */
function action_log(){
    $log  = UID . ':' . strtolower(CONTROLLER_NAME.'/'.ACTION_NAME) . PHP_EOL;
    file_put_contents('./log.txt', $log, FILE_APPEND);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 拥抱 <572300808@qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 拥抱 <572300808@qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 获取登录用户名
 * @return string 用户名
 * @author 拥抱 <572300808@qq.com>
 */
function get_username() {
    $user = session('user_auth');
    if (is_array($user)) {
        return $user['username'];
    } else {
        return '';
    }
}


/**
 * 删除目录和文件
 * @author 拥抱 <572300808@qq.com>
 */
function delete_dir($path = '') {
    if(is_dir($path)) {
        $file_list= scandir($path);
        foreach ($file_list as $file) {
            if( $file!='.' && $file!='..') {
                delete_dir($path.'/'.$file);
            }
        }
        rmdir($path);
    } else {
        unlink($path);
    }
}
