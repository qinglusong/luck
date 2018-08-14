<?php
/**
 * 慧目堂设计
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2004-2017 Shanghai Three Eyes Art Design Co.,Ltd.  All Rights Reserved  © 版权所有，并保留所有权利。
 * 网站地址: http://www.3e-d.com
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 
 * --------------------------------------------------------------------------------------------------
 * Author: DouCo
 * Release Date: 2018-05-23
 */
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');

// rec操作项的初始化
$rec = $check->is_rec($_REQUEST['rec']) ? $_REQUEST['rec'] : 'edit';

// 图片上传
include_once (ROOT_PATH . 'include/file.class.php');
$file = new File('images/fuwu/'); // 实例化类文件(文件上传路径，结尾加斜杠)

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'fuwu');

/**
 * +----------------------------------------------------------
 * 文章列表
 * +----------------------------------------------------------
 */
if ($rec == 'edit') {
    $smarty->assign('ur_here', '编辑服务页面');
    $smarty->assign('action_link', array (
            'text' => '服务页面',
            'href' => 'fuwu.php' 
    ));
    
    
    $fuwu = $dou->get_row('fuwu', '*', "type = '1'");//type=1 服务页
    if($fuwu['contents']){
        $fuwu['contents'] = urldecode($fuwu['contents']);
        $fuwu['contents_arr'] = json_decode($fuwu['contents'],true);
    }
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'update');
    $smarty->assign('info', $fuwu);
    
    $smarty->display('fuwu.htm');

} 

elseif ($rec == 'update') {
    // 验证标题
    //if (empty($_POST['title'])) $dou->dou_msg('所有内容不可为空');
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);

    //print_r($_POST);
    unset($_POST['id']);
    unset($_POST['submit']);
    unset($_POST['token']);

    foreach($_POST as $k=>$v){
        if(!$v){
            $dou->dou_msg('所有内容不可为空');
            break;
        }
    }

    $contents = json_encode($_POST);
    $contents = urlencode($contents);
    
    $sql = "UPDATE " . $dou->table('fuwu') . " SET contents = '$contents',  type = '1' WHERE type = '1' ";
    $dou->query($sql);
    
    $dou->create_admin_log('修改服务页面内容');
    $dou->dou_msg($_POST['title'].'修改成功', 'fuwu.php');
} 




?>