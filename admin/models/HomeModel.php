<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class HomeModel extends MY_Model {


	public function __construct() {
		parent::__construct ();
		$this->load->model('commonModel');
		
	}

	

	
}
