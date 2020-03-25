<?php

class Product_model extends CI_Model {

	public function getProduct($id = null) {
		if ($id === null) {
			return $this->db->get('products')->result_array();
		}
		else
		{
			return $this->db->get_where('products', ['id' => $id])->row_array();
		}
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