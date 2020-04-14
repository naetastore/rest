<?php 

class User_model extends CI_Model {

	public function createUser($data) {
		$this->db->insert('users', $data);
		return $this->db->affected_rows();
	}

	public function getUser($username) {
		return $this->db->get_where('users', ['username' => $username])->row_array();
	}

	public function updateUser($data, $user) {
		$_data=[];
		if (isset($data['password'])) $_data['password'] = $data['password'];
		if (isset($data['name'])) $_data['name'] = $data['name'];
		if (isset($data['address'])) $_data['address'] = $data['address'];
		if (isset($data['phonenumber']))
		{
			$init_contact = strlen($user['phonenumber']) < 1 && $data['phonenumber'] !== $user['phonenumber'];
			if ($init_contact) {
				$_data['phonenumber'] = $data['phonenumber']; //update in to: table users
			}else{
				$contact_existed = $this->db->get_where('contacts', ['user_id' => $user['id'], 'phonenumber' => $data['phonenumber']]);				
				if ($contact_existed->num_rows() < 1) {
					$contact = [
						'phonenumber' 	=> $data['phonenumber'],
						'user_id' 		=> $user['id'],
						'created' 		=> time()
					];
					$this->db->insert('contacts', $contact); //insert in to: table contacts
				}

				$_data['phonenumber'] = $user['phonenumber']; //replace: source
			}
		}

		$this->db->update('users', $_data, [ 'id' => $user['id'] ]);
		return $this->db->affected_rows();
	}
	
}