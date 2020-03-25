<?php 

class Tweet_model extends CI_Model
{

	public function createTweet($data)
	{
		$this->db->insert('tweets', $data);
		return $this->db->affected_rows();
	}

}