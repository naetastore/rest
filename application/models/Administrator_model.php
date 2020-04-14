<?php

class Administrator_model extends CI_Model
{

    public function dashboard_statistics()
    {
        $products = $this->db->get('products');
		$selled=0;
		foreach ($products->result_array() as $p)
		{
			$order = $this->db->get_where('orders', [ 'product_id' => $p['id'], 'purchased' => 1 ]);
			if ($order->num_rows() > 0 && (int)$p['qty'] == 0) $selled += 1;
        }
        
        $order = $this->db->group_by('entry')->get_where('orders', ['purchased' => 0, 'deleted'	=> 0])->num_rows();
        $product = $products->num_rows();
        
        return [ 
            [
                'name'  => 'Products',
                'count' => $product,
                'icon'  => 'icon-cloud-upload',
                'bgcolor' => 'bg-primary'
            ], 
            [
                'name'  => 'Orders',
                'count' => $order,
                'icon'  => 'fa fa-shopping-cart',
                'bgcolor' => 'bg-purple'
            ],
            [
                'name'  => 'Selled',
                'count' => $selled,
                'icon'  => 'fa fa-shopping-cart',
                'bgcolor' => 'bg-green'
            ]
        ];
    }

    public function activities()
    {
        $activities = $this->db->order_by('created', 'DESC')->get('activities')->result_array();

        $data=[];
        $i=0;
        foreach($activities as $a) 
        {
            $record = $this->db->get_where($a['table'], [ 'id' => $a['key'] ])->row_array();
            switch ($a['table'])
            {
                case 'users':
                    $data[$i]['description'] = $a['description'];
                    $data[$i]['name'] = $record['username'];
                    $data[$i]['url'] = base_url('user/profile?uid=' . $record['id'] . '&');
                    $data[$i]['created'] = postdate($a['created']);
                    $data[$i]['icon'] = 'fa-user';
                    $data[$i]['textcolor'] = 'text-white';
                    $data[$i]['backcolor'] = 'text-purple';
                break;

                case 'products':
                    $data[$i]['description'] = $a['description'];
                    $data[$i]['name'] = $record['name'];
                    $data[$i]['url'] = base_url('administrator/product?id=' . $record['id'] . '&');
                    $data[$i]['created'] = postdate($a['created']);
                    $data[$i]['icon'] = 'fa-file-text-o';
                    $data[$i]['textcolor'] = 'text-white';
                    $data[$i]['backcolor'] = 'text-info';
                break;

                default:
                break;
            }
            $i++;
        }
        return $data;
    }
    
    public function visitors()
    {
        $recurrent = $this->db->get_where('visitors', [ 'year' => date('y', time())])->result_array();
        $_recurrent=[
            'label' => 'Recurrent',
            'color' => '#1F92FE',
            'data'  => []
        ];
        $data=[];
        $i=0;
        foreach($recurrent as $u) {
            $data[$i] = [$recurrent[$i]['mont'], (int)$recurrent[$i]['count']];
            $i++;
        }
        $_recurrent['data'] = $data;


        $uniques = $this->db->get_where('visitors', [ 'year' => $recurrent[0]['year']-1 ])->result_array();
        $_uniques=[
            'label' => 'Uniques',
            'color' => '#768294',
            'data'  => []
        ];
        $data=[];
        $i=0;
        foreach($uniques as $s) {
            $data[$i] = [$uniques[$i]['mont'], (int)$uniques[$i]['count']];
            $i++;
        }
        $_uniques['data'] = $data;

        return [
            $_recurrent, $_uniques
        ];
    }

    // =====

    public function order($id = NULL)
    {
        if ($id !== NULL) {
            $_data = $this->_get_order_by_id($id);
        }else{
            $_data = $this->_get_all_order();
        }
        return $_data;
    }

    public function showorder($id)
    {
        $order = $this->order($id);
        $_orders = $this->db->get_where('orders', [ 'entry' => $order['entry'] ])->result_array();
        $_data=[];
        $i=0;
        foreach($_orders as $r) {
            $product = $this->db->get_where('products', [ 'id' => $r['product_id'] ])->row_array();

            $_data[$i]['id'] = $r['id'];
            $_data[$i]['price'] = rupiah_format($r['price']);
            $_data[$i]['qty'] = number_format($r['qty'], 0, '.', '.');
            $out = $product['qty'] - $r['qty'] < 1 && $order['purchased'] > 0;
            $_data[$i]['status'] = [
                'name' => ($out ? 'Out of Stock' : 'In Stock'),
                'textcolor' => ($out ? 'label-warning': 'label-success')
            ];
            $_data[$i]['total'] = rupiah_format($r['price']*$r['qty']);
            $_data[$i]['currency'] = 'Rp.';
            $_data[$i]['product_id'] = $r['product_id'];
            $i++;
        }

        return [
            'order' => $order,
            'product' => $_data
        ];
    }

    private function _get_order_by_id($id)
    {
        $order = $this->db->order_by('created', 'DESC')->get_where('orders', [ 'id' => $id ])->row_array();
        $_data=[];
        $_data['entry'] = $order['entry'];
        $_data['id'] = $order['id'];
        $_data['created'] = date('d F y', $order['created']);
        
        $consumer = $this->db->get_where('users', [ 'id' => $order['user_id'] ])->row_array();
        $_data['consumer'] = [
            'id'        => $consumer['id'],
            'name'      => $consumer['name'],
            'url'       => base_url('user/profile?uid=' . $consumer['id'] . '&'),
            'address'   => $consumer['address'],
            'phone'     => $consumer['phonenumber']
        ];

        $product = $this->db->get_where('orders', [ 'entry' => $order['entry'] ])->result_array();
        $_total_order=0;
        $_shipping=0;
        $_qty=0;
        foreach($product as $p) {
            $_total_order += $p['price']*$p['qty'];
            $_qty += $p['qty'];
        }

        $_data['currency'] = 'Rp.';
        $_data['amount'] = rupiah_format($_total_order+$_shipping);
        $_data['qty'] = number_format($_qty, 0, '.', '.');

        $_data['purchased'] = $order['purchased'];

        if ($order['purchased'] < 1 && $order['deleted'] < 1) {
            $_data['status'] = [
                'name' => 'Standby',
                'textcolor' => 'label-info'
            ];
        }
        if ($order['purchased'] < 1 && $order['deleted'] > 0) {
            $_data['status'] = [
                'name' => 'Canceled',
                'textcolor' => 'label-inverse'
            ];
        }
        if ($order['purchased'] > 0) {
            $_data['status'] = [
                'name' => 'Paid',
                'textcolor' => 'label-success'
            ];
        }
        return $_data;
    }

    private function _get_all_order()
    {
        $order = $this->db->order_by('created', 'DESC')->group_by('entry')->get('orders')->result_array();
        $_data=[];
        $i=0;
        foreach($order as $r)
        {
            $_data[$i]['entry'] = $r['entry'];
            $_data[$i]['id'] = $r['id'];
            $_data[$i]['created'] = date('d F y', $r['created']);
            
            $consumer = $this->db->get_where('users', [ 'id' => $r['user_id'] ])->row_array();
            $_data[$i]['consumer'] = [
                'name' => $consumer['name'],
                'url' => base_url('user/profile?uid=' . $consumer['id'] . '&')
            ];

            $product = $this->db->get_where('orders', [ 'entry' => $r['entry'] ])->result_array();
            $_total_order=0;
            $_shipping=0;
            $_qty=0;
            foreach($product as $p) {
                $_total_order += $p['price']*$p['qty'];
                $_qty += $p['qty'];
            }

            $_data[$i]['currency'] = 'Rp.';
            $_data[$i]['amount'] = rupiah_format($_total_order+$_shipping);
            $_data[$i]['qty'] = number_format($_qty, 0, '.', '.');


            if ($r['purchased'] < 1 && $r['deleted'] < 1) {
                $_data[$i]['status'] = [
                    'name' => 'Standby',
                    'textcolor' => 'label-info'
                ];
            }
            if ($r['purchased'] < 1 && $r['deleted'] > 0) {
                $_data[$i]['status'] = [
                    'name' => 'Canceled',
                    'textcolor' => 'label-inverse'
                ];
            }
            if ($r['purchased'] > 0) {
                $_data[$i]['status'] = [
                    'name' => 'Paid',
                    'textcolor' => 'label-success'
                ];
            }

            $i++;
        }
        return $_data;
    }

}
