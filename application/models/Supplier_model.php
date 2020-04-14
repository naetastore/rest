<?php

class Supplier_model extends CI_Model
{

    public function upload_statistics()
    {
        $item = $this->db->get('products')->num_rows();
        $stock = $this->stock_statistic();
        $outstock = $this->db->get_where('products', [ 'qty' => 0 ])->num_rows();
        $noready = $this->db->get_where('products', [ 'is_ready' => 0 ])->num_rows();

        return [ 
            [
                'name' => 'items',
                'count' => number_format($item, 0, '.', '.'),
                'bgcolor' => 'bg-primary'
            ], 
            [
                'name' => 'stocks',
                'count' => number_format($stock, 0, '.', '.'),
                'bgcolor' => 'bg-purple'
            ],
            [
                'name' => 'out of stocks',
                'count' => number_format($outstock, 0, '.', '.'),
                'bgcolor' => 'bg-green'
            ],
            [
                'name' => 'no ready',
                'count' => number_format($noready, 0, '.', '.'),
                'bgcolor' => 'bg-green'
            ],
        ];
    }

    public function stock_statistic()
	{
		$products = $this->db->query("SELECT `products`.`qty` FROM `products`")->result_array();
		$stock = 0;
		foreach ($products as $p) {
			$stock += $p['qty'];
		}
		return $stock;
    }

    public function products()
    {
        $product = $this->db->get('products')->result_array();
        $product = $this->rewrapp_product($product);
        return $product;
    }

    public function rewrapp_product($product)
    {
        $i=0;
        foreach($product as $p)
        {
            // replace: rupiah number formating
            $product[$i]['price'] = 'Rp. ' . rupiah_format($p['price']);
            $product[$i]['qty'] = number_format($p['qty'], 0, '.', '.'); //replace: qty

            $order = $this->db->get_where('orders', [ 'product_id' => $p['id'], 'purchased' => 1 ])->result_array();
            $_n=0;
            foreach ($order as $r) {
                $_n += $r['qty'];
            }
            $product[$i]['selled'] =  number_format($_n, 0, '.', '.');

            $product[$i]['created'] = date('d F y', $p['created']); //replace: created
            
            // add: status
            if ($p['qty'] > 0)
            {
                $product[$i]['status_label'] = "Stock";
                $product[$i]['status_classname'] = "label-success";
            }else{
                $product[$i]['status_label'] = "Out of Stock";
                $product[$i]['status_classname'] = "label-warning";
            }
            if ($p['deleted'] > 0)
            {
                $product[$i]['status_label'] = "Removed";
                $product[$i]['status_classname'] = "label-danger";
            }
            if ($p['is_ready'] < 1)
            {
                $product[$i]['status_label'] = "Not Ready";
                $product[$i]['status_classname'] = "label-default";
            }
            $i++;
        }
        return $product;
    }

    // =====

    public function category()
    {
        $general = $this->db->get('globals');
        $_data=[];
        $i=0;
        foreach ($general->result_array() as $g)
        {
            $_data[$i]['id'] = $g['id'];
            $_data[$i]['name'] = $g['name'];
            
            $category = $this->db->get_where('categories', [ 'global_id' => $g['id'] ]);
            
            $_category=[];
            $n=0;
            foreach($category->result_array() as $c)
            {
                $posts = $this->db->get_where('products', [ 'category_id' => $c['id'] ]);
                $_category[$n]['id'] = $c['id'];
                $_category[$n]['name'] = $c['name'];
                $_category[$n]['url'] = base_url('supplier/product?cid=' . $c['id'] . '&');
                $_category[$n]['description'] = $c['description'];
                $_category[$n]['product'] = $posts->num_rows();

                $_n=0;
                foreach ($posts->result_array() as $p)
                {
                    $order = $this->db->get_where('orders', [ 'product_id' => $p['id'], 'purchased' => 1 ]);
                    if ($order->num_rows() > 0 && (int)$p['qty'] == 0) $_n += 1;
                }
                $_category[$n]['selled'] = $_n;
                $_category[$n]['ratio'] = 0 . '%';
                if ($posts->num_rows() > 1) {
                    $_category[$n]['ratio'] = number_format(100/$posts->num_rows()*$_n, 0, '.', '.') . '%';
                }

                $_category[$n]['updated'] = $this->_generate_category_updated($c);
                $n++;
            }
            $_data[$i]['category'] = $_category;

            $i++;
        }
        return $_data;
    }

    private function _generate_category_updated($c)
    {
        $on_session = $this->db->query("SELECT `users`.`username`, `id` FROM `users`")->row_array();
        if ($c['updated'] < 1) $c['updated'] = $c['created'];
        return [
            'user' => [
                'username'  => $on_session['username'],
                'url'       => base_url('user/profile?uid=' . $on_session['id'] . '&')
            ],
            'date' => date('d F y', $c['updated'])
        ];
    }

    public function rewrapp_data_category($c)
    {
        $product = $this->db->get_where('products', [ 'category_id' => $c['id'] ]);
        $c['product'] = $product->num_rows();

        $c['url'] = base_url('supplier/product?cid=' . $c['id'] . '&');
        $c['updated'] = $this->_generate_category_updated($c);
        
        $_selled=0;
        foreach ($product->result_array() as $p)
        {
            $count = $this->db->get_where('orders', [ 'product_id' => $p['id'], 'purchased' => 1 ])->num_rows();
            $_selled += $count;
        }
        $c['selled'] = $_selled;
        $c['ratio'] = 0 . '%';
        if ($product->num_rows() > 0) {
            $c['ratio'] = number_format(100/$product->num_rows()*$_selled, 0, '.', '.') . '%';
        }

        $c['global_id'] = $c['global_id'];
        return $c;
    }

}
