<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Notification extends REST_Controller
{

	private $is_admin = 2;
	private $is_consumer = 3;

	private $on_session;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Notification_model', 'notif');
	}

	public function index_get()
	{
		$username = $this->get('username');
		$password = $this->get('password');

		$on_session = get_user($username, $password);
		if (!$on_session) $this->_failedAPIResponse('something went wrong!');

		$is_admin 	 = FALSE;	if ($on_session['role_id'] == $this->is_admin) $is_admin = TRUE;
		$is_consumer = FALSE; 	if ($on_session['role_id'] == $this->is_consumer) $is_consumer = TRUE;

		$control['is_admin'] 		= $is_admin;
		$control['is_consumer'] 	= $is_consumer;

		$this->on_session = $on_session;
		$notification = $this->_get_control($control);
		
		if (!$notification) {
			$this->response([
				'status' => false,
				'message' => 'no data to display'
			], REST_Controller::HTTP_NOT_FOUND);
		}

		$this->response([
			'status' => true,
			'notification' => $notification
		], REST_Controller::HTTP_OK);
	}

	private function _get_control($control)
	{
		return $this->notif->getNotification($this->on_session);
	}

	private function _failedAPIResponse($msg)
	{
		$this->response([
        	'status' => false,
        	'message' => $msg
    	], REST_Controller::HTTP_BAD_REQUEST);
	}

}