<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class UserBalanceModel extends MY_Model {

	protected $_table = 'user_balance';
	protected $globalCacheKey = 'userBalance:';
	private $userBalanceInfoCacheKey = 'userBalanceInfo:';
	private $expireTime = 60;

	public function __construct() {
		parent::__construct ();
		$this->load->library('Global_func');
		$this->load->model('commonModel');
		
	}

	/*
	*获取用户余额信息详情
	*uId 用户id
	*/
	public function userBalanceInfo($uId){
		if(!is_numeric($uId) || empty($uId)){
			return false;
		}
		$balanceInfo = array();
		$balanceInfo = $this->getRedisUserBalanceInfo($uId);
		//$balanceInfo = false;
		if(!$balanceInfo || !is_array($balanceInfo)){
			$balanceInfo = $this->_userBalanceInfo($uId);
			//var_dump($balanceInfo);
			if($balanceInfo['uid'] > 0){
				$this->cacheUserBalanceInfo($uId, $balanceInfo);
				$balanceInfo['noCache'] = 1; 
			}
		}
		
		return $balanceInfo;
	}

	/*
	*获取用户余额数据库信息详情
	*uId 用户id
	*/
	public function _userBalanceInfo($uId){
		if(!is_numeric($uId) || empty($uId) ||intval($uId) < 1){
			return false;
		}
		$balanceInfo = array();
		$sql = "select * from ".$this->_table." where uId = '".$uId."' limit 1 ";
		$balanceInfo = $this->commonModel->get_one_data_by_sql($sql);
		return $balanceInfo;
	}


	/**
	 * 缓存用户余额信息到Redis
	 *
	 * @param array $info
	 * @return string
	 */
	public function cacheUserBalanceInfo($uId, $info) {
		if (! $info || ! is_numeric ( $uId )) return false;
		$cacheKey = $this->getCacheKey($uId);
		// 设置redis缓存及过期时间
		$rs = $this->commonModel->redis->hMSet ($cacheKey, $info );
		$this->commonModel->redis->expire($cacheKey, $this->expireTime);

		return $rs;
	}

	/**
	 * 清除用户余额缓存
	 *
	 * @param int $uId
	 * @return boolen
	 */
	public function clearCacheUserBalanceInfo($uId) {
		$cacheKey = $this->getCacheKey($uId);
		return $this->commonModel->redis->delete($cacheKey);
	}

	/**
	 * 获取用户余额redis信息
	 *
	 * @param unknown_type $uId
	 * @param unknown_type $field
	 */
	public function getRedisUserBalanceInfo($uId) {
		if (! is_numeric ( $uId ))
			return false;
		$cacheKey = $this->getCacheKey($uId);
		$result = $this->commonModel->redis->hGetAll ( $cacheKey );
		
		return $result;
	}


	public function getCacheKey($uId){
		$cacheKey = $this->globalCacheKey . $this->userBalanceInfoCacheKey . $uId;
		return $cacheKey;
	}
	
	

	
}