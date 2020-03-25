<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Notification extends REST_Controller
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
			/** Inti fungsi
			 */ $notification = $this->notif->getNotification($username);
			if ($notification) {
				$this->response([
		        	'status' => true,
		        	'notification' => $notification
		    	], REST_Controller::HTTP_OK);
			} else {
				$this->response([
		        	'status' => false,
		        	'message' => 'notification not found!'
		    	], REST_Controller::HTTP_NOT_FOUND);
			}
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'something went wrong!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function index_put()
	{
		$id = $this->put('id');

		$affected = $this->notif->updateNotification($id);
		
		if ($affected){
			$this->response([
            	'status' => true,
            	'message' => 'Notification has been updated!'
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'Failed to update.'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}