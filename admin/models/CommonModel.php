<?php
/**
 * 一般MODEL
 * @author 
 *
 * @property	cache_redis		$cache_redis
 */
class CommonModel extends MY_Model {
	private $_trans_flag = false;
	public $redis;
	function __construct() {
		parent::__construct ();
		$this->load->library("global_func");
		$this->redis = new Redis();
        $this->redis->connect('127.0.0.1',6379);
	}
	public function get_data_by_sql($sql) {
		$res = $this->db->query ( $sql );

		if (empty ( $res )) {
			return array ();
		} else {
			return $res->result_array ();
		}
	}
	public function get_one_data_by_sql($sql) {
		$res = $this->db->query ( $sql );
		if (empty ( $res )) {
			return array ();
		} else {
			return $res->row_array ();
		}
	}
	public function execute_by_sql($sql) {
		$return = $this->db->query ( $sql );
		if (stripos ( $sql, 'insert' ) !== false) {
			$return = $this->db->insert_id ();
		}
		return $return;
	}
	public function insert_id() {
		if ($this->db->conn_write) {
			return @mysql_insert_id ( $this->db->conn_write );
		} else {
			return @mysql_insert_id ( $this->db->conn_id );
		}
	}

	/**
	 * 返回上条SQL的影响行数
	 */
	public function get_affected_row_count() {
		// 		$sql = "SELECT ROW_COUNT() as c";
		// 		if ($this->db->conn_write) {
		// 			$rs = $this->db->query($sql);
		// 		} else {
		// 			$rs = $this->db->query($sql);
		// 		}
		// P.S. PHP5.5.0之前可以使用!!
		if ($this->db->conn_write) {
			$r = mysql_affected_rows($this->db->conn_write);
		} else {
			$r = mysql_affected_rows($this->db->conn_id);
		}

		return $r;
	}
	public function trans_begin () {
		if ($this->_trans_flag) {
			$this->trans_commit();
		}

		$sql = "start transaction";
		$this->execute_by_sql($sql);
		$sql = "SET autocommit=0";
		$this->execute_by_sql($sql);

		$this->_trans_flag = true;
	}
	public function trans_commit() {
		if (!$this->_trans_flag) {
			return;
		}
		$sql = "commit";
		$this->execute_by_sql($sql);
		$sql = "SET autocommit=1";
		$this->execute_by_sql($sql);
		$this->_trans_flag = false;
	}
	public function trans_rollback() {
		if (!$this->_trans_flag) {
			return;
		}
		$sql = "rollback";
		$this->execute_by_sql($sql);
		$sql = "SET autocommit=1";
		$this->execute_by_sql($sql);
		$this->_trans_flag = false;
	}

	// ----------------------------------------------------------------------------------------------------------//

	/**
     *随机数生成
     */
	public function get_code($length=32,$mode=0)//获取随机验证码函数
	{
			switch ($mode)
			{
					case '1':
							$str='0123456789';
							break;
					case '2':
							$str='abcdefghijklmnopqrstuvwxyz';
							break;
					case '3':
							$str='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
					case '4':
							$str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
							break;
					case '5':
							$str='ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
							break;
					case '6':
							$str='abcdefghijklmnopqrstuvwxyz1234567890';
							break;
					default:
							$str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
							break;
			}
			$checkstr='';
			$len=strlen($str)-1;
			for ($i=0;$i<$length;$i++)
			{
					//$num=rand(0,$len);//产生一个0到$len之间的随机数
					$num=mt_rand(0,$len);//产生一个0到$len之间的随机数
					$checkstr.=$str[$num];


			}

			return $checkstr;
	}


	//获取分页数据
	public function get_page_data($data)
	{
		$data['show_pages'] = empty($data['show_pages']) ? 5 : $data['show_pages'];	// 最多展示多少页
		$data['url_prefix'] = empty($data['url_prefix']) ? $data['controllers_name'] : $data['url_prefix'];	// URL前缀 . page
		$data['page'] = (int)$data['page'] < 1 ? 1 : (int)$data['page']; // 当前页数
		$data['total_rows'] =  (int)$data['total_rows'];	// 总记录数
		$data['page_size'] = (int) $data['page_size'] < 1 ? 10 : (int)$data['page_size'];	// 每页多少

		// ------------------------------------------------------------------//
		$data['total_pages'] = ceil($data['total_rows'] / $data['page_size']);

		if ($data['total_pages'] >= 1)
		{
			$data['page_begin'] = $data['page'] - floor($data['show_pages'] / 2);
			$data['r_offset'] = $data['page_begin'] >= 1 ? 0 : -$data['page_begin'] + 1;

			$data['page_end'] = $data['page'] + floor($data['show_pages'] / 2);
			$data['l_offset'] = $data['page_end'] > $data['total_pages'] ? $data['page_end'] - $data['total_pages'] : 0;

			$data['page_begin'] -= $data['l_offset'];
			$data['page_begin'] < 1 && $data['page_begin'] = 1;

			$data['page_end'] += $data['r_offset'];
			$data['page_end'] > $data['total_pages'] && $data['page_end'] = $data['total_pages'];
		}

		return $data;
	}

	/**
	 * 分页函数
	 *
	 * @param $num 信息总数
	 * @param $curr_page 当前分页
	 * @param $perpage 每页显示数
	 * @param $urlrule URL规则
	 * @param $array 需要传递的数组，用于增加额外的方法
	 * @return 分页
	 */
	public static function pages($num, $curr_page, $perpage = 20, $setpages = 6, $urlrule = '', $array = array())
	{
		$data = '';
		if ($num > $perpage) {
			$page = $setpages + 1;
			$offset = ceil($setpages / 2 - 1);
			$pages = ceil($num / $perpage);

			$from = $curr_page - $offset;
			$to = $curr_page + $offset;
			$more = 0;
			if ($page >= $pages) {
				$from = 2;
				$to = $pages - 1;
			} else {
				if ($from <= 1) {
					$to = $page - 1;
					$from = 2;
				} elseif ($to >= $pages) {
					$from = $pages - ($page - 2);
					$to = $pages - 1;
				}
				$more = 1;
			}
			$data['pages']	= $pages;
			$data['from']	= $from;
			$data['to']		= $to;
			$data['more']	= $more;
			$data['curr_page']	=	$curr_page;
			$data['pagesize'] = $perpage;
			$data['total_nums'] = $num;

			// 构造分页中间显示部分
			for ($i = $from; $i <= $to; $i++) {
				if ($i != $curr_page) {
					$arr[$i] = 0;
				} else {
					$arr[$i] = 1;
				}

			}
			$data['middle_page'] = $arr;
		}else{
            $data = array('total_nums'=>$num);
        }
		return $data;
	}

}
