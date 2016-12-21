<?php
namespace Admin\Controller;
use Admin\Common\AController;
class News4Controller extends AController {
    private $db;
    private $type;
    private $title = '招聘信息';
    private $configs = array(
        'allow_create_time' => 1,   //发布时间
        'allow_position'    => 0,   //推荐位
    );

    private $fields = array(
        'title' => array(
            'primary' => 1,
            'type' => 'text',
            'title' => '标题',
            'desc' => '招聘职位',
        ),
        'zhize' => array(
            'primary' => 0,
            'type' => 'textarea',
            'title' => '岗位职责',
            'desc' => '岗位职责',
        ),
        'zige' => array(
            'primary' => 0,
            'type' => 'textarea',
            'title' => '任职资格',
            'desc' => '任职资格',
        ),
    );

    public function _init () {
        //获取类名称
        $this->type = str_replace('Controller', '', substr(strrchr(__CLASS__, '\\'), 1));
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U($this->type . '/index').'">'. $this->title .'管理</a>';
        $this->db = D('News');
        $this->assign('type', $this->type);
        $this->assign('configs', $this->configs);
        $this->assign('fields', $this->fields);
    }

    /* 列表 */
    public function index(){
        $map = $search = array(
            'status' => 1,
            'type' => $this->type,
        );
        $catid = I('catid', 0, 'intval');
        if (!empty($catid)) {
            $map['catid'] = $catid;
            $search['catid'] = $catid;
        }
        $title = I('title');
        if (!empty($title)) {
            $map['title'] = array('like', '%'. $title .'%');
            $search['title'] = $title;
        }
        $list = $this->lists('News', $map, 'sort desc, id desc');
        $this->assign('list', $list);
        if ($this->configs['allow_position']) {
            $fields = M()->query('SHOW FULL COLUMNS FROM __NEWS__');
            $positions = array();
            foreach ($fields as $key => $value) {
                if (strpos($value['field'], 'position_') !== false) {
                    $positions[$value['field']] = $value['comment'];
                }
            }
            unset($fields);
            $this->assign('positions', $positions);
        }
        $this->assign('category', D('category')->getAll());
        $this->assign('cate_list', D('category')->formatTree());
        $this->assign('search', $search);
        $this->meta_title = $this->title . '列表';
        $this->display('News/index');
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
            $cate_list = D('category')->formatTree();
            foreach ($cate_list as $key => $value) {
                if ($value['id'] == $catid) {
                    $this->assign('cate_info', $value);
                    break;
                } else {
                    continue;
                }
            }
            $this->assign('cate_list', $cate_list);

            $this->assign('info', array('type'=>$this->type,'catid'=>I('catid')));
            $this->meta_title = '新增' . $this->title;
            $this->display('News/edit');
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
            $cate_list = D('category')->formatTree();
            foreach ($cate_list as $key => $value) {
                if ($value['id'] == $info['catid']) {
                    $this->assign('cate_info', $value);
                    break;
                } else {
                    continue;
                }
            }
            $this->assign('cate_list', $cate_list);
            $this->meta_title = '更新' . $this->title;
            $this->display('News/edit');
        }
    }
    /* 批量推荐 */
    public function position($id = 0, $position = '', $status = 0) {
        if (empty($id) || empty($position)) {
            $this->error('参数错误');
        }
        if($this->db->where(array('id'=>$id))->setField($position, $status)){
            action_log();
            $this->success('成功');
        } else {
            $this->error('失败！');
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
