<?php

class Category_model extends CI_Model {

    public function getCategory($id = null) {
		if ($id === null) {
			return $this->db->get('categories')->result_array();
		}
		else
		{
			return $this->db->get_where('categories', ['id' => $id])->result_array();
		}
	}

	// public function deleteCategory($id) {
	// 	$this->db->delete('categories', ['id' => $id]);
	// 	return $this->db->affected_rows();
	// }

	// public function createCategory($data) {
	// 	$this->db->insert('categories', $data);
	// 	return $this->db->affected_rows();
	// }

	// public function updateCategory($data, $id)
	// {
	// 	$this->db->update('categories', $data, ['id' => $id]);
	// 	return $this->db->affected_rows();
	// }
	
}