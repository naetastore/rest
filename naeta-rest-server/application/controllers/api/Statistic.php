<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Statistic extends REST_Controller
{

	private $is_admin;
	private $is_consumer;

	public function __construct()
	{
		parent::__construct();
		$this->is_consumer = get_api_setting('default_role');
		$this->is_admin = get_api_setting('admin_notification');

		$this->load->model('Statistic_model', 'statistic');
	}

	public function index_get()
	{
		$username = $this->get('username');
		$password = $this->get('password');

		$on_session = $this->db->get_where('users', ['username' => $username])->row_array();
		if ($on_session['role_id'] != $this->is_admin) {
			$this->response([
            	'status' => false,
            	'message' => 'something went wrong!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
		
		$this->response([
			'status' 		=> true,
			'products' 		=> number_format($this->statistic->product()),
			'stock' 		=> number_format($this->statistic->stock()),
			'selled' 		=> number_format($this->statistic->selled()),
			'inOrder'		=> number_format($this->statistic->inOrder()),
			'visitor'		=> number_format($this->statistic->visitor()),
			'newVisitor'	=> number_format($this->statistic->newVisitor())
		], REST_Controller::HTTP_OK);
	}

}