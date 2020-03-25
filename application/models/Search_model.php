<?php 

class Search_model extends CI_Model
{
	public function getSearch($keyword) {
		$this->db->like('seo_keyword', $keyword);
		$this->db->or_like('name', $keyword);
		$result = $this->db->get('products')->result_array();
		$i=0;
		foreach ($result as $key) {
			$result[$i]['price'] = number_format($key['price']);
			$i++;
		}
		return $result;
	}
}