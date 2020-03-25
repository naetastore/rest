<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Upuser extends REST_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model', 'user');
	}

	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');

		if ( is_logedin($username, $password) ) {
			$userdata = [
				'name' 		  => $this->post('name'),
				'address' 	  => $this->post('address'),
				'phonenumber' => $this->post('phonenumber')
			];

			$affected = $this->user->updateUser($userdata, $username);
			
			if ($affected){
				$this->_successAPIResponse('User data has been changed!');
			} else {
				$this->_successAPIResponse('Everything is up to date.');
			}
		} else {
			$this->_failedAPIResponse('Something went wrong!');
		}
	}

	private function _successAPIResponse($msg)
	{
		$this->response([
        	'status' => true,
        	'message' => $msg
    	], REST_Controller::HTTP_OK);
	}

	private function _failedAPIResponse($msg)
	{
		$this->response([
        	'status' => false,
        	'message' => $msg
    	], REST_Controller::HTTP_BAD_REQUEST);
	}
	
}