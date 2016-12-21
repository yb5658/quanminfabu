<?php
namespace Home\Model;
use Think\Model;
class CategoryModel extends Model {

    protected $order = 'sort desc, id asc'; //统一排序

    /* 获取所有栏目 */
    public function getAll() {
        $data = S('CATEGORYS');
        if (!$data) {
            $categorys = $this->where(1)->order($this->order)->select();
            foreach ($categorys as $key => $val) {
                $data[$val['id']] = $val;
                $data[$val['id']]['setting'] = unserialize($val['setting']);
                if ($val['english']) {
                    // $url_route_rules[$val['english']] = array('Index/lists', array('catid'=>$val['id']));
                    $data[$val['id']]['url'] = U('/' . $val['english']);
                    $data[$val['id']]['url_route'][$val['english'] . '/:id\d'] = array('Index/show', array('catid'=>$val['id']));
                    $data[$val['id']]['url_route'][$val['english'] . '/:p\d'] = array('Index/lists', array('catid'=>$val['id']));
                    $data[$val['id']]['url_route'][$val['english']] = array('Index/lists', array('catid'=>$val['id']));
                } else {
                    $data[$val['id']]['url'] = U('lists', array('catid'=>$val['id']));
                }
            }
            S('CATEGORYS', $data);
        }
        return $data;
    }
}
