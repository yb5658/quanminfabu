<?php
namespace Home\Model;
use Think\Model;
class MessageModel extends Model {

    protected $fields = array('id', 'title', 'email', 'tel', 'content', 'create_time', 'update_time', 'status', 'extend');

    /* 自动验证 */
    protected $_validate = array(
        array('title', 'require', '姓名必填!'),
        array('tel', '/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/', '请填写正确的手机号码!',1),
    );

    /* 自动完成 */
    protected $_auto = array(
        array('extend', 'callbackExtend', 3, 'callback'),
        array('create_time', NOW_TIME, 1),
        array('update_time', NOW_TIME, 2),
        array('status', 0)
    );

    public function callbackExtend($data) {
        if(empty($data)){
            return '';
        } else {
            // $data = array_filter($data);
            // if (empty($data)) {
            //     return '';
            // } else {
            //     return serialize($data);
            // }
            return serialize($data);
        }
    }

    public function input() {
        if(!$this->create()) {
            return false;
        } else {
            if($this->add()) {
                return true;
            } else {
                $this->error = '留言失败';
                return false;
            }
        }
    }

}
