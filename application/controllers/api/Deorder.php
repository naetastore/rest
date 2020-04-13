<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Deorder extends REST_Controller
{
	private $is_superuser = 1;
	private $is_admin = 2;
	private $is_consumer = 3;

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

		if (!$on_session) $this->_failedAPIResponse('Something went wrong!');
		if (!$consumername | !$entry) $this->_failedAPIResponse('Something went wrong!');

		$this->on_session = $on_session;
		$user = $this->db->get_where('users', ['username' => $consumername])->row_array();
		$this->user = $user;
		$this->entry = $entry;
		$this->topic = $entry;

		$this->_delete();
	}

	private function _delete()
	{
		$rules = [
			'user_id'	=> $this->user['id'],
			'entry'		=> $this->entry
		];
		$order = $this->db->get_where('orders', $rules)->row_array();
		
		$canceled = $order['canceled'] > 0 && $order['purchased'] < 1;
		if (!$canceled && $order['purchased'] < 1) // status: in_order
		{
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'in_order',
				'action' 	=> 'cancel'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$data = ['readed' => 1, 'canceled' => time()];
				$affected = $this->order->updateOrder($data, $rules);
			}
		}
		if ($canceled) // status: canceled
		{
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'canceled',
				'action' 	=> 'hard_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'canceled',
				'action' 	=> 'soft_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->updateOrder([
					'readed'  => 1,
					'deleted' => time()
				], $rules);
			}
		}
		if ($order['purchased'] > 0) //status: purchased
		{
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased',
				'action' 	=> 'hard_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased',
				'action' 	=> 'soft_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->updateOrder([
					'readed'  => 1,
					'deleted' => time()
				], $rules);
			}
		}
		if ($order['deleted'] > 0 && $order['purchased'] > 0) //status: purchased_deleted
		{
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased_deleted',
				'action' 	=> 'hard_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased_deleted',
				'action' 	=> 'soft_delete'
			];
			$queryAccess = get_order_access($dataAccess);
			if ($queryAccess->num_rows() > 0)
			{
				$affected = $this->order->updateOrder([
					'readed'  => 1,
					'deleted' => time()
				], $rules);
			}
		}

		if ($affected) {
			$this->_deleted_success();
		} else {
			$this->_failedAPIResponse('Failed to delete!');
		}
	}

	private function _deleted_success()
	{
		$options = [
			'user_id'	=> $this->user['id'],
			'entry'		=> $this->entry,
			'purchased' => 1
		];

		$purchased = $this->db->get_where('orders', $options)->num_rows() > 0;

		$role_id = $this->on_session['role_id'];
		if ($role_id == $this->is_consumer && !$purchased)
		{
			$this->subject = "Pesanan berhasil kami batalkan";
			$this->message = "";
			$this->_sendNotif($this->is_consumer);

			$this->subject = $this->user['name'] . " membatalkan pesanan";
			$this->_sendNotif($this->is_admin);
		}

		$this->db->update('users', ['entry' => create_entry()], [ 'id' => $this->user['id'] ]);

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