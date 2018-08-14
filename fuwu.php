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

$fuwu = $dou->get_row('fuwu', '*', "type = '1'");//type=1 服务页
    if($fuwu['contents']){
        $fuwu['contents'] = urldecode($fuwu['contents']);
        $fuwu['contents_arr'] = json_decode($fuwu['contents'],true);
    }
    //print_r($fuwu);
    $smarty->assign('info', $fuwu);

$smarty->display('fuwu.html');

?>