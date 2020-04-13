<?php 

class Statistic_model extends CI_Model {

	public function get()
	{
		return [
			'product'		=> $this->product(),
			'selled'		=> $this->selled(),
			'stock'			=> $this->stock(),
			'outofstock'	=> $this->outofstock(),
			'noready'		=> $this->noready(),
			'inOrder'		=> $this->inOrder(),
			'visitor'		=> $this->visitor(),
			'newVisitor'	=> $this->newVisitor()
		];
	}

	public function product()
	{
		return $this->db->get('products')->num_rows();
	}

	public function selled()
	{
		$products = $this->db->get('products');
		$_n=0;
		foreach ($products->result_array() as $p)
		{
			$order = $this->db->get_where('orders', [ 'product_id' => $p['id'], 'purchased' => 1 ]);
			if ($order->num_rows() > 0 && (int)$p['qty'] == 0) $_n += 1;
		}
		return $_n;
		// return $this->db->get_where('orders', ['purchased' => 1])->num_rows();
	}

	public function stock()
	{
		$products = $this->db->get('products')->result_array();
		$stock = 0;
		foreach ($products as $p) {
			$stock += $p['qty'];
		}
		return $stock;
	}

	public function outofstock()
	{
		$products = $this->db->get_where('products', [ 'qty' => 0 ])->num_rows();
		return $products;
	}

	public function noready()
	{
		$products = $this->db->get_where('products', [ 'is_ready' => 0 ])->num_rows();
		return $products;
	}

	public function inOrder()
	{
		$rules = [
			'purchased' => 0,
			'deleted'	=> 0
		];
		return $this->db->group_by('entry')->get_where('orders', $rules)->num_rows();
	}

	public function visitor()
	{
		$client = $this->db->get('clients')->result_array();
		$count = 0;

		foreach ($client as $key) {
			$inMoon = date('F y', time());
			$moon = date('F y', $key['created']);

			if ($moon != $inMoon) {
				$count += 1;
			}
		}

		return $count;
	}

	public function newVisitor()
	{
		$client = $this->db->get('clients')->result_array();
		$count = 0;

		foreach ($client as $key) {
			$inMoon = date('F y', time());
			$moon = date('F y', $key['created']);

			if ($moon == $inMoon) {
				$count += 1;
			}
		}

		return $count;
	}

}