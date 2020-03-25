<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Deorder extends REST_Controller
{
	private $on_session;
	private $user;
	private $entry;

	private $topic;
	private $subject;
	private $message;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Order_model', 'order');
	}

	public function index_get()
	{
		// parameter
		$username = $this->get('username');
		$password = $this->get('password');
		$consumername = $this->get('consumername');
		$entry = $this->get('entry');

		$on_session = get_user($username, $password);

		if ($on_session) {
			if (!$consumername | !$entry) {
				$this->_failedAPIResponse('Order failed to delete!');
			}

			$this->on_session = $on_session;
			$user = $this->db->get_where('users', ['username' => $consumername])->row_array();
			$this->user = $user;
			$this->entry = $entry;
			$this->topic = $entry;

			$this->_delete();
		} else {
			$this->_failedAPIResponse('Something went wrong!');
		}
	}

	private function _delete()
	{
		$rules = [
			'user_id'	=> $this->user['id'],
			'entry'		=> $this->entry
		];

		$is_admin = $this->on_session['role_id'] < 2;

		if ($is_admin) {
			$affected = $this->order->deleteOrder($rules);
		} else {
			$data = [ 'readed' => 1, 'deleted' => time() ];
			$affected = $this->order->updateOrder($data, $rules);
		}

		$result = $this->db->get_where('orders', $rules)->num_rows();

		if ($affected) {
			$this->_deleted_success();
		} else {
			$this->_failedAPIResponse($this->user['id']);
		}
	}

	private function _deleted_success()
	{
		$is_admin = $this->on_session['role_id'] < 2;

		$options = [
			'user_id'	=> $this->user['id'],
			'entry'		=> $this->entry,
			'purchased' => 1
		];

		$purchased = $this->db->get_where('orders', $options)->num_rows() > 0;

		if (!$is_admin) {
			if ($purchased) {
				$msg_concat = "hapus";
			} else {
				$msg_concat = "batalkan";
			}

			$this->subject = "Pesanan berhasil kami " . $msg_concat;
			$this->message = "";
			$this->_sendNotif(2);

			if (!$purchased) {
				$this->subject = $this->user['name'] . " membatalkan pesanan";
				$this->_sendNotif(1);
			}
		}

		$this->db->update('users', [ 'entry' => create_entry() ], [ 'id' => $this->user['id'] ]);

		$this->_successAPIResponse('Order has been deleted!');
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