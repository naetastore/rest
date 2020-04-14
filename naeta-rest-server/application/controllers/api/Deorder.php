<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Deorder extends REST_Controller
{
	private $is_admin;
	private $is_consumer;

	private $on_session;
	private $user;
	private $entry;

	private $topic;
	private $subject;
	private $message;

	public function __construct()
	{
		parent::__construct();
		$this->is_consumer = get_api_setting('default_role');
		$this->is_admin = get_api_setting('admin_notification');
		
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
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$data = ['readed' => 1, 'canceled' => time()];
				$affected_rows = $this->order->updateOrder($data, $rules);
				if ($affected_rows > 0) $this->_canceled_success();
			}
		}
		if ($canceled) // status: canceled
		{
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'canceled',
				'action' 	=> 'hard_delete'
			];
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'canceled',
				'action' 	=> 'soft_delete'
			];
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->updateOrder([
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
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased',
				'action' 	=> 'soft_delete'
			];
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->updateOrder([
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
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->deleteOrder($rules);
			}
			
			$dataAccess = [
				'role_id' 	=> $this->on_session['role_id'],
				'status' 	=> 'purchased_deleted',
				'action' 	=> 'soft_delete'
			];
			if (get_order_access($dataAccess)->num_rows() > 0)
			{
				$this->order->updateOrder([
					'readed'  => 1,
					'deleted' => time()
				], $rules);
			}
		}

		$this->_successAPIResponse('Deleted successfuly.');
	}

	private function _canceled_success()
	{
		$this->subject = "Pesanan berhasil kami batalkan";
		$this->message = "";
		$this->_sendNotif($this->is_consumer);

		$this->subject = $this->user['name'] . " membatalkan pesanan";
		$this->_sendNotif($this->is_admin);

		$this->db->update('users', ['entry' => create_entry()], [ 'id' => $this->user['id'] ]);
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