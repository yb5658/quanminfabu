<?php
namespace Admin\Controller;
use Admin\Common\AController;
class CategoryController extends AController {
    private $db;

    public function _init () {
        $this->assign('__ACT__', strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/index'));
        $this->meta_head = '<a href="'.U('Category/index').'">栏目管理</a>';
        $this->db = D('category');
    }

    public function _empty(){
        $this->meta_title = '空操作';
        $this->display('index');
    }

    /* 列表 */
    public function index(){
        $this->assign('cateType', C('CATEGORY_TYPE'));
        $this->assign('list', $this->db->formatTree());
        $this->meta_title = '栏目列表';
        $this->display();
    }

    /* 新增 */
    public function add() {
        if (IS_POST) {
            if (!$this->db->input()) {
                $this->error($this->db->getError());
            } else {
                action_log();
                $this->updateCache();
                $this->success('新增成功', U('index'));
            }
        } else {
            $this->assign('info', array('pid'=>I('pid')));
            $this->assign('list', $this->db->formatTree());
            $this->assign('cateType', C('CATEGORY_TYPE'));
            $this->meta_title = '新增栏目';
            $this->display("edit");
        }
    }

    /* 修改 */
    public function edit() {
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
            $info = $this->db->find($id);
            if (!$info) {
                $this->error('不存在！');
            } else {
                $info['setting'] = unserialize($info['setting']);
                $this->assign('info', $info);
            }
            if ($info['type'] != 'Pages') {
                $count = D('News')->where(array('catid'=>$info['id']))->count();
            }
            $this->assign('template', $this->_getTemplate());
            $this->assign('count', $count);
            $this->assign('list', $this->db->formatTree());
            $this->assign('cateType', C('CATEGORY_TYPE'));
            $this->meta_title = '更新栏目';
            $this->display();
        }
    }

    /*  删除 */
    public function del() {
        $id = array_unique((array)I('id',0));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id) );
        if($this->db->where($map)->delete()){
            action_log();
            $this->updateCache();
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    /* 获取模版信息 */
    private function _getTemplate() {
        $pattern =  APP_PATH .'Home/View/Index/*.html';
        $files = array();
        foreach (glob($pattern) as $key => $value) {
            $filename = pathinfo($value);

            switch (substr($filename['basename'], 0, 4)) {
                case 'inde':
                    $pos = array('index','首页');
                    break;
                case 'list':
                    $pos = array('lists','列表页');
                    break;
                case 'show':
                    $pos = array('show','内容页');
                    break;
                case 'page':
                    $pos = array('pages','单页');
                    break;
                case 'sear':
                    $pos = array('search','搜索页');
                    break;
                case 'colu':
                    $pos = array('column','栏目页');
                    break;

                default:
                    $pos = array('wdy','未知页');
                    break;
            }
            $files[$pos[0]][$key]['basename'] = $filename['basename'];
            $files[$pos[0]][$key]['filename'] = $filename['filename'];
            $files[$pos[0]][$key]['path'] = $value;
            $handle = @fopen($value, "r");
            if ($handle) {
                $buffer = fgets($handle);
                preg_match('/<!--\s?(.*?)\s?-->/', $buffer, $res);
                if ($res) {
                    $files[$pos[0]][$key]['title'] = $res[1];
                } else {
                    $files[$pos[0]][$key]['title'] = $pos[1];
                }
                fclose($handle);
            }
        }
        return $files;
    }
    /* 更新缓存 */
    protected function updateCache() {
        S('DB_CATE_FORMAT', null);
        S('CATEGORYS', null);
        S('NAVS', null);
    }
}
