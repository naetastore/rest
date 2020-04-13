<?php

class Product_model extends CI_Model {

	public function getProduct($id = null) {
		if ($id == null) {
			$product = $this->db->get_where('products', ['is_ready' => 1])->result_array();
		}
		else
		{
			$product = $this->db->get_where('products', ['id' => $id, 'is_ready' => 1])->row_array();
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
	
}