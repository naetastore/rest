<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Uporder extends REST_Controller
{
	private $update_automatic = 0;
	private $delete_automatic = 0;

	private $data;
	private $user;
	private $rules;

	private $on_session;

	private $topic;
	private $subject;
	private $message;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Order_model', 'order');

		$settings = $this->db->get('settings')->row_array();
		
		$this->update_automatic = $settings['update_automatic'];
		$this->delete_automatic = $settings['delete_automatic'];
	}

	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');
		$consumername = $this->post('consumername');

		$on_session = get_user($username, $password);
		$is_admin = $on_session['role_id'] < 2;

		if ($is_admin) {
			$this->on_session = $on_session;
			
			$user = $this->db->get_where('users', ['username' => $consumername])->row_array();
			$this->user = $user;

			$this->topic = $user['entry'];
			
			$this->data = [ 'purchased' => 1, 'readed' => 1 ];
			$this->rules = [
				'user_id'	=> $user['id'],
				'purchased'	=> 0,
				'entry'		=> $user['entry']
			];

			$this->_update();
		} else {
			$this->_failedAPIResponse('Something went wrong!.');
		}
	}

	private function _update()
	{
		$affected = $this->order->updateOrder($this->data, $this->rules);

		if ($affected) {
			$this->_updated_success();
		} else {
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

		$this->subject = "Pesanan sudah kami konfirmasi";
		$this->message = "Jika kamu belum juga segera mendapatkan pesanan silahkan hubungi Admin " . $this->on_session['name'] . " ke Nomor " . $this->on_session['phonenumber'] . ".";
		$this->_sendNotif(2);

		$this->subject = "Pesanan " . $this->user['name'] . " berhasil dikonfirmasi";
		$this->message = "";
		$this->_sendNotif(1);

		// Update with new Entry
		$this->db->update('users', [ 'entry' => create_entry() ], [ 'id' => $this->user['id'] ]);

		$this->_successAPIResponse('Order has been updated!');
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