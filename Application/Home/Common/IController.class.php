<?php
namespace Home\Common;
use Vendor\TPWechat;
class IController extends \Think\Controller {
     private $options = array();
     public  $WX = null;
     public  $user_info = array();
    public function _initialize () {
        //批量添加配置
        $config = S('DB_CONFIG_DATA');
        if(!$config){
            $config = D('Admin/Config')->lists();
            S('DB_CONFIG_DATA', $config);
        }
        
        C($config);
        // 执行类初始化方法最好不要用__construct
        /************************微信配置信息**************************************/
       $this->options = array(
           'token' => C('TOKEN'), //填写你设定的key
           'encodingaeskey' => '', //填写加密用的EncodingAESKey
           'appid' => C('APPID'), //填写高级调用功能的app id
           'appsecret' => C('APPSECRET') //填写高级调用功能的密钥
       );
       $this->WX = new TPWechat($this->options);
       // 执行类初始化方法最好不要用__construct
        $this->_init();
    }

    protected function _init() {
        //$user_id = cookie('user_id');
        $user_id = 1;
        if ($user_id) {
            cookie('user_id', $user_id);
            $user_info = M('User')->find($user_id);
            if (!$user_info) {
                cookie('user_id', null);
 			          redirect(C('WEB_SITE_URL'));
            }
        } else {
            if (empty($_GET['code'])) {
                $callback = C('WEB_SITE_URL') . __SELF__;
                $url = $this->WX->getOauthRedirect($callback, '123456', 'snsapi_userinfo');
                redirect($url);
            }
            $access_data = $this->WX->getOauthAccessToken();
            $wx_info = $this->WX->getOauthUserinfo($access_data['access_token'], $access_data['openid']);
            if (empty($wx_info['openid'])) {
 			         redirect(C('WEB_SITE_URL'));
            }
            $user_info = M('User')->where(array('openid' => $wx_info['openid']))->find();
            if (!$user_info) {
                $wx_info['nickname'] = remove_emoji($wx_info['nickname']);
                $user_info = array(
                    'openid' => $wx_info['openid'],
                    'nickname' => $wx_info['nickname'],
                    'avatar' => $wx_info['headimgurl'],
                    'sex' => $wx_info['sex'],
                    'province' => $wx_info['province'],
                    'city' => $wx_info['city'],
                    'create_time' => NOW_TIME,
                    'update_time' => NOW_TIME,
                );

                $user_info['user_id'] = M('User')->add($user_info);
            }
            cookie('user_id', $user_info['user_id']);
        }
        $this->user_info = $user_info;
        $this->assign('user_info', $user_info);
        // 微信JSJDK
        /*echo '<pre>';
         print_r($this->WX->getJsSign('http://wwb.sypole.com' . __SELF__)); exit('</pre>');*/
        $this->assign('wx_config', $this->WX->getJsSign(C('WEB_SITE_URL') . __SELF__));

     }

    /**
     * 通用分页列表数据集获取方法
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @return array|false
     * 返回数据集
     */
    protected function getAll($model, $where = array(), $order = '', $page_num = 0){
        $options    =   array();
        $param    =   (array)I('get.');
        if(is_string($model)){
            $model  =   M($model);
        }
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($param['_order']) && isset($param['_field']) && in_array(strtolower($param['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$param['_field'].'` '.$param['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($param['_order'],$param['_field']);
        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
        if( isset($param['r']) ){
            $listRows = (int)$param['r'];
        }else{
            if (empty($page_num)) {
                $listRows = C('SHOW_LIST_ROWS') > 0 ? C('SHOW_LIST_ROWS') : 10;
            } else {
                $listRows = $page_num;
            }

        }
        $page = new \Think\MyPage($total, $listRows, $param);
        if($total>$listRows){
            $page->setConfig('theme',' %UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% ');
            $page->rollPage = 10;
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }

}
