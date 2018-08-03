<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

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
		$this->load->model('api/loginModel');
	}
	public function getWxSessionKey()
	{	
		$param = $this->input->post();
		$keyInfo = $this->loginModel->getWxSessionKey($param);
		
		Util::echoFormatReturn($code = 200, $keyInfo, $message = '请求成功', $exit = 0);
	}

	public function index2(){

		echo 'inext2';
	}
}
