<?php
namespace Home\Controller;
use Home\Common\IController;
class IndexController extends IController {
    protected $NAVS= array();   // 一级导航
    protected $CATEGORYS = array(); //所有栏目信息

    protected $order = 'sort desc, id desc'; //统一排序

    public $childs = array();

    public function _getChilds($catid, $level = 0) {
        if ($level == 0) {
            $this->childs[] = $catid;
        }
        if ($level < 3) {
            ++$level;
            foreach ($this->CATEGORYS as $key => $value) {
                if ($value['pid'] == $catid) {
                    $this->childs[] = $key;
                    if ($value['pid']) {

                        $this->_getChilds($key, $level);
                    }
                }
            }
        }
        return $this->childs;
    }

    public function _init() {
        /* 所有栏目信息 */
        $this->CATEGORYS = D('Category')->getAll();
        $this->assign('CATEGORYS', $this->CATEGORYS);
        if (ACTION_NAME == 'show') {
            $id = I('id', 0, 'intval');
            if (empty($id)) {
                $this->redirect('index');
            }
            $catid = M('News')->where(array('id'=>$id))->getField('catid');
            if (empty($catid)) {
                $this->redirect('index');
            }
        } else {
            $catid = I('catid', 0, 'intval');
        }
        /* 一级导航 */
        foreach ($this->CATEGORYS as $key => $val) {
            if ($val['pid'] == 0 && $val['display']) {
                $this->NAVS[$key] = $val;
                // 目前延伸7级
                if ($key == $catid ||
                    $key == $this->CATEGORYS[$catid]['pid'] ||
                    $key == $this->CATEGORYS[$this->CATEGORYS[$catid]['pid']]['pid'] ||
                    $key == $this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$catid]['pid']]['pid']]['pid'] ||
                    $key == $this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$catid]['pid']]['pid']]['pid']]['pid'] ||
                    $key == $this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$catid]['pid']]['pid']]['pid']]['pid']]['pid'] ||
                    $key == $this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$this->CATEGORYS[$catid]['pid']]['pid']]['pid']]['pid']]['pid']]['pid']
                    ) {
                    $this->NAVS[$key]['action'] = 1;
                    $top_catid = $key;
                } else {
                    $this->NAVS[$key]['action'] = 0;
                }
            }
		}
		    $this->assign('top_catid', $top_catid);
        $this->assign('NAVS', $this->NAVS);
    }

    /* 首页 */
    public function index(){
        $meta_title = C('WEB_SITE_TITLE');
        $meta_keywords = C('WEB_SITE_KEYWORD');
        $meta_description = C('WEB_SITE_DESCRIPTION');
        $this->assign('meta_title', $meta_title);
        $this->assign('meta_keywords', $meta_keywords);
        $this->assign('meta_description', $meta_description);
        $this->assign('is_index', 1);
        $this->display();
    }

    /* 列表 */
    public function lists($catid = 0) {
        if (!$catid) {
            $this->redirect('index');
        }
        //当前栏目信息
        $CAT = $this->CATEGORYS[$catid];
        if ($CAT['setting']['location_catid'] != $catid) {
            if ($CAT['setting']['location_catid']) {
                $this->redirect('lists', array('catid'=>$CAT['setting']['location_catid']));
            } else {
                    //子栏目列表
                    $childs_2 = get_category($catid);
                    // 取第一个子catid
                    if ($childs_2) {
                        $child_shift_2 = array_shift($childs_2);
                        $this->redirect('lists', array('catid'=>$child_shift_2['id']));
                    }
            }
        }
        $this->assign('catid', $catid);
        //当前栏目信息
        $CAT = $this->CATEGORYS[$catid];
        $this->assign('CAT', $CAT);
        switch ($CAT['type']) {
            case 'News':
            case 'News2':
            case 'News3':
            case 'News4':
                // 如果没有设置模版就找上级模版，如果还没有就默认喽，我也只能帮到这了，如需无限上级自己写，不过没意义
                if (empty($CAT['setting']['column_template'])) {
                    if (!empty($CAT['setting']['list_template'])) {
                        $template = $CAT['setting']['list_template'] ? $CAT['setting']['list_template'] : 'lists';
                    } else {
                        $template = $this->CATEGORYS[$CAT['pid']]['setting']['list_template'] ? $this->CATEGORYS[$CAT['pid']]['setting']['list_template'] : 'lists';
                    }
                } else {
                    $template = $CAT['setting']['column_template'] ;
                }
                if (IS_AJAX) {
                    $template = 'Ajax'.$template;
                }
                $childs = $this->_getChilds($catid);
                $where = array();
                $where['status'] = 1;
                $where['type'] = $CAT['type'];
                $where['catid'] = array('in', $childs);
                if ($CAT['setting']['page_num']) {
                    $page_num = $CAT['setting']['page_num'];
                } else {
                    $page_num = $this->CATEGORYS[$CAT['pid']]['setting']['page_num'] ? $this->CATEGORYS[$CAT['pid']]['setting']['page_num'] : 0;
                }
                $list = $this->getAll('News', $where, $this->order, $page_num);
                $CATEGORYS = D('Category')->getAll();
                foreach ($list as $key => $val) {
                    if ($val['extends']) {
                        $list[$key]['extends'] = unserialize($val['extends']);
                    }
                    if ($CATEGORYS[$val['catid']]['english']) {
                        $list[$key]['url'] = U('/' . $CATEGORYS[$val['catid']]['english'] . '/' . $val['id']);
                    } else {
                        $list[$key]['url'] = U('show', array('catid' => $val['catid'], 'id' => $val['id']));
                    }
                }
                $this->assign('list', $list);
                $meta_title = $CAT['setting']['meta_title'] ? $CAT['setting']['meta_title'] : $CAT['title'] . ' - ';
                $meta_keywords = $CAT['setting']['meta_keywords'] ? $CAT['setting']['meta_keywords'] : '';
                $meta_description = $CAT['setting']['meta_description'] ? $CAT['setting']['meta_description'] : '';
                break;

            case 'Pages':
                // 如果没有设置模版就找上级模版，如果还没有就默认喽，我也只能帮到这了，如需无限上级自己写，不过没意义
                if (empty($CAT['setting']['column_template'])) {
                    if (!empty($CAT['setting']['page_template'])) {
                        $template = $CAT['setting']['page_template'] ? $CAT['setting']['page_template'] : 'pages';
                    } else {
                        $template = $this->CATEGORYS[$CAT['pid']]['setting']['page_template'] ? $this->CATEGORYS[$CAT['pid']]['setting']['page_template'] : 'pages';
                    }
                } else {
                    $template = $CAT['setting']['column_template'] ;
                }
                $content = M('Pages')->where(array('catid'=>$catid))->getField('content');
                $this->assign('content', get_content($content));

                $meta_title = $CAT['setting']['meta_title'] ? $CAT['setting']['meta_title'] : $CAT['title'] . ' - ';
                $meta_keywords = $CAT['setting']['meta_keywords'] ? $CAT['setting']['meta_keywords'] : '';
                $meta_description = $CAT['setting']['meta_description'] ? $CAT['setting']['meta_description'] : '';
                break;

            default:
                $this->redirect('/');
                break;
        }
        $meta_title .= C('WEB_SITE_TITLE');
        $meta_keywords = $meta_keywords ? $meta_keywords : C('WEB_SITE_KEYWORD');
        $meta_description = $meta_description ? $meta_description : C('WEB_SITE_DESCRIPTION');
        $this->assign('meta_title', $meta_title);
        $this->assign('meta_keywords', $meta_keywords);
        $this->assign('meta_description', $meta_description);
        $this->display($template);
    }

    /* 详情 */
    public function show($id = 0) {
        if (empty($id)) {
            $this->redirect('index');
        }
        $info = M('News')->find($id);
        if (empty($info)) {
            $this->redirect('index');
        }
        //当前栏目信息
        $catid = $info['catid'];
        $CAT = $this->CATEGORYS[$catid];
        $this->assign('catid', $catid);
        $this->assign('CAT', $CAT);
        $info['content'] = get_content($info['content']);
        $info['extends'] = unserialize($info['extends']);
        $info['images'] = unserialize($info['images']);
        $this->assign('info', $info);
        M('News')->where(array('id'=>$id))->setInc('views',1); // 访问次数加1
        // 如果没有设置模版就找上级模版，如果还没有就默认喽，我也只能帮到这了，如需无限上级自己写，不过没意义
        if (!empty($CAT['setting']['show_template'])) {
            $template = $CAT['setting']['show_template'] ? $CAT['setting']['show_template'] : 'show';
        } else {
            $template = $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] ? $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] : 'show';
        }

        $meta_title = $info['extends']['meta_title'] ? $info['extends']['meta_title'] : $info['title'] . ' - ' . C('WEB_SITE_TITLE');
        $meta_keywords = $info['extends']['meta_keywords'] ? $info['extends']['meta_keywords'] : $info['title'];;
        $meta_description = $info['extends']['meta_description'] ? $info['extends']['meta_description'] : $info['title'];;

        $this->assign('meta_title', $meta_title);
        $this->assign('meta_keywords', $meta_keywords);
        $this->assign('meta_description', $meta_description);
        $this->display($template);
    }

    /* 详情 */
    public function video_show($id = 0) {
        if (empty($id)) {
            $this->redirect('index');
        }
        $info = M('News')->find($id);
        if (empty($info)) {
            $this->redirect('index');
        }
        //当前栏目信息
        $catid = $info['catid'];
        $CAT = $this->CATEGORYS[$catid];
        $this->assign('catid', $catid);
        $this->assign('CAT', $CAT);
        $info['content'] = get_content($info['content']);
        $info['extends'] = unserialize($info['extends']);
        $info['images'] = unserialize($info['images']);
        $this->assign('info', $info);
        M('News')->where(array('id'=>$id))->setInc('views',1); // 访问次数加1
        // 如果没有设置模版就找上级模版，如果还没有就默认喽，我也只能帮到这了，如需无限上级自己写，不过没意义
        if (!empty($CAT['setting']['show_template'])) {
            $template = $CAT['setting']['show_template'] ? $CAT['setting']['show_template'] : 'show';
        } else {
            $template = $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] ? $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] : 'show';
        }
        $meta_title = $info['title'] . ' - ' . C('WEB_SITE_TITLE');
        $meta_keywords = $info['title'];
        $meta_description = $info['description'];
        $this->assign('meta_title', $meta_title);
        $this->assign('meta_keywords', $meta_keywords);
        $this->assign('meta_description', $meta_description);
        $this->display();
    }

    /* 详情 */
    public function data_show($id = 0) {
        if (empty($id)) {
            $this->redirect('index');
        }
        $info = M('News')->find($id);
        if (empty($info)) {
            $this->redirect('index');
        }
        //当前栏目信息
        $catid = $info['catid'];
        $CAT = $this->CATEGORYS[$catid];
        $this->assign('catid', $catid);
        $this->assign('CAT', $CAT);
        $info['content'] = get_content($info['content']);
        $info['extends'] = unserialize($info['extends']);
        $info['images'] = unserialize($info['images']);
        $this->assign('info', $info);
        M('News')->where(array('id'=>$id))->setInc('views',1); // 访问次数加1
        // 如果没有设置模版就找上级模版，如果还没有就默认喽，我也只能帮到这了，如需无限上级自己写，不过没意义
        if (!empty($CAT['setting']['show_template'])) {
            $template = $CAT['setting']['show_template'] ? $CAT['setting']['show_template'] : 'show';
        } else {
            $template = $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] ? $this->CATEGORYS[$CAT['pid']]['setting']['show_template'] : 'show';
        }
        $meta_title = $info['title'] . ' - ' . C('WEB_SITE_TITLE');
        $meta_keywords = $info['title'];
        $meta_description = $info['description'];
        $this->assign('meta_title', $meta_title);
        $this->assign('meta_keywords', $meta_keywords);
        $this->assign('meta_description', $meta_description);
        $this->display();
    }

    /* 搜索（根据情况自定义） */
    public function search($keyword = ''){
        $where = array(
            'title' => array('like', '%'. $keyword .'%'),
        );
        $list = $this->getAll('News', $where, $this->order, $page_num);
        foreach ($list as $key => $val) {
            $list[$key]['url'] = U('show', array('catid'=>$val['catid'],'id'=>$val['id']));
        }
        $this->assign('list', $list);
        $template = ACTION_NAME;
        if (IS_AJAX) {
            $template .= 'Ajax';
        }
        $this->assign('keyword', $keyword);
        $this->display($template);
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
