<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 
	public function __construct(){
		parent::__construct();
		$this->load->model('homeModel');
		$this->load->model('userModel');
	}
	public function index()
	{
		print_r($_SERVER);
	
		if(isset($_GET['redis'])){
			$redis = new Redis();
                	$redis->connect('127.0.0.1',6379);
                	echo $redis->ping().'<br>';
                	$redis->set('babadi','www.babadi.top');
                
                	$redis_res = $redis->get('babadi');
                	echo "<font color='green'>".$redis_res."</font>";
		}
		
		
		$this->assign('name','首页巴巴滴幸运');
		$this->display('test.html');
	}

}
