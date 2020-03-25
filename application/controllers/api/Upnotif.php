<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Upnotif extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Notification_model', 'notif');
	}

	public function index_post()
	{
		$id = $this->post('id');

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