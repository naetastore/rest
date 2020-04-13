<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Settings extends REST_Controller
{

	private $is_admin = 2;
	private $is_consumer = 3;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Settings_model', 'settings');
	}

	public function index_get()
	{
		// parameter
		$username = $this->get('username');
		$password = $this->get('password');

		$on_session = get_user($username, $password);
		if ($on_session['role_id'] != $this->is_admin) {
			$this->response([
            	'status'  => false,
            	'message' => 'Something went wrong!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}

		$settings = $this->db->get('settings')->row_array();
		
		if ($settings){
			$this->response([
				'status'  => true,
				'settings' => $settings
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status'  => false,
				'message' => 'Settings not found.'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	
	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');

		$on_session = get_user($username, $password);
		if ($on_session['role_id'] != $this->is_admin) {
			$this->response([
            	'status'  => false,
            	'message' => 'Something went wrong!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}

		$data = [
			'update_otomatic' 	=> $this->post('update_otomatic'),
			'delete_otomatic' 	=> $this->post('delete_otomatic')
		];
		$affected_rows = $this->settings->updateSettings($data);
		
		if ($affected_rows > 1){
			$this->response([
				'status'  => true,
				'message' => 'Settings has been changed!'
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status'  => true,
				'message' => 'Everything is up to date.'
			], REST_Controller::HTTP_OK);
		}

	}
	
}