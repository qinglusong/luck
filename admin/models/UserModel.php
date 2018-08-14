<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class UserModel extends MY_Model {

	protected $_table = 'user';
	protected $globalCacheKey = 'user:';
	private $userInfoCacheKey = 'userInfo:';
	private $expireTime = 5;//一小时

	public function __construct() {
		parent::__construct ();
		$this->load->library('Global_func');
		$this->load->model('commonModel');
		$this->load->model('userBalanceModel');
		
	}


	/*
	*获取用户信息详情
	*uId 用户id
	*isAll 是否查询所有信息（包含用户余额信息）
	*/
	public function userInfo($uId, $isAll=0){
		if(!is_numeric($uId) || empty($uId) ||intval($uId) < 1){
			return false;
		}
		$info = array();

		//读取redis
		$info = $this->getRedisUserInfo($uId,$isAll);
		//print_r($info);
		//$info = false;
		if(!$info || !is_array($info)){
			$info = $this->_userInfo($uId, $isAll);
			if($info['id'] > 0){
				$this->cacheUserInfo($uId, $info, $isAll);
			}
		}
		if($info['userBalanceInfo']){
			$info['userBalanceInfo'] = json_decode($info['userBalanceInfo'],true);//hmset 解决二维数组问题 encode
		}
		

		return $info;
	}

	/**
	 * 获取用户数据库信息
	 *
	 */
	private function _userInfo($uId, $isAll=0){
		if(!is_numeric($uId) || empty($uId) ||intval($uId) < 1){
			return false;
		}
		$res = array();
		if($isAll){
			$sqlUserInfo = "select * from ".$this->_table." where id = '".$uId."' limit 1 ";
			$resUserInfo = $this->commonModel->get_one_data_by_sql($sqlUserInfo);
			$resUserBalanceInfo = $this->userBalanceModel->userBalanceInfo($uId);
			if($resUserBalanceInfo){
				$res = $resUserInfo;
				$res['userBalanceInfo'] = json_encode($resUserBalanceInfo);
			}
			
		}else{
			$sql = "select * from ".$this->_table." where id = '".$uId."' limit 1 ";
			$res = $this->commonModel->get_one_data_by_sql($sql);
		}

		return $res;
	}

	/**
	 * 缓存用户信息到Redis
	 *
	 * @param unknown_type $userInfo
	 * @return string
	 */
	public function cacheUserInfo($uId, $userInfo, $isAll=0) {
		if (! $userInfo || ! is_numeric ( $uId )) return false;
		$cacheKey = $this->getCacheKey($uId,$isAll);
		// 设置redis缓存及过期时间
		$rs = $this->commonModel->redis->hMSet ($cacheKey, $userInfo );
		$this->commonModel->redis->expire($cacheKey, $this->expireTime);

		return $rs;
	}

	/**
	 * 清除用户缓存
	 *
	 * @param unknown_type $userInfo
	 * @return string
	 */
	public function clearCacheUserInfo($uId, $isAll=0) {
		$cacheKey = $this->getCacheKey($uId,$isAll);
		return $this->commonModel->redis->delete($cacheKey);
	}

	/**
	 * 获取用户redis信息
	 *
	 * @param unknown_type $uId
	 * @param unknown_type $field
	 */
	public function getRedisUserInfo($uId, $isAll=0) {
		if (! is_numeric ( $uId ))
			return false;
		$cacheKey = $this->getCacheKey($uId,$isAll);
		$result = $this->commonModel->redis->hGetAll ( $cacheKey );

		return $result;
	}

	/**
	 * 获取redis KEY
	 *
	 * @param int $uId
	 * @param int $isAll
	 */
	public function getCacheKey($uId,$isAll=0){
		$cacheKey = $this->globalCacheKey . $this->userInfoCacheKey . $isAll . $uId;
		return $cacheKey;
	}

	
	

	
}