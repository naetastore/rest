<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Order extends REST_Controller
{
	private $on_session;
	private $data;
	private $product;

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
		$entry = $this->get('entry');

		$on_session = get_user($username, $password);
		if ($on_session) {
			// inti fungsi
			$this->on_session = $on_session;

			$is_admin = FALSE;
			if ($on_session['role_id'] == 1) {
				$is_admin = TRUE;
			}

			$is_member = FALSE;
			if ($on_session['role_id'] > 1) {
				$is_member = TRUE;
			}

			$get_entry = FALSE;
			if ($entry) {
				$get_entry = TRUE;
			}

			$control = [
				'is_admin' => $is_admin,
				'is_member'	=> $is_member,
				'get_entry'	=> $get_entry
			];
			$ok = $this->_get_control($control);

			if ($ok) {
				$this->response([
		        	'status' 	=> true,
		        	'order' 	=> $ok['order'],
		        	'user'		=> $ok['user']
		    	], REST_Controller::HTTP_OK);
			} else {
				$this->response([
		        	'status' => false,
		        	'message' => 'order not found!'
		    	], REST_Controller::HTTP_NOT_FOUND);
			}
		} else {
			$this->_failedAPIResponse('something went wrong!');
		}
	}
	
	private function _get_control($control)
	{
		$controls = [
			'is_admin' => $control['is_admin'],
			'is_member'	=> $control['is_member'],
			'get_entry'	=> $control['get_entry']
		];
		$options = [
			'username' => $this->get('username'),
			'entry' => $this->get('entry')
		];
		$order = $this->order->getOrder($controls, $options);

		if (!$order)
		{
			return FALSE;
		}

		$on_session = $this->on_session;
		$on_session['hasConfirm'] = FALSE;
		$on_session['hasDelete'] = FALSE;

		if ($control['is_admin'])
		{
			$on_session = [];

			if ($control['get_entry'])
			{
				$user_id = $order[0]['user_id'];
				$deleted = $order[0]['deleted'] > 0;
				$purchased = $order[0]['purchased'] > 0;
				$canceled = $deleted && !$purchased;

				$on_session = $this->db->get_where('users', ['id' => $user_id])->row_array();

				if (!$canceled && !$purchased)
				{
					$on_session['hasConfirm'] = TRUE;
					$on_session['hasDelete'] = FALSE;
				}

				if ($canceled | $purchased)
				{
					$on_session['hasDelete'] = TRUE;
				}
			}
		}

		if ($control['is_member'])
		{
			$on_session['hasDelete'] = TRUE;
		}

		return [
			'order' => $order,
			'user'	=> $on_session
		];
	}

	public function index_post()
	{
		// parameter
		$username 	= $this->post('username');
		$password 	= $this->post('password');

		$product_id = $this->post('product_id');
		$name 		= $this->post('name');
		$price 		= $this->post('price');
		$qty 		= $this->post('qty');

		$on_session = get_user($username, $password);
		if ($on_session)
		{
			if ($on_session['role_id'] == 1)
			{
				$this->_failedAPIResponse('wrong!');
			}

			if ( ! isset($product_id, $qty, $price, $name) )
			{
				$this->_failedAPIResponse('wrong!');
			}

			$_qty = (float) $qty;
			if ($_qty == 0)
			{
				$this->_failedAPIResponse('wrong!');
			}

			$data = [
				'name' 			=> $name,
				'price' 		=> $price,
				'qty' 			=> $qty,
				'product_id' 	=> $product_id,

				'user_id' 		=> $on_session['id'],
				'readed' 		=> 0,
				'entry'			=> $on_session['entry'],
				'created' 		=> time()
			];
			$this->data = $data;
			$this->on_session = $on_session;
			$this->topic = $on_session['entry'];
			$product = $this->db->get_where('products', [ 'id' => $product_id ])->row_array();

			$rules = [
				'user_id' 		=> $on_session['id'],
				'entry'			=> $on_session['entry'],
				'product_id' 	=> $product_id,
				'purchased' 	=> 0
			];
			$existed = $this->db->get_where('orders', $rules);

			if ($existed->num_rows() > 0)
			{
				$newQty = (int) $qty + (int) $existed->row_array()['qty'];
				if ($newQty > $product['qty'])
				{
					$this->product = $product;

					$this->_over($existed->row_array());
					return;
				}

				$this->data['qty'] = $newQty;

				$this->_existed($rules);
				return;
			}

			if ($existed->num_rows() == 0)
			{
				$rules = [
					'user_id' 		=> $on_session['id'],
					'entry'			=> $on_session['entry'],
					'purchased' 	=> 0
				];
				$ever = $this->db->get_where('orders', $rules)->num_rows();
				if ($ever > 0)
				{
					$this->_ever();
				}
			}

			if ($qty > $product['qty'])
			{
				$this->_over($existed->row_array());
	        	return;
			}

			$this->_post();
		}
		else
		{
			$this->_failedAPIResponse('something went wrong!');
		}
	}

	private function _ever()
	{
		$this->subject = $this->on_session['name'] . " menambah item pesanan";
		$this->message = $this->on_session['name'] . " menambahkan item " . $this->data['name'] . ", Cek Total, semua Barang yang dipesan, selengkapnya di detail.";
		$this->_sendNotif(1);
	}

	private function _existed($rules)
	{
		if ($this->order->updateOrder($this->data, $rules) > 0) {
			$this->_updated_success();
		} else {
			$this->_failedAPIResponse('failed to update!');
		}
	}

	private function _post()
	{
		if ($this->order->createOrder($this->data) > 0) {				
			$this->_created_success();
		} else {
			$this->_failedAPIResponse('failed to create new data!');
		}
	}

	private function _over($existed)
	{
		$product_qty = $this->product['qty'];
		$existed_qty = $existed['qty'];

		$overMessage = "Jumlah pesanan melebihi kuantitas barang tersedia.";

		$canDo = $product_qty - $existed_qty;

		if ($canDo > 0) {
			$overMessage .= " Kamu dapat menambah " . $canDo . " pada item " . $this->data['name'] . ".";
		} else {
			$this->subject = $this->on_session['name'] . " gagal menambahkan " . $this->data['name'] . " ke daftar pesanan";
			$this->message = "Kamu dapat menambah " . $this->data['qty'] . " stock item " . $this->data['name'] . ".";
			$this->_sendNotif(1);

			$data = [
				'product_id' 	=> $this->data['product_id'],
				'user_id'		=> $this->on_session['id']
			];
			$this->db->insert('requests', $data);
		}

		$this->_failedAPIResponse($overMessage);
	}

	private function _created_success()
	{
		activities('create', 'orders', $this->on_session['name'] . ' created new order!');

		$number2nd = $this->db->order_by('created', 'DESC')->get_where('contacts', ['user_id' => $this->on_session['id']])->row_array();

		$msg_concat = "";
		if ($number2nd) {
			$msg_concat = " atau ke " . $number2nd['phonenumber'];
		}

		$this->subject = "Pesanan berhasil kami unggah";
		$this->message = "Kami meninjau dan mengirimi pemberitahuan pesan melalui Nomor berikut " . $this->on_session["phonenumber"] . $msg_concat . " pastikan Nomor tersebut Aktif.";
		$this->_sendNotif(2);

		$this->subject = $this->on_session['name'] . " memesan";
		$this->message = "Cek Total, semua Barang yang dipesan, selengkapnya di detail.";
		$this->_sendNotif(1);

		$this->_createdAPIResponse('created new data successfuly.');
	}

	private function _updated_success()
	{
		activities('update', 'orders', $this->on_session['name'] . ' updated new order!');

		$this->subject = "Terjadi duplikasi pesanan";
		$this->message = "Kami menambahkan jumlah item pada " . $this->data['name'] . ". Cek Total, selengkapnya di detail.";
		$this->_sendNotif(2);

		$this->subject = $this->on_session['name'] . " menambah jumlah pesanan";
		$this->message = $this->on_session['name'] . " menambahkan jumlah item " . $this->data['name'] . " menjadi " . $this->data['qty'] . ", Cek Total, semua Barang yang dipesan, selengkapnya di detail.";
		$this->_sendNotif(1);

		$this->_successAPIResponse('updated successfuly!');
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
			notification($rules['subject'], $rules['message'], $role_id, $this->on_session['id'], $this->topic);
		}
	}

	private function _createdAPIResponse($msg)
	{
		$this->response([
        	'status' => true,
        	'message' => $msg
    	], REST_Controller::HTTP_CREATED);
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
