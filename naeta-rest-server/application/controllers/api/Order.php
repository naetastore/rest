<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Order extends REST_Controller
{
	private $is_admin;
	private $is_consumer;

	private $on_session;
	private $data;
	private $product;

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
		$entry = $this->get('entry');

		$on_session = get_user($username, $password);
		if (!$on_session) $this->_failedAPIResponse('something went wrong!');

		$this->on_session = $on_session;

		$is_admin 	 = FALSE;	if ($on_session['role_id'] == $this->is_admin) $is_admin = TRUE;
		$is_consumer = FALSE; 	if ($on_session['role_id'] == $this->is_consumer) $is_consumer = TRUE;
		$get_entry 	 = FALSE; 	if ($entry) $get_entry = TRUE;

		$control['is_admin'] 	= $is_admin;
		$control['is_consumer'] = $is_consumer;
		$control['get_entry'] 	= $get_entry;

		$response = $this->_get_control($control);

		if (!$response) {
			$this->response([
				'status' => false,
				'message' => 'no data to display'
			], REST_Controller::HTTP_NOT_FOUND);
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}

	private function _get_control($control)
	{
		$options = [
			'user_id' 	=> $this->on_session['id'],
			'entry'		=> $this->get('entry')
		];
		$order = $this->order->getOrder($control, $options);

		if (!$order) return FALSE;

		//  ==USER ACCESS==
		if ($control['get_entry'])
		{
			$dataAccess = ['role_id' => $this->on_session['role_id']];
			// default
			$user_access['hasDelete'] = FALSE;
			$user_access['hasConfirm'] = FALSE;
			$user_access['hasCancel'] = FALSE;

			$canceled = $order['product'][0]['canceled'] > 0 && $order['product'][0]['purchased'] < 1;

			if (!$canceled && $order['product'][0]['purchased'] < 1) // status: in_order
			{
				$dataAccess['status'] = 'in_order';

				$dataAccess['action'] = 'confirm';
				if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasConfirm'] = TRUE; //*

				$dataAccess['action'] = 'cancel';
				if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasCancel'] = TRUE;
			}
			if ($canceled) // status: canceled
			{
				$dataAccess['status'] = 'canceled';

				$dataAccess['action'] = 'hard_delete';
				if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				
				if (get_order_access($dataAccess)->num_rows() < 1) {
					$dataAccess['action'] = 'soft_delete';
					if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				}
			}
			if ($order['product'][0]['purchased'] > 0) //status: purchased
			{
				$dataAccess['status'] = 'purchased';

				$dataAccess['action'] = 'hard_delete';
				if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				
				if (get_order_access($dataAccess)->num_rows() < 1) {
					$dataAccess['action'] = 'soft_delete';
					if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				}
			}
			if ($order['product'][0]['deleted'] > 0 && $order['product'][0]['purchased'] > 0) //status: purchased_deleted
			{
				$dataAccess['status'] = 'purchased_deleted';

				$dataAccess['action'] = 'hard_delete';
				if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				
				if (get_order_access($dataAccess)->num_rows() < 1) {
					$dataAccess['action'] = 'soft_delete';
					if (get_order_access($dataAccess)->num_rows() > 0) $user_access['hasDelete'] = TRUE;
				}
			}
		}

		//  ==UI CONFIG==
		$dataConfig['role_id'] = $this->on_session['role_id'];
		// default
		$ui_config['hasName'] = TRUE;
		$ui_config['hasAvatar'] = TRUE;

		$dataConfig['ui'] = 'name';
		if (get_ui_config($dataConfig)->num_rows() > 0) $ui_config['hasName']	= FALSE;
		
		$dataConfig['ui'] = 'avatar';
		if (get_ui_config($dataConfig)->num_rows() > 0) $ui_config['hasAvatar'] = FALSE;

		//  ==CONSUMER DETAIL==
		if ($control['get_entry'] && $control['is_consumer'])
		{
			$on_session = $this->on_session;
			$on_session = rewrapp($on_session);
		}
		if ($control['get_entry'] && $control['is_admin']) {
			$user_id = $order['product'][0]['user_id'];
			$on_session = $this->db->get_where('users', ['id' => $user_id])->row_array();
			$on_session = rewrapp($on_session);
		}

		// clear data
		if ($control['get_entry'])
		{
			$_order['consumer'] = $on_session;
			$_order['summary'] 	= $order;

			$response = [
				'status' 		=> true,
				'order' 		=> $_order,
				'useraccess' 	=> $user_access
			];
		}else{
			$response = [
				'status' 	=> true,
				'order' 	=> $order,
				'uiconfig'	=> $ui_config
			];
		}
		return $response;
	}

	public function index_post()
	{
		$username 	= $this->post('username'); //*
		$password 	= $this->post('password'); //*

		$product_id = $this->post('product_id');
		$name 		= $this->post('name');
		$price 		= $this->post('price');
		$qty 		= $this->post('qty');

		$on_session = get_user($username, $password);
		if (!$on_session) $this->_failedAPIResponse('something went wrong!');

		$order_maxhour = get_api_setting('order_maxhour');
		$in_order = $this->db->order_by('created', 'ASC')->get_where('orders', [ 'entry' => $on_session['entry'] ]);
		if ($in_order->num_rows() > 0 && time() - $in_order->row_array()['created'] > (60*60*$order_maxhour)) {
			$this->_failedAPIResponse("Waktu diperbolehkan menambah jumlah item dibatasi ". $order_maxhour ." jam,\r\nmohon tunggu sampai kami mengkonfirmasi pesanan.");
		}

		$data = [
			'product_id' 	=> $product_id,
			'name' 			=> $name,
			'price' 		=> $price,
			'qty' 			=> $qty,

			'user_id' 		=> $on_session['id'],
			'readed' 		=> 0,
			'entry'			=> $on_session['entry']
		];

		$this->_productvalidity($data);

		$this->data 		= $data;
		$this->on_session 	= $on_session;
		$this->topic 		= $on_session['entry'];

		$this->_existchecks($product_id);

		$this->_order();
	}

	private function _productvalidity($data)
	{
		if (!isset($data['product_id'], $data['qty'], $data['price'], $data['name'])) $this->_failedAPIResponse('No containing data!');
		
		$_qty = (int) $data['qty'];
		if ($_qty < 1) $this->_failedAPIResponse('The product qty is ' . $_qty . ', Please check you product');

		$_price = (int) $data['price'];
		if ($_price < 1) $this->_failedAPIResponse('The product price is ' . $_price . ', Please check you product');
	}

	private function _existchecks($product_id)
	{
		$product = $this->db->get_where('products', [ 'id' => $product_id ])->row_array();

		$rules = [
			'user_id' 		=> $this->on_session['id'],
			'entry'			=> $this->on_session['entry'],
			'product_id' 	=> $product_id,
			'purchased' 	=> 0
		];
		$existed = $this->db->get_where('orders', $rules);

		if ($existed->num_rows() > 0)
		{
			$newQty = (int) $this->data['qty'] + (int) $existed->row_array()['qty'];
			if ($newQty > $product['qty'])
			{
				$this->product = $product;
				$this->_over($existed->row_array());
				return;
			}

			$this->data['qty'] = $newQty;
			$this->_update($rules);
			return;
		}

		if ($this->data['qty'] > $product['qty']) $this->_over($existed->row_array());
	}

	private function _update($rules)
	{
		$this->data['updated'] = time();
		$this->order->updateOrder($this->data, $rules) > 0
		? $this->_updated_success()
		: $this->_failedAPIResponse('failed to update!');
	}

	private function _order()
	{
		$this->data['created'] = time();
		$this->order->createOrder($this->data) > 0
		? $this->_created_success()
		: $this->_failedAPIResponse('failed to create new data!');
	}


	private function _over($existed)
	{
		$product_qty = $this->product['qty'];
		$existed_qty = $existed['qty'];

		$overMessage = "Jumlah item melebihi kuantitas barang tersedia.";

		$canDo = $product_qty - $existed_qty;
		if ($canDo > 0) {
			$overMessage .= " Kamu dapat menambah " . $canDo . " pada item " . $this->data['name'] . ".";
		}else{
			$this->subject = $this->on_session['name'] . " gagal menambahkan " . $this->data['name'] . " ke daftar pesanan";
			$this->message = "Kamu dapat menambah " . $this->data['qty'] . " stock item " . $this->data['name'] . ".";
			$this->_sendNotif($this->is_admin);

			$data = [
				'product_id' 	=> $this->data['product_id'],
				'user_id'		=> $this->on_session['id']
			];
			$this->db->insert('requests', $data); //save request
		}

		$this->_failedAPIResponse($overMessage);
	}

	private function _created_success()
	{
		$number2nd = $this->db->order_by('created', 'DESC')->get_where('contacts', ['user_id' => $this->on_session['id']])->row_array();

		$msg_concat = "";
		if ($number2nd && $number2nd['phonenumber'] !== $this->on_session['phonenumber']) $msg_concat = " atau ke " . $number2nd['phonenumber'];

		$this->subject = "Pesanan berhasil kami unggah";
		$this->message = "Kami meninjau dan mengirimi pemberitahuan pesan melalui Nomor berikut " . $this->on_session["phonenumber"] . $msg_concat . " pastikan Nomor tersebut Aktif.";
		$this->_sendNotif($this->is_consumer);

		$this->subject = $this->on_session['name'] . " membuat pesanan";
		$this->message = "Cek Total, semua Barang yang dipesan, selengkapnya di detail.";
		$this->_sendNotif($this->is_admin);

		$this->_createdAPIResponse('created new order successfuly.');
	}

	private function _updated_success()
	{
		$this->subject = "Terjadi duplikasi pesanan";
		$this->message = "Kami menambahkan jumlah item pada " . $this->data['name'] . ". Cek Total, selengkapnya di detail.";
		$this->_sendNotif($this->is_consumer);
		
		$this->subject = $this->on_session['name'] . " menambah jumlah item pesanan";
		$this->message = $this->on_session['name'] . " menambahkan jumlah item " . $this->data['name'] . " menjadi " . $this->data['qty'] . ", Cek Total, semua Barang yang dipesan, selengkapnya di detail.";
		$this->_sendNotif($this->is_admin);

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

		$this->subject = "";
		$this->message = "";
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
