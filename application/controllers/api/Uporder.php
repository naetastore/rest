<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Uporder extends REST_Controller
{
	private $is_admin = 2;
	private $is_consumer = 3;

	private $update_automatic = 0;
	private $delete_automatic = 0;

	private $data;
	private $user;

	private $admin;

	private $topic;
	private $subject;
	private $message;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Order_model', 'order');

		// $settings = $this->db->get('settings')->row_array();
		
		// $this->update_automatic = $settings['update_automatic'];
		// $this->delete_automatic = $settings['delete_automatic'];
	}

	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');
		$consumer_name = $this->post('consumername');

		$on_session = get_user($username, $password);

		if ($on_session['role_id'] != $this->is_admin) $this->_failedAPIResponse('Something went wrong!');
		
		$dataAccess = [
			'role_id' 	=> $on_session['role_id'],
			'status' 	=> 'in_order',
			'action' 	=> 'confirm'
		];
		$queryAccess = get_order_access($dataAccess);
		if ($queryAccess->num_rows() < 1) $this->_failedAPIResponse('Something went wrong!');

		$this->admin = $on_session;
		
		$user = $this->db->get_where('users', ['username' => $consumer_name])->row_array();
		$this->user = $user;

		$this->topic = $user['entry'];
		
		$data = [ 'purchased' => 1, 'readed' => 1 ];
		$rules = [
			'user_id'	=> $user['id'],
			'purchased'	=> 0,
			'entry'		=> $user['entry']
		];
		$this->_update($data, $rules);
	}

	private function _update($data, $rules)
	{
		$affected_rows = $this->order->updateOrder($data, $rules);

		if ($affected_rows > 0) {
			$this->_updated_success();
		}else{
			$this->_failedAPIResponse('Order failed to update.');
		}
	}

	private function _updated_success()
	{
		if ($this->update_automatic > 0) {
			update_automatic($this->user['id'], $this->user['entry']);
		}

		if ($this->delete_automatic > 0) {
			$rules = [
				'user_id'	=> $this->user['id'],
				'purchased'	=> 1,
				'entry'		=> $this->user['entry']
			];
			delete_automatic($rules);
		}

		$this->subject = "Pesanan telah kami konfirmasi";
		$this->message = "Jika Anda belum juga segera mendapatkan pesanan silahkan hubungi Admin " . $this->admin['name'] . " melalui Nomor telepon " . $this->admin['phonenumber'] . ".";
		$this->_sendNotif($this->is_consumer);
		
		$this->subject = "Pesanan " . $this->user['name'] . " berhasil dikonfirmasi";
		$this->message = "";
		$this->_sendNotif($this->is_admin);

		// * Update entry
		$this->db->update('users', [ 'entry' => create_entry() ], [ 'id' => $this->user['id'] ]);

		$this->_successAPIResponse('Order has been confirmed!');
	}

	private function _sendNotif($role_id)
	{
		$rules = [
			'subject' 		=> $this->subject,
			'message'		=> $this->message,
			'topic'			=> $this->topic
		];
		$existed = $this->db->get_where('notifications', $rules)->num_rows() > 0;

		if (!$existed) {
			notification($rules['subject'], $rules['message'], $role_id, $this->user['id'], $this->topic);
		}

		$this->subject = "";
		$this->message = "";
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