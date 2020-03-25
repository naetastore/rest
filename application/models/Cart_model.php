<?php 

class Cart_model extends CI_Model
{
	public function getUser($uid)
	{
		return $this->db->get_where('users', ['id' => $uid])->num_rows();
	}

	public function getProduct($id)
	{
		return $this->db->get_where('products', ['id' => $id])->num_rows();
	}

	public function getOldData($uid)
	{
		return $this->db->get_where('carts', ['user_id' => $uid])->result_array();
	}
}