<?php
/**
 * DouPHP
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2013-2018 漳州豆壳网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.douco.com
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 授权协议：http://www.douco.com/license.html
 * --------------------------------------------------------------------------------------------------
 * Author: DouCo
 * Release Date: 2018-05-23
 */
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');

// 验证并获取合法的ID，如果不合法将其设定为-1
$id = intval($_REQUEST['id']);


// 赋值给模板-meta和title信息
$smarty->assign('page_title', $dou->page_title('page', '', $page['page_name']));
$smarty->assign('keywords', $page['keywords']);
$smarty->assign('description', $page['description']);

// 赋值给模板-导航栏
$smarty->assign('nav_top_list', $dou->get_nav('top'));

$smarty->assign('nav_middle_list', $dou->get_nav('middle', '0', 'page', $id));
$smarty->assign('nav_bottom_list', $dou->get_nav('bottom'));

// 赋值给模板-数据
$smarty->assign('ur_here', $dou->ur_here('page', '', $page['page_name']));
$smarty->assign('top', $top);
$smarty->assign('page', $page);
$pages_list = $dou->get_pages_list();

#print_r($pages_list);
$pages_arr = array();

foreach($pages_list as $k=>$v){
	if($v['id']==$id){
		$pages_arr['info'] = $v;
		break;
	}
}
if(empty($pages_arr['info'])){
	$pages_arr['info'] = $pages_list[0];
}
$pages_arr['list'] = $pages_list;
$smarty->assign('pages_arr',$pages_arr);

$smarty->display('pages.html');

?>