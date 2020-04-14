<?php 

/**
 * 
 */
class Settings_model extends CI_Model
{
	
	function updateSettings($data)
	{
		$this->db->update('settings', $data);
		return $this->db->affected_rows();
	}
}