<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Uporder extends REST_Controller
{
	private $is_admin;
	private $is_consumer;

	private $data;
	private $user;

	private $admin;

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

	public function index_post()
	{
		// parameter
		$username = $this->post('username');
		$password = $this->post('password');
		$consumer_name = $this->post('consumername');

		$on_session = get_user($username, $password);
		if (!$on_session) $this->_failedAPIResponse('Something went wrong!');
		
		$dataAccess = [
			'role_id' 	=> $on_session['role_id'],
			'status' 	=> 'in_order',
			'action' 	=> 'confirm'
		];
		if (get_order_access($dataAccess)->num_rows() < 1) $this->_failedAPIResponse('Something went wrong!');

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
		$this->_product_control($this->user['id'], $this->user['entry']);

		$this->_sendNotifications();

		// * Update entry
		$this->db->update('users', [ 'entry' => create_entry() ], [ 'id' => $this->user['id'] ]);

		$this->_successAPIResponse('Order has been confirmed!');
	}

	private function _product_control($uid, $uentry) {
		$ci = get_instance();
	
		$rules = [
			'user_id'	=> $uid,
			'purchased'	=> 1,
			'entry'		=> $uentry
		];
		$purchased = $ci->db->get_where('orders', $rules)->result_array();
	
		foreach ($purchased as $key) //status: selled
		{
			$product = $ci->db->get_where('products', [ 'id' => $key['product_id'] ])->row_array();
			$newQty = $product['qty'] - $key['qty'];

			$dataQuery['status'] = 'selled';

			$dataQuery['action'] = 'update_stock';
			if (get_product_act_allowed($dataQuery)->num_rows() > 0)
			{
				$ci->db->update('products', ['qty' => $newQty], ['id' => $product['id'] ]);
			}

			if ($newQty == 0) //status: out_of_stock
			{
				$dataQuery['status'] = 'out_of_stock';

				$dataQuery['action'] = 'update_stock';
				if (get_product_act_allowed($dataQuery)->num_rows() > 0)
				{
					$ci->db->update('products', ['qty' => $newQty], ['id' => $product['id'] ]);
				}

				$dataQuery['action'] = 'soft_delete';
				if (get_product_act_allowed($dataQuery)->num_rows() > 0)
				{
					$ci->db->update('products', ['is_ready' => 0, 'deleted' => time()], [ 'id' => $product['id'] ]);
				}
				
				$dataQuery['action'] = 'hard_delete';
				if (get_product_act_allowed($dataQuery)->num_rows() > 0)
				{
					if ($product['image'] !== 'dummy_image.jpg') unlink(FCPATH . 'src/img/product/' . $product['image']);
					$ci->db->delete('products', [ 'id' => $product['id'] ]);

					$this->subject = $key['name'] . " dihapus dari daftar barang";
					$this->message = "Kamu dapat menonaktifkan fitur ini melalui setelan.";
					$this->_sendNotif($this->is_admin);
				}
			}
			
		}
	}

	private function _sendNotifications()
	{
		$this->subject = "Pesanan telah kami konfirmasi";
		$this->message = "Jika Anda belum juga segera mendapatkan pesanan silahkan hubungi Admin " . $this->admin['name'] . " melalui Nomor telepon " . $this->admin['phonenumber'] . ".";
		$this->_sendNotif($this->is_consumer);
		
		$this->subject = "Pesanan " . $this->user['name'] . " berhasil dikonfirmasi";
		$this->message = "";
		$this->_sendNotif($this->is_admin);
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