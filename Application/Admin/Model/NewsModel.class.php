<?php
namespace Admin\Model;
use Think\Model;
class NewsModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('catid','require','所属栏目必选！'),
        array('title','require','标题必填！'),
    );

    /* 自动完成 */
    protected $_auto = array(
        array('status', 1),
        array('create_time', 'callbackCreateTime', 3, 'callback'),
        array('update_time', 'time', 2, 'function'),
        array('extends', 'callbackExtends', 3, 'callback'),
        array('images', 'callbackImages', 3, 'callback'),
    );

    public function callbackExtends($data) {
        if(empty($data)){
            return ' ';
        } else {
            return serialize($data);
        }
    }
    public function callbackImages($data) {
        if(empty($data)){
            return '';
        } else {
            $data = array_filter($data);
            if (empty($data)) {
                return '';
            } else {
                return serialize($data);
            }
        }
    }
    public function callbackCreateTime($data) {
        if(empty($data)){
            return NOW_TIME;
        } else {
            return strtotime($data);
        }
    }

    /* id获取 */
    public function getById($id){
        $info = $this->find($id);
        if ($info) {
            $info['extends'] = unserialize($info['extends']);
            $info['images'] = unserialize($info['images']);
        }
        return $info;
    }

    /* 插入操作 */
    public function input () {
        if (!$this->create()) {
            return false;
        } else {
            $this->add();
            return true;
        }
    }

    /* 更新操作 */
    public function update () {
        if (!$this->create()) {
            return false;
        } else {
            $this->save();
            return true;
        }
    }

}
