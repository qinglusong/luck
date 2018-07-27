<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class UserBalanceModel extends MY_Model {

	protected $_table = 'user_balance';

	public function __construct() {
		parent::__construct ();
		$this->load->library('Global_func');
		$this->load->model('commonModel');
		
	}

	/*
	*获取用户余额信息详情
	*uid 用户id
	*/
	public function userBalanceInfo($uid){
		if(!is_numeric($uid) || empty($uid)){
			return false;
		}
		$res = array();
		$sql = "select * from ".$this->_table." where uid = '".$uid."' limit 1 ";
		$res = $this->commonModel->get_one_data_by_sql($sql);
		return $res;
	}

	
	

	
}