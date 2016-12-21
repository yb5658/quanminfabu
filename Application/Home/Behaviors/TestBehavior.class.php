<?php
namespace Home\Behaviors;
class TestBehavior extends \Think\Behavior{
    //行为执行入口
    public function run(&$params){
        C('DEFAULT_MODULE', 'Home');
        $categorys = S('CATEGORYS');
        $url_route_rules= array();
        foreach ($categorys as $key => $value) {
            if ($value['url_route']) {
                $url_route_rules = array_merge($url_route_rules, $value['url_route']);
            }
        }
        $url_route_rules = array_merge($url_route_rules, array('index$'=>array('Home/Index/index'),'search$'=>array('Home/Index/search')));
        C('URL_ROUTE_RULES', $url_route_rules);
        // print_r(C('URL_ROUTE_RULES'));
    }
}
