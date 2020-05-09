<?php

class Administrator_model extends CI_Model
{
	
	public function basic_statistics()
	{
		$products = $this->db->query("SELECT `products`.`id`,`qty` FROM `products`");

		$selled=0;
		foreach ($products->result_array() as $p) {
			$this->db->where('product_id', $p['id']);
			$this->db->where('purchased >', 0);
			$order = $this->db->get('orders');
			if ($order->num_rows() > 0 && (int)$p['qty'] == 0) $selled += 1;
        }
		
		$this->db->where('purchased <', 1);
		$this->db->where('deleted', 0);
        $order = $this->db->group_by('entry')->get('orders')->num_rows();
        $product = $products->num_rows();

        return [
        	'product' => $product,
        	'order' => $order,
        	'selled' => $selled
        ];
	}

	public function product_statistics()
	{
        $stock = $this->_stock_statistic();
        $outstock = $this->db->get_where('products', [ 'qty' => 0 ])->num_rows();
        $noready = $this->db->get_where('products', [ 'is_ready' => 0 ])->num_rows();

        return [
        	'stocks' => number_format($stock, 0, '.', '.'),
        	'out_of_stock' => number_format($outstock, 0, '.', '.'),
        	'not_ready' => number_format($noready, 0, '.', '.')
        ];
	}

	private function _stock_statistic()
	{
		$products = $this->db->query("SELECT `products`.`qty` FROM `products`")->result_array();
		
		$stock = 0;
		foreach ($products as $p) {
			$stock += $p['qty'];
		}
		
		return $stock;
    }
}
