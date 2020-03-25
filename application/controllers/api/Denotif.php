<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Denotif extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Notification_model', 'notif');
	}

	public function index_get()
	{
		/** parameter
		 */ $username = $this->get('username');
		$password = $this->get('password');

		if (is_logedin($username, $password)) {
			$id = $this->get('id');

			$affected = $this->notif->deleteNotification($id);
			
			if ($affected){
				$this->response([
	            	'status' => true,
	            	'message' => 'Notification has been deleted!'
	        	], REST_Controller::HTTP_OK);
			} else {
				$this->response([
	            	'status' => false,
	            	'message' => 'Failed to delete.'
	        	], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'Something went wrong.'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}