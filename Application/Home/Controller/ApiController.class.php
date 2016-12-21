<?php
namespace Home\Controller;
use Home\Common\IController;
use Vendor\TPWechat;
class ApiController extends IController {
    protected function _init() {
        $this->options = array(
            'token' => C('TOKEN'), //填写你设定的key
            'encodingaeskey' => '', //填写加密用的EncodingAESKey
            'appid' => C('APPID'), //填写高级调用功能的app id
            'appsecret' => C('APPSECRET') //填写高级调用功能的密钥
        );
        //echo '<pre>'; print_r($this->options); exit;
        $this->WX = new TPWechat($this->options);
    }

    public function zdy(){
        //设置菜单
        $newmenu =  array(
            "button"=>array(
                array(
                    'name'=>'扫码',
                    'sub_button' => array(
                        array('type'=>'scancode_waitmsg','name'=>'扫码带提示','key'=>'rselfmenu_0_0'),
                        array('type'=>'scancode_push','name'=>'扫码推事件','key'=>'rselfmenu_0_1'),
                        array('type'=>'view','name'=>'百度一下','url'=>'http://www.baidu.com/')
                    )
                ),
                array(
                    'name'=>'发图',
                    'sub_button'=>array(
                        array('type'=>'pic_sysphoto','name'=>'系统拍照发图','key'=>'rselfmenu_1_0'),
                        array('type'=>'pic_photo_or_album','name'=>'拍照或者相册发图','key'=>'rselfmenu_1_1'),
                        array('type'=>'pic_weixin','name'=>'微信相册发图','key'=>'rselfmenu_1_2')
                    )
                ),
                array(
                    'type'=>'location_select',
                    'name'=>'发送位置',
                    'key'=>'rselfmenu_2_0'
                )
            )
        );
        // echo '<pre>'; print_r($newmenu); exit;
        $result = $this->WX->createMenu($newmenu);
        if ($result) {
            $this->show('成功');
        } else {
            $this->show('失败');
        }
    }

     /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     * 在mp.weixin.qq.com 开发者中心配置的 URL(服务器地址)  http://域名/index.php/home/weixin/index/id/member_public表的id.html
     */
	public function index() {
        $this->WX->valid();
        $type = $this->WX->getRev()->getRevType();
        if (C('DEVELOP_MODE')) {
            addWeixinLog($GLOBALS ['HTTP_RAW_POST_DATA']);
        }
        //与微信交互的中控服务器逻辑可以自己定义，这里实现一个通用的
        switch ($type) {
            //事件
            case TPWechat::MSGTYPE_EVENT:         //先处理事件型消息
                $event = $this->WX->getRevEvent();
                switch ($event['event']) {
                    //关注
                    case TPWechat::EVENT_SUBSCRIBE:
                        //二维码关注
                        if(isset($event['eventkey']) && isset($event['ticket'])){

                        }else{
                            //普通关注
                        }
                        $openid = $this->WX->getRevFrom();
                        $Fans = M('Fans');
                        $fans_info = $Fans->where(array('openid'=>$openid))->find();
                        if ($fans_info) {
                            $map = array(
                                'openid' => $this->WX->getRevFrom()
                            );
                            $Fans->where($map)->setField('subscribe', 1);
                        } else {
                            $wx_info = $this->WX->getUserInfo($this->WX->getRevFrom());
                            $data = array(
                                'openid' => $openid,
                                'username' => $wx_info['nickname'],
                                'avatar' => $wx_info['headimgurl'],
                                'subscribe' => 1,
                                'ctime' => NOW_TIME
                            );
                            $Fans->add($data);
                        }
                        $this->WX->text('你好，欢迎关注宜可姿美胸美体专业机构。并推荐10个好友关注此公众号，即可获得免费乳房专业检测一次，美胸体验项目一次等。地址：平安北大街阿尔卡迪亚小区底商宜可姿美胸美体专业机构，电话：89681919')->reply();

                        break;

                    //扫描二维码
                    case TPWechat::EVENT_SCAN:

                        break;
                    //地理位置
                    case TPWechat::EVENT_LOCATION:

                        break;
                    //自定义菜单 - 点击菜单拉取消息时的事件推送
                    case TPWechat::EVENT_MENU_CLICK:
                        switch ($event['key']) {
                            case 'MENU_KEY_NEWS':

                                break;

                            default:
                                # code...
                                break;
                        }

                        break;
                    //自定义菜单 - 点击菜单跳转链接时的事件推送
                    case TPWechat::EVENT_MENU_VIEW:
                        $this->WX->text('//自定义菜单 - 点击菜单跳转链接时的事件推送')->reply();
                        break;
                    //自定义菜单 - 扫码推事件的事件推送
                    case TPWechat::EVENT_MENU_SCAN_PUSH:

                        break;
                    //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
                    case TPWechat::EVENT_MENU_SCAN_WAITMSG:

                        break;
                    //自定义菜单 - 弹出系统拍照发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_SYS:

                        break;
                    //自定义菜单 - 弹出拍照或者相册发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_PHOTO:

                        break;
                    //自定义菜单 - 弹出微信相册发图器的事件推送
                    case TPWechat::EVENT_MENU_PIC_WEIXIN:

                        break;
                    //自定义菜单 - 弹出地理位置选择器的事件推送
                    case TPWechat::EVENT_MENU_LOCATION:

                        break;
                    //取消关注
                    case TPWechat::EVENT_UNSUBSCRIBE:
                        $openid = $this->WX->getRevFrom();
                        $Fans = M('Fans');
                        $fans_info = $Fans->where(array('openid'=>$openid))->find();
                        if ($fans_info) {
                            $map = array(
                                'openid' => $this->WX->getRevFrom()
                            );
                            $Fans->where($map)->setField('subscribe', 0);
                        } else {
                            $wx_info = $this->WX->getUserInfo($this->WX->getRevFrom());
                            $data = array(
                                'openid' => $openid,
                                'username' => $wx_info['nickname'],
                                'avatar' => $wx_info['headimgurl'],
                                'subscribe' => 0,
                                'ctime' => NOW_TIME
                            );
                            $Fans->add($data);
                        }
                        break;
                    //群发接口完成后推送的结果
                    case TPWechat::EVENT_SEND_MASS:

                        break;
                    //模板消息完成后推送的结果
                    case TPWechat::EVENT_SEND_TEMPLATE:

                        break;
                    default:

                        break;
                }
                break;
            //文本
            case TPWechat::MSGTYPE_TEXT :
                $keyword = trim($this->WX->getRevContent());

                switch ($keyword) {
                    case '1':
                        $this->WX->text('0你好，欢迎关注宜可姿美胸美体专业机构。并推荐10个好友关注此公众号，即可获得免费乳房专业检测一次，美胸体验项目一次等。地址：平安北大街阿尔卡迪亚小区底商宜可姿美胸美体专业机构，电话：89681919')->reply();
                        break;

                    case '2':
                        $data = array(
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description1',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/3_G_1440030087739.jpg',
                                'Url' => 'http://wwb.sypole.com/'
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description2',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description3',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            ),
                            array(
                                'Title' => '最新新闻',
                                'Description' => '测试description4',
                                'PicUrl' => 'http://shop.zlsgx.com/images/201508/goods_img/5_G_1440028482068.jpg',
                                'Url' => ''
                            )
                        );
                        $this->WX->news($data)->reply();
                        break;
                    case '投票':
                        $this->WX->text("http://ykzmx.hbjsfx.com/index.php?m=Vote&c=Index&a=index&vid=1")->reply();
                        break;
                    default:
                        $this->WX->text("默认信息...")->reply();
                        break;
                }
                break;
            //图像
            case TPWechat::MSGTYPE_IMAGE :

                break;
            //语音
            case TPWechat::MSGTYPE_VOICE :

                break;
            //视频
            case TPWechat::MSGTYPE_VIDEO :

                break;
            //位置
            case TPWechat::MSGTYPE_LOCATION :

                break;
            //链接
            case TPWechat::MSGTYPE_LINK :

                break;
            default:

                break;
        }
	}

    // 销毁
    public function _destructor(){

    }
}
