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
$rec = $check->is_rec($_REQUEST['rec']) ? $_REQUEST['rec'] : 'default';

// 图片上传
include_once (ROOT_PATH . 'include/file.class.php');
$file = new File('images/friend/'); // 实例化类文件(文件上传路径，结尾加斜杠)

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'friend');

/**
 * +----------------------------------------------------------
 * 文章列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['friend']);
    $smarty->assign('action_link', array (
            'text' => '添加友情链接',
            'href' => 'friend.php?rec=add' 
    ));
    
    // 获取参数
    $cat_id = $check->is_number($_REQUEST['cat_id']) ? $_REQUEST['cat_id'] : 0;
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    
    // 筛选条件
    $where = ' WHERE 1=1 ';
    if ($keyword) {
        $where = $where . " AND title LIKE '%$keyword%'";
        $get = '&keyword=' . $keyword;
    }
    
    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'friend.php';
    $limit = $dou->pager('friend_link', 15, $page, $page_url, $where, $get);
    
    $sql = "SELECT * FROM " . $dou->table('friend_link') . $where . " ORDER BY id DESC" . $limit;
    $query = $dou->query($sql);
    while ($row = $dou->fetch_array($query)) {
        
        $add_time = date("Y-m-d", $row['add_time']);
        
        $friend_list[] = array (
                "id" => $row['id'],
                "title" => $row['title'],
                "add_time" => $add_time,
                "link_url" => $row['link_url'],
        );
    }
 
    // 赋值给模板
    $smarty->assign('keyword', $keyword);
    $smarty->assign('friend_list', $friend_list);
    
    $smarty->display('friend.htm');
} 

/**
 * +----------------------------------------------------------
 * 文章添加
 * +----------------------------------------------------------
 */
elseif ($rec == 'add') {
    $smarty->assign('ur_here', '添加友情链接');
    $smarty->assign('action_link', array (
            'text' => '友情链接列表',
            'href' => 'friend.php' 
    ));
    
    // 格式化自定义参数，并存到数组$article，文章编辑页面中调用文章详情也是用数组$article，
    if ($_DEFINED['article']) {
        print_r($_DEFINED);
        $defined = explode(',', $_DEFINED['article']);
        foreach ($defined as $row) {
            $defined_article .= $row . "：\n";
        }
        $article['defined'] = trim($defined_article);
        $article['defined_count'] = count(explode("\n", $article['defined'])) * 2;
    }
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'insert');
    
    $smarty->assign('article', $article);
    
    $smarty->display('friend.htm');
} 

elseif ($rec == 'insert') {
    // 验证标题
    if (empty($_POST['title'])) $dou->dou_msg('友情链接标题不能为空');
    
    // 数据格式化
    $add_time = time();
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
        
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    $sql = "INSERT INTO " . $dou->table('friend_link') . " (id, title, link_url, add_time, sort)" . " VALUES (NULL, '$_POST[title]', '$_POST[link_url]', '$add_time','$_POST[sort]')";
    $dou->query($sql);
    
    $dou->create_admin_log('添加友情链接' . ': ' . $_POST['title']);
    $dou->dou_msg('添加成功', 'friend.php');
} 

/**
 * +----------------------------------------------------------
 * 文章编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'edit') {
    $smarty->assign('ur_here', '编辑友情链接');
    $smarty->assign('action_link', array (
            'text' => $_LANG['friend'],
            'href' => 'friend.php' 
    ));
    
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : '';
    
    $query = $dou->select($dou->table('friend_link'), '*', '`id` = \'' . $id . '\'');
    $friend = $dou->fetch_array($query);
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->get_token());
    
    // 赋值给模板
    $smarty->assign('form_action', 'update');
    $smarty->assign('friend', $friend);
    
    $smarty->display('friend.htm');
} 

elseif ($rec == 'update') {
    // 验证标题
    if (empty($_POST['title'])) $dou->dou_msg('友情链接标题不能为空');
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token']);
    
    $sql = "UPDATE " . $dou->table('friend_link') . " SET title = '$_POST[title]',  link_url = '$_POST[link_url]', sort = '$_POST[sort]' WHERE id = '$_POST[id]'";
    $dou->query($sql);
    
    $dou->create_admin_log('修改友情链接' . ': ' . $_POST['title']);
    $dou->dou_msg($_POST['title'].'修改成功', 'friend.php');
} 


/**
 * +----------------------------------------------------------
 * 文章删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的ID
    $id = $check->is_number($_REQUEST['id']) ? $_REQUEST['id'] : $dou->dou_msg($_LANG['illegal'], 'friend.php');
    $title = $dou->get_one("SELECT title FROM " . $dou->table('friend_link') . " WHERE id = '$id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        
        $dou->create_admin_log($_LANG['friend_del'] . ': ' . $title);
        $dou->delete($dou->table('friend_link'), "id = '$id'", 'friend.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $title, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'friend.php', '', '30', "friend.php?rec=del&id=$id");
    }
} 

/**
 * +----------------------------------------------------------
 * 批量操作选择
 * +----------------------------------------------------------
 */
elseif ($rec == 'action') {
    if (is_array($_POST['checkbox'])) {
        if ($_POST['action'] == 'del_all') {
            // 批量文章删除
            $dou->del_all('friend_link', $_POST['checkbox'], 'id');
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        $dou->dou_msg($_LANG['friend_select_empty']);
    }
}



?>