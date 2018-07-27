<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class UserModel extends MY_Model {

	protected $_table = 'user';

	public function __construct() {
		parent::__construct ();
		$this->load->library('Global_func');
		$this->load->model('commonModel');
		$this->load->model('userBalanceModel');
		
	}

	/*
	*获取用户信息详情
	*uid 用户id
	*isAll 是否查询所有信息（包含用户余额信息）
	*/
	public function userInfo($uid,$isAll=false){
		if(!is_numeric($uid) || empty($uid)){
			return false;
		}
		$res = array();
		if($isAll){
			$sqlUserInfo = "select * from ".$this->_table." where id = '".$uid."' limit 1 ";
			$resUserInfo = $this->commonModel->get_one_data_by_sql($sqlUserInfo);
			$resUserBalanceInfo = $this->userBalanceModel->userBalanceInfo($uid);
			$res = $resUserInfo;
			$res['userBalanceInfo'] = $resUserBalanceInfo;
		}else{
			$sql = "select * from ".$this->_table." where id = '".$uid."' limit 1 ";
			$res = $this->commonModel->get_one_data_by_sql($sql);
		}
		return $res;
	}

	
	

	
}