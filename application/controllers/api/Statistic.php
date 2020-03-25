<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Statistic extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Statistic_model', 'statistic');
	}

	public function index_get()
	{
		/** parameter
		 */ $username = $this->get('username');
		$password = $this->get('password');

		if (is_admin($username, $password)) {
			$this->response([
	        	'status' 		=> true,
	        	'products' 		=> number_format($this->statistic->product()),
	        	'stock' 		=> number_format($this->statistic->stock()),
	        	'selled' 		=> number_format($this->statistic->selled()),
	        	'inOrder'		=> number_format($this->statistic->inOrder()),
	        	'visitor'		=> number_format($this->statistic->visitor()),
	        	'newVisitor'	=> number_format($this->statistic->newVisitor())
	    	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'something went wrong!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}