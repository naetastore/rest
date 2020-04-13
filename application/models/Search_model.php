<?php 

class Search_model extends CI_Model
{

	public function getSearch($keyword) {

		$keyword = explode(' ', $keyword);
		$search=[];
		foreach($keyword as $k)
		{
			if ($k !== '')
			{
				$query = $this->db->query("SELECT * FROM products WHERE seo_keyword LIKE '%$k%'")->result_array();
				$i=0;
				foreach($query as $q)
				{
					$search[$i]=$q;
					$i++;
				}
			}
		}

		$search = array_unique($search, false);

		$i=0;
		foreach ($search as $key) {
			$search[$i]['price'] = rupiah_format($key['price']);
			$search[$i]['image'] = base_url('src/img/product/' . $key['image']);
			$i++;
		}
		return $search;
	}

}
