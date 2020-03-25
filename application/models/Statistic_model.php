<?php 

class Statistic_model extends CI_Model {

	public function product()
	{
		return $this->db->get('products')->num_rows();
	}

	public function selled()
	{
		return $this->db->get_where('orders', ['purchased' => 1])->num_rows();
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

	public function inOrder()
	{
		$rules = [
			'purchased' => 0,
			'deleted'	=> 0
		];
		return $this->db->get_where('orders', $rules)->num_rows();
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