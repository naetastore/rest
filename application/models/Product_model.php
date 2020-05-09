<?php

class Product_model extends CI_Model {

	public function getProduct($id = null) {
		if ($id == null) {
			$product = $this->db->get_where('products', ['is_ready' => 1, 'deleted' => 0])->result_array();
		}
		else
		{
			$product = $this->db->get_where('products', ['id' => $id, 'is_ready' => 1, 'deleted' => 0])->row_array();
		}

		return $product;
	}

	public function deleteProduct($id) {
		$this->db->delete('products', ['id' => $id]);
		return $this->db->affected_rows();
	}

	public function createProduct($data) {
		$this->db->insert('products', $data);
		return $this->db->affected_rows();
	}

	public function updateProduct($data, $id)
	{
		$this->db->update('products', $data, ['id' => $id]);
		return $this->db->affected_rows();
	}

	public function selled($id)
	{
		$this->db->where('product_id', $id);
		$this->db->where('purchased <', 1);
		$order = $this->db->get('orders')->result_array();
        $_n=0;
        foreach ($order as $r) {
            $_n += $r['qty'];
        }
        return number_format($_n, 0, '.', '.');
	}
	
}