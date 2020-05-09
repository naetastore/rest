<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Orders extends REST_Controller {

    protected $user;

    function __construct()
    {
        parent::__construct();

        $this->load->helper('client');

        $this->user = basic_auth();
    }

    public function index_get()
    {
        if ($this->get('delete')) {
            $this->_delete();
            return;
        }

        $entry = $this->get('entry');

        $this->load->helper('andi');

        if ($entry === NULL)
        {
            if ($this->user['role_id'] == 1) {
                $rules = [];
            }

            if ($this->user['role_id'] == 2) {
                $rules = ['user_id' => $this->user['id'], 'deleted' => 0];
            }

            $orders = $this->db->order_by('created', 'DESC')->group_by('entry')->get_where('orders', $rules)->result_array();

            if ($this->get('all')) {

                if ($orders) {
                    $i=0;
                    foreach ($orders as $key) {
                        $orders[$i] = $this->_get_details($key['entry']);
                        $i++;
                    }
                    $this->response($orders, REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No orders were found'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }

            }else{
                if ($orders)
                {
                	$i=0;
                	foreach ($orders as $key) {
                		$orders[$i]['created'] = postdate($key['created']);
                		$orders[$i]['user_name'] = $this->db->get_where('users', ['id' => $key['user_id']])->row_array()['username'];
                		$i++;
                	}

                    $this->response($orders, REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No orders were found'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }else{
            $orders = $this->_get_details($entry);

            if ($orders) {
                $this->response($orders, REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Orders could not be found'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }

    }

    private function _get_details($entry)
    {
        $orders = $this->db->get_where('orders', ['entry' => $entry])->result_array();

        if (!empty($orders))
        {
            $i=0;
            foreach ($orders as $key) {
                $orders[$i]['created'] = postdate($key['created']);
                
                $queryProduct = "SELECT `products`.`image` FROM products WHERE `products`.`id` = {$key['product_id']}";
                $product = $this->db->query($queryProduct)->row_array();

                $orders[$i]['image'] = base_url('src/img/product/' . $product['image']);

                $i++;
            }

            $user_access = $this->_generateAccess([
                'orders' => $orders,
                'user' => $this->user
            ]);

            $orders = $this->_generateDetails($orders);

            $details = [
                'order' => $orders,
                'useraccess' => $user_access
            ];

            return $details;
        }
        else
        {
            return FALSE;
        }
    }

    public function index_post()
    {
        if ($this->post('update')) {
            $this->_update();
            return;
        }

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('notification');

        if (!$this->user['entry']) {
            $this->response([
                'status' => FALSE,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $order_maxhour = 24;
        $in_order = $this->db->order_by('created', 'ASC')->group_by('entry')->get_where('orders', ['entry' => $this->user['entry']]);
        if ($in_order->num_rows() > 0 && time() - $in_order->row_array()['created'] > (60*60*$order_maxhour)) {
            $message = [
                'status' => FALSE,
                'message' => "Waktu diperbolehkan menambah jumlah item dibatasi ". $order_maxhour ." jam,\r\nmohon tunggu sampai kami mengkonfirmasi pesanan."
            ];
    
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|integer');
        $this->form_validation->set_rules('qty', 'Qty', 'required|integer');
        $this->form_validation->set_rules('product_id', 'ID', 'required|integer');

        if (!$this->form_validation->run())
        {
            $message = [
                'status' => FALSE,
                'message' => 'Data not valid'
            ];
    
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'name'          => $this->post('name'),
            'price'         => $this->post('price'),
            'qty'           => $this->post('qty'),
            'product_id'    => $this->post('product_id'),
            'user_id'       => $this->user['id'],
            'entry'         => $this->user['entry'],
            'created'       => time()
        ];

        $rules = ['product_id' => $data['product_id'], 'entry' => $data['entry'], 'user_id' => $data['user_id']];
        $existed = $this->db->get_where('orders', $rules);

        $admin = $this->db->get_where('users', ['role_id' => 1])->row_array();

        if ($existed->num_rows() > 0) {
            $existed = $existed->row_array();

            $data['qty'] = $existed['qty'] + $data['qty'];
            $data['created'] = $existed['created'];
            $data['updated'] = time();
            
            $this->db->update('orders', $data, [
                'product_id' => $data['product_id'], 'user_id' => $data['user_id'], 'entry' => $data['entry']
            ]);

            send_notification([
                'subject' => 'Terjadi duplikasi pesanan',
                'message' => "Kami menambahkan jumlah item pada {$data['name']}. Cek Total, selengkapnya di detail.",
                'user' => $this->user
            ]);

            send_notification([
                'subject' => "{$this->user['name']} menambah jumlah item pesanan",
                'message' => "Jumlah item {$data['name']} yang dipesan sekarang menjadi {$data['qty']}.",
                'user' => $admin
            ]);

            $queryOrder = "SELECT `orders`.`id`,`name`,`price`,`qty`,`product_id`
                FROM `orders`
                WHERE `orders`.`user_id` = {$data['user_id']}";
            $order = $this->db->query($queryOrder)->row_array();

            $message = [
                'order' => $order,
                'message' => 'Updated a resource'
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }else{
            $this->db->insert('orders', $data);

            send_notification([
                'subject' => 'Pesanan berhasil kami unggah',
                'message' => "Kami meninjau dan mengirimi pemberitahuan pesan melalui Nomor berikut {$this->user['phonenumber']} pastikan Nomor tersebut Aktif.",
                'user' => $this->user
            ]);

            send_notification([
                'subject' => "{$this->user['name']} membuat pesanan",
                'message' => 'Cek Total, semua Barang yang dipesan, selengkapnya di detail.',
                'user' => $admin
            ]);

            $queryOrder = "SELECT `orders`.`id`,`name`,`price`,`qty`,`product_id`
                FROM `orders`
                WHERE `orders`.`user_id` = {$data['user_id']}";
            $order = $this->db->query($queryOrder)->row_array();
            
            $message = [
                'order' => $order,
                'message' => 'Added a resource'
            ];
    
            $this->set_response($message, REST_Controller::HTTP_CREATED);
        }
    }

    private function _update()
    {
    	$entry = $this->post('entry');

        $this->load->helper('user');
        $this->load->helper('notification');

        if (!isset($entry)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Provide an entry'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $orders = $this->db->get_where('orders', ['entry' => $entry])->result_array();

        if (!$orders) {
            $this->response([
                'status' => FALSE,
                'message' => 'Orders could not be found'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user_access = $this->_generateAccess(['orders' => $orders, 'user' => $this->user]);

        if ($user_access['hasConfirm']) {
            $this->db->update('orders', ['purchased' => time(), 'readed' => 1], ['entry' => $entry]);

            if ($this->db->affected_rows() > 0) {
                // update stock
                $orders = $this->db->get_where('orders', ['entry' => $entry])->result_array();
                foreach ($orders as $key) {
                    $queryProduct = "SELECT `products`.`qty` FROM `products` WHERE `products`.`id` = {$key['product_id']}";
                    $product = $this->db->query($queryProduct)->row_array();

                    $newQty = (int)$product['qty'] - (int)$key['qty'];
                    $this->db->update('products', ['qty' => $newQty], ['id' => $key['product_id'] ]);
                }

                $consumer = $this->db->get_where('users', ['id' => $orders[0]['user_id']])->row_array();

                send_notification([
                    'subject' => 'Pesanan berhasil kami konfirmasi',
                    'message' => '',
                    'user' => $consumer
                ]);

                $this->db->update('users', ['entry' => create_entry()], ['entry' => $entry]);

                $this->response([
                    'entry' => $entry,
                    'message' => 'Updated a resource'
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Failed to updated'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    private function _delete()
    {
        $entry = $this->get('entry');

        $this->load->helper('user');
        $this->load->helper('notification');

        if (!isset($entry)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Provide an entry'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $rules = ['entry' => $entry];

        $orders = $this->db->get_where('orders', $rules)->result_array();

        if (!$orders) {
            $this->response([
                'status' => FALSE,
                'message' => 'Orders could not be found'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $user_access = $this->_generateAccess(['orders' => $orders, 'user' => $this->user]);

        if ($user_access['hasCancel']) {
            $this->db->update('orders', ['readed' => 1, 'canceled' => time()], $rules);

            $this->db->update('users', ['entry' => create_entry()], ['entry' => $entry]);

            send_notification([
                'subject' => 'Pesanan berhasil kami batalkan',
                'message' => '',
                'user' => $this->user
            ]);

            $admin = $this->db->get_where('users', ['role_id' => 1])->row_array();
            send_notification([
                'subject' => "{$this->user['name']} membatalkan pesanan",
                'message' => '',
                'user' => $admin
            ]);

            $this->response([
                'entry' => $entry,
                'message' => 'Canceled'
            ], REST_Controller::HTTP_OK);
        }
        
        if ($user_access['soft_delete']) {
            $this->db->update('orders', ['readed'  => 1, 'deleted' => time()], $rules);

            $this->_deleted($entry);
        }else{
            if ($user_access['hard_delete']) {
                $this->db->delete('orders', $rules);

                $this->_deleted($entry);
            }
        }

    }

    private function _deleted($entry)
    {
        $message = [
            'entry' => $entry,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    private function _generateAccess($array)
    {
        $orders = $array['orders'];
        $user = $array['user'];

        $this->load->helper('order');

        // default
        $user_access['hasDelete'] = FALSE;
        $user_access['hasConfirm'] = FALSE;
        $user_access['hasCancel'] = FALSE;
        // for backend
        $user_access['soft_delete'] = FALSE;
        $user_access['hard_delete'] = FALSE;

        $canceled = $orders[0]['canceled'] > 0 && $orders[0]['purchased'] < 1;

        if (!$canceled && $orders[0]['purchased'] < 1) // status: in_order
        {
            if (get_access([
                'status' => 'in_order',
                'action' => 'cancel',
                'role_id' => $user['role_id']
            ])->num_rows() > 0) {
                $user_access['hasCancel'] = TRUE;
            }else{
                if (get_access([
                    'status' => 'in_order',
                    'action' => 'confirm',
                    'role_id' => $user['role_id']
                ])->num_rows() > 0) {
                    $user_access['hasConfirm'] = TRUE;
                }
            }
        }

        if ($canceled) // status: canceled
        {
            if (get_access([
                'status' => 'canceled',
                'action' => 'hard_delete',
                'role_id' => $user['role_id']
            ])->num_rows() > 0) {
                $user_access['hasDelete'] = TRUE;

                // for backend
                $user_access['hard_delete'] = TRUE;
            }else{
                if (get_access([
                    'status' => 'canceled',
                    'action' => 'soft_delete',
                    'role_id' => $user['role_id']
                ])->num_rows() > 0) {
                    $user_access['hasDelete'] = TRUE;

                    // for backend
                    $user_access['soft_delete'] = TRUE;
                }
            }
        }

        if ($orders[0]['purchased'] > 0) //status: purchased
        {
            if (get_access([
                'status' => 'purchased',
                'action' => 'hard_delete',
                'role_id' => $user['role_id']
            ])->num_rows() > 0) {
                $user_access['hasDelete'] = TRUE;

                // for backend
                $user_access['hard_delete'] = TRUE;
            }else{
                if (get_access([
                    'status' => 'purchased',
                    'action' => 'soft_delete',
                    'role_id' => $user['role_id']
                ])->num_rows() > 0) {
                    $user_access['hasDelete'] = TRUE;

                    // for backend
                    $user_access['soft_delete'] = TRUE;
                }
            }
        }

        if ($orders[0]['deleted'] > 0 && $orders[0]['purchased'] > 0) //status: purchased_deleted
        {
            if (get_access([
                'status' => 'purchased_deleted',
                'action' => 'hard_delete',
                'role_id' => $user['role_id']
            ])->num_rows() > 0) {
                $user_access['hasDelete'] = TRUE;

                // for backend
                $user_access['hard_delete'] = TRUE;
            }else{
                if (get_access([
                    'status' => 'purchased_deleted',
                    'action' => 'soft_delete',
                    'role_id' => $user['role_id']
                ])->num_rows() > 0) {
                    $user_access['hasDelete'] = TRUE;

                    // for backend
                    $user_access['soft_delete'] = TRUE;
                }
            }
        }

        return $user_access;
    }

    private function _generateDetails($orders)
    {
        $consumer = $this->db->get_where('users', ['id' => $orders[0]['user_id']])->row_array();
        $product = $orders;

        $subtotal=0;
        foreach ($product as $p) {
            $subtotal += $p['price'] * $p['qty'];
        }

        $shipping=0;
        $total=$subtotal+$shipping;

        $i=0;
        foreach ($product as $p) {
            $product[$i]['qty'] = number_format($p['qty'], 0, '.', '.');

            $queryProduct = "SELECT `products`.`qty`,`price` FROM `products` WHERE `products`.`id` = {$p['product_id']}";
            $pOrdered = $this->db->query($queryProduct)->row_array();

            // replace > price
            $product[$i]['price'] = number_format($pOrdered['price'], 0, '.', '.');

            $count = $pOrdered['qty'] / $p['qty'];

            if ($count >= $p['qty']) {
            	$product[$i]['status'] = 'Dalam Stock';
            }else{
            	$product[$i]['status'] = 'Keluar Stock';
            }

            $product[$i]['stock'] = $pOrdered['qty'];
            $i++;
        }

        $subtotal = number_format($subtotal, 0, '.', '.');
        $total = number_format($total, 0, '.', '.');

        return [
            'consumer' => $consumer,
            'summary' => [
                'product' => $product,
                'detail' => [
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total
                ]
            ]
        ];
    }

}
