<?php
/**
 * seo方法未完成
 * @param   string
 * @return  string
 */
function seo() {

    return '';
}
// 微信日志
function addWeixinLog($data = '') {
    $log =  '====' . date( 'Y-m-d H:i:s') . '====' . PHP_EOL;
    $log .= $data . PHP_EOL . PHP_EOL;
    file_put_contents('weixin.log', $log, FILE_APPEND);
}
// 微信昵称过滤
function remove_emoji($string) {
    return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $string);
}/**
 * 获取图片
 * @param
 * @return  string
 */
function thumb($img = '', $width = 0, $height = 0) {
    if (empty($img)) {
        return __ROOT__ . '/default.jpg';
    }
    $Uploads = '/Uploads/';
    $file = '.' . $Uploads . $img;
    if (file_exists($file)) {
        if (empty($width)) {
            return __ROOT__ . substr($file, 1);
        } else {
            $pathinfo = pathinfo($file);
            $thumb_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . '-' . $height . '.' . $pathinfo['extension'];
            if (file_exists($thumb_file)) {
                return __ROOT__ . substr($thumb_file, 1);
            } else {
                $image = new \Think\Image();
                $image->open($file);
                if (empty($height)) {
                    $height = $image->height();
                }
                $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_file);
                return __ROOT__ . substr($thumb_file, 1);
            }
        }
    }
    return __ROOT__ . '/default.jpg';
}

/**
 * 获取内容信息
 * @param   string       $content  内容
 * @return  string
 */
function get_content($content = ''){
    if ($content) {
        //return preg_replace('/src="(.*?)"/', 'src="'.__ROOT__.'$1"', html_entity_decode($content));
        return html_entity_decode($content);
    } else {
        return '';
    }
}

/**
 * 获取单页内容
 * @param  int  $catid
 * @return  string
 */
function get_page($catid = 0){
    if ($catid) {
        return M('Pages')->where(array('catid'=>$catid))->getField('content');
    } else {
        return '';
    }
}

/**
 * 获取子栏目id
 * @param  [type] $catid [description]
 * @return [type]        [description]
 */
function get_childs($catid, $field = ','){
	$cates = get_category($catid);
	if ($cates) {
		$str = '';
        $i =1;
		foreach($cates as $key => $val){
			$str .= $key;
            $temp = get_childs($key, $field);
            if ($temp) {
                $str .= $field . $temp;
            }
            if (count($cates) != $i) {
                $str .= $field;
            }
            $i++;
		}
		return $str;
	} else {
		return '';
	}
}

/**
 * 获取子栏目id
 * @param  [type] $catid [description]
 * @return [type]        [description]
 */
function get_childs_array($catid){
	$cates = get_category($catid);
	if ($cates) {
		$array = array();
		foreach($cates as $key => $val){
            array_push($array, $key);
            foreach (get_childs_array($key) as $v) {
                array_push($array, $v);
            }
		}
		return $array;
	} else {
		return array();
	}
}

/**
 * 获取父级栏目
 * @param   int       $catid  栏目catid
 * @return  array
 */
function get_category($catid = 0, $num = 0) {
    $data = array();
    foreach ( D('Category')->getAll() as $key => $val) {
        if ($val['pid'] == $catid && $val['display']) {
            $data[$key] = $val;
            if ($num && count($data) >= $num) {
                break;
            }
        }
    }
    return $data;
}

/**
 * 获取广告位
 * @param   int         $id   广告位id
 * @param   int/string  $limit   数量
 * @return  array
 */
function get_banner($id = 0, $limit = 0){
    if (empty($id)) {
        return array();
    } else {
        $model = M('BannerData');
        $map = array();
        $map['bid'] = $id;
        if (empty($limit)) {
            $limit = '';
        }
        $lists = $model->cache(false, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
        if ($lists) {
            return $lists;
        } else {
            return array();
        }
    }
}

/**
 * 获取推荐位信息
 * @param  string  $field [自定义的推荐位字段]
 * @param  integer $catid [栏目catid]
 * @param  string  $type  [数据类型News、Product、Sping]
 * @param  integer $limit [梳理]
 * @return array          [数组]
 */
function get_position($catid = 0,  $limit = 0, $field = 'position_1'    ){
    $model = M('News');
    $map = array(
        'status' => 1,
        $field => 1,
    );
    if ($catid) {
        $childs = get_childs_array($catid);
        if ($childs) {
            $childs[] = $catid;
            $map['catid'] = array('in', $childs);
        } else {
            $map['catid'] = $catid;
        }
    }
    if (empty($limit)) {
        $limit = '';
    }
    $lists = $model->cache(false, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
    if ($lists) {
        $CATEGORYS = D('Category')->getAll();
        foreach($lists as $key=>$val){
            if($val['extends']){
                $lists[$key]['extends'] = unserialize($val['extends']);
            }
            if ($CATEGORYS[$val['catid']]['english']) {
                $lists[$key]['url'] = U('/' . $CATEGORYS[$val['catid']]['english'] . '/' . $val['id']);
            } else {
                $lists[$key]['url'] = U('show', array('catid' => $val['catid'], 'id' => $val['id']));
            }
        }
        return $lists;
    } else {
        return array();
    }
}

/**
 * 获取列表信息
 * @param  integer $catid [栏目catid]
 * @param  string  $type  [数据类型News、Product、Sping]
 * @param  integer $limit [梳理]
 * @return array          [数组]
 */
function get_lists($catid = 0, $limit = 0){
    $model = M('News');
    $map = array('status' => 1);
    if ($catid) {
        $childs = get_childs_array($catid);
        if ($childs) {
            $childs[] = $catid;
            $map['catid'] = array('in', $childs);
        } else {
            $map['catid'] = $catid;
        }
    }
    if (empty($limit)) {
        $limit = '';
    }
    $lists = $model->cache(false, 60)->where($map)->order('sort desc, id desc')->limit($limit)->select();
    if ($lists) {
        $CATEGORYS = D('Category')->getAll();
        foreach($lists as $key=>$val){
            if($val['extends']){
                $list[$key]['extends'] = unserialize($val['extends']);
            }
            if ($CATEGORYS[$val['catid']]['english']) {
                $lists[$key]['url'] = U('/' . $CATEGORYS[$val['catid']]['english'] . '/' . $val['id']);
            } else {
                $lists[$key]['url'] = U('show', array('catid' => $val['catid'], 'id' => $val['id']));
            }
        }
        return $lists;
    } else {
        return array();
    }
}

// 获取单个文件
function get_file($file = '') {
    if (empty($file)) {
        return '';
    }
    $Uploads = '/Uploads/';
    $file = '.' . $Uploads . $file;
    if (file_exists($file)) {
        return __ROOT__ . substr($file, 1);
    } else {
        return '';
    }
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $ext 栏目间隔符
 */
function catpos($catid = 0, $ext = '') {
    $categorys = D('Category')->getAll();
    $html = '';
    if ($catid == 0) {
        $html = '<p><i>&gt;</i><a href="'. U('/') .'">首页</a></p>' . $html;
        return $html;
    } else {
        $html = $ext . '<p><i>&gt;</i><a  href="' . $categorys[$catid]['url'] . '">' . $categorys[$catid]['title'] . '</a></p>' . $html;
        $html = catpos($categorys[$catid]['pid'], $ext) . $html;
    }
    return $html;
}



    // 上一页
    function prve_page($id, $catid = 0){
        $map = array();
        if ($catid) {
            $map['catid'] = $catid;
        }
                $lists = M('News')->field('title, id, catid')->where($map)->order('sort desc, id desc')->select();
                foreach ($lists as $key => $value) {
                    if ($value['id'] == $id && isset($lists[$key-1])) {
                        return '<a href="'.U('Index/show', array('catid'=>$value['catid'],'id'=>$lists[$key-1]['id'])).'">上一篇：'. $lists[$key-1]['title'] .'</a>';
                    }
                }
                return '<a href="javascript:void(0);" >上一篇：暂无</a>';
    }

    // 下一页
    function next_page($id, $catid = 0){
        $map = array();
        if ($catid) {
            $map['catid'] = $catid;
        }
                $lists = M('News')->field('title, id, catid')->where($map)->order('sort desc, id desc')->select();
                foreach ($lists as $key => $value) {
                    if ($value['id'] == $id && isset($lists[$key+1])) {
                        return '<a href="'.U('Index/show', array('catid'=>$value['catid'],'id'=>$lists[$key+1]['id'])).'">下一篇：'. $lists[$key+1]['title'] .'</a>';
                    }
                }
                return '<a href="javascript:void(0);">下一篇：暂无</a>';
    }
