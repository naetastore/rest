<?php 

class GLobal_model extends CI_Model {

	public function getGlobal($id = null)
	{
		if ($id === null) {
			return $this->db->get('globals')->result_array();
		}
		else
		{
			return $this->db->get_where('globals', ['id' => $id])->row_array();
		}
	}
	
}