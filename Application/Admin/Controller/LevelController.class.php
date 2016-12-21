<?php
namespace Admin\Controller;
use Admin\Common\AController;
class LevelController extends AController {
    private $db;
    private $type;
    private $title = '等级';
    private $configs = array(
        'allow_create_time' => 0,   //发布时间-李震
    );

    private $fields = array(
        'title' => array(
            'primary' => 1,
            'type' => 'text',
            'title' => '等级名称',
            'desc' => '',
        ),
        'short_title' => array(
            'primary' => 1,
            'type' => 'text',
            'title' => '短标题',
            'desc' => '',
        ),
        'price' => array(
            'primary' => 1,
            'type' => 'text',
            'title' => '价格',
            'desc' => '',
        ),
        'counts' => array(
            'primary' => 1,
            'type' => 'text',
            'title' => '分享次数',
            'desc' => '',
        ),
        'description' => array(
            'primary' => 1,
            'type' => 'textarea',
            'title' => '描述',
            'desc' => '',
        ),
    );

    public function _init () {
        //获取类名称
        $this->type = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($this->type . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('Level');
        $this->assign('type', $this->type);
        $this->assign('configs', $this->configs);
        $this->assign('fields', $this->fields);
    }

    /* 列表 */
    public function index(){
        $list = $this->lists('Level', '', 'id desc');
        $this->assign('list', $list);
        $this->meta_title = $this->title . '列表';
        $this->display('Level/index');
    }

    /* 新增 */
    public function add($catid = 0) {
        if (IS_POST) {
            if (!$this->db->input()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功', U('index'));
            }
        } else {
            $this->meta_title = '新增' . $this->title;
            $this->display('Level/edit');
        }
    }

    /* 修改 */
    public function edit($catid = 0) {
        if (IS_POST) {
            if (!$this->db->update()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('更新成功', U('index'));
            }
        } else {
            $id = I('id',0,'intval');
            $info = $this->db->getById($id);
            if (!$info) {
                $this->error('不存在！');
            }
            $this->assign('info', $info);
            $this->meta_title = '更新' . $this->title;
            $this->display('Level/edit');
        }
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

    /* 更新缓存 */
    protected function updateCache() {

    }
}
