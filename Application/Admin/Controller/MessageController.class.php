<?php
namespace Admin\Controller;
use Admin\Common\AController;
class MessageController extends AController {
    private $db;
    private $type;
    private $title = '在线留言';

    public function _init () {
        //获取类名称
        $this->type = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($this->type . '/index').'">'. $this->title .'管理</a>';
        $this->db = D($this->type);
        $this->assign('type', $this->type);
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('Index/index');
    }

    /* 列表 */
    public function index(){

        $map = array();
        $list = $this->lists($this->type, $map, 'id desc');
        $this->assign('list', $list);
        $this->meta_title = $this->title . '列表';
        $this->display();
    }

    /* 修改 */
    public function show($id = 0) {
        $info = $this->db->find($id);
        if (!$info) {
            $this->error('不存在！');
        } else {
            $info['extend'] = unserialize($info['extend']);
            $this->assign('info', $info);
        }
        $this->meta_title = '更新' . $this->title;
        $this->display();
    }

    /*  删除 */
    public function del() {
        $id = array_unique((array)I('id',0));
        //print_r($id); exit;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if($this->db->where($map)->delete()){
            action_log();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
