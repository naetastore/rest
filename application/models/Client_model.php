<?php 

class Client_model extends CI_Model {

	public function createClient($data)
	{
		$this->db->insert('clients', $data);
		return $this->db->affected_rows();
	}

}