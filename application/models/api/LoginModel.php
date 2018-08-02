<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class LoginModel extends MY_Model {

	private $wxKeySessionUrl = "https://api.weixin.qq.com/sns/jscode2session";
	public function __construct() {
		parent::__construct ();
		$this->load->library('Global_func');
		$this->load->model('commonModel');
		
	}


	/*
	*获取微信sessionKey内容
	*/
	public function getWxSessionKey($param){
		
		$info = array();
		$info = $this->global_func->curl_post($this->wxKeySessionUrl,$param);

		return $info;
	}

	
	
	

	
}