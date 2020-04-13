<?php 

class Activities_model extends CI_Model
{

	public function getActivities($limit = null)
	{
		if ($limit == null)
		{
			$act = $this->db->order_by('created', 'DESC')->get_where('activities', ['readed' => 0])->result_array();
		}else{
			$act = $this->db->limit($limit)->order_by('created', 'DESC')->get_where('activities', ['readed' => 0])->result_array();
		}

		$i=0;
		foreach ($act as $key) {
		
			$result = $this->db->get_where($key['table'], ['id' => $key['key']])->row_array();
			switch ($key['table']) {
				case 'orders':
					$act[$i]['detail'] = [
						'product' 	=> $result['name'],
						'id' 	=> $result['product_id']
					];
					break;
				
				case 'clients':
					$act[$i]['detail'] = [
						'name' 	=> $result['userAgent']
					];
					break;
				
				case 'users':
					$act[$i]['detail'] = [
						'username' 	=> $result['username']
					];
					break;
				
				case 'tweets':
					$act[$i]['detail'] = [
						'tweet' 	=> $result['description']
					];
					break;
				
				default:
					# code...
					break;
			}
			$i++;
		}

		return $act;
	}

}