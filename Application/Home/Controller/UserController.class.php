<?php
namespace Home\Controller;
use Home\Common\IController;
class UserController extends IController {
    protected $order = 'sort desc, id desc'; //统一排序-李震
    /* 会员首页 */
    public function index(){
        $user_id = cookie('user_id');
        if (!$user_id) {
            $this->redirect('Index/index');
        }
        $meta_title = C('WEB_SITE_TITLE');
        $this->assign('meta_title', $meta_title.'-会员中心');
        $this->assign('beijing', 1);
        $this->display();
    }

    /* 用户信息 */
    public function profile() {
          $user_id = cookie('user_id');
          if (!$user_id) {
              $this->redirect('Index/index');
          }
          $region = M('Region')->where(array('parent_id'=>1))->getField('region_id,region_name');
          $this->assign('region',$region);
          $this->assign('sex',array(0=>'未知',1=>'男',2=>'女'));
          $level = M('Level')->select();
          $this->assign('level',$level);
          $meta_title = C('WEB_SITE_TITLE');
          $this->assign('meta_title', $meta_title.'-个人信息管理');
          $this->assign('beijing', 1);
          $this->display();
    }
    /* 修改用户*/
    public function editProfile() {
          $user_id = cookie('user_id');
          if (!$user_id) {
              $this->redirect('Index/index');
          }
          if(IS_POST){
               $data = $_POST;
               $data['update_time'] = NOW_TIME;
               $ret = M('User')->where(array('user_id'=>$user_id))->save($data);
               if($ret){
                   $this->success('修改成功',U('User/profile'));
               }else{
                   $this->error('修改失败');
               }
          }else{
            $field = I('get.field');
            if($field == 'province'){
              $region = M('Region')->where(array('parent_id'=>1))->getField('region_id,region_name');
              $this->assign('region',$region);
            }
            $this->assign('field',I('get.field'));
            $this->assign('beijing', 1);
            $this->display();
          }
    }
    /* 申请开店*/
    public function applyShop() {
          $user_id = cookie('user_id');
          if (!$user_id) {
              $this->redirect('Index/index');
          }


          $this->assign('field',I('get.field'));
          $this->assign('beijing', 1);
          $this->display();
    }
    /* 在线留言 */
    public function message() {
        if (IS_POST) {
            $model = D('Message');
            if ($model->input()) {
                $this->success('留言成功');
            } else {
                $this->error($model->getError());
            }
        } else {
            $this->display();
        }
    }


}
