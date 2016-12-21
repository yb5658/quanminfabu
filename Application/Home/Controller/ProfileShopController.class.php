<?php
namespace Home\Controller;
use Home\Common\IController;
class ProfileShopController extends IController {
    protected $order = 'sort desc, id desc'; //统一排序-李震
    /* 个人商店 */
    public function index(){
        $user_id = cookie('user_id');
        if (!$user_id) {
            $this->redirect('Index/index');
        }
        $meta_title = C('WEB_SITE_TITLE');
        $this->assign('meta_title', $meta_title);
        $this->assign('beijing', 1);
        $this->display();
    }

    /* 注册个人商店 */
    public function register(){
        $user_id = cookie('user_id');
        if (!$user_id) {
            $this->redirect('Index/index');
        }
        $meta_title = C('WEB_SITE_TITLE');
        $this->assign('meta_title', $meta_title.'-个人店面注册');
        $this->assign('beijing', 1);
        $this->display();
    }


}
