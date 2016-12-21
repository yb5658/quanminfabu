<?php
namespace Home\Widget;
class IndexWidget extends \Think\Controller {
    public function index(){
        echo '测试';
    }

    public function message() {

        $this->display('Widget/Index/message');
    }
}
