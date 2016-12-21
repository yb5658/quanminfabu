<?php
namespace Admin\Model;
use Think\Model;
class LevelModel extends Model {
    /* 自动验证 */
    protected $_validate = array(
        array('title','require','等级名称必填！'),
        array('short_title','require','短标题必填！'),
        array('price','require','价格必填！'),
        array('counts','require','分享次数必填！'),
        array('description','require','描述必填！'),
    );
    /* 自动完成 */
    protected $_auto = array(
        array('create_time', 'time', 3, 'function'),
        array('update_time', 'time', 2, 'function'),
    );
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
