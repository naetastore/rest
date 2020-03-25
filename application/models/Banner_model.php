<?php 

class Banner_model extends CI_Model
{
	public function getBanner($id = null) {
		if ( $id === null ) {
			return $this->db->get('banners')->result_array();
		} else {
			return $this->db->get_where('banners', ['id' => $id])->result_array();
		}
	}
}