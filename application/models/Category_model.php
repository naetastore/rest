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
	
}