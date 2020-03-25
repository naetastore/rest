<?php 

class User_model extends CI_Model {

	public function createUser($data) {
		$this->db->insert('users', $data);
		return $this->db->affected_rows();
	}

	public function getUser($username) {
		return $this->db->get_where('users', ['username' => $username])->row_array();
	}

	public function updateUser($data, $username) {
		$user = $this->db->get_where('users', ['username' => $username])->row_array();
		$userdata = [];
		if ($data['name']) {
			$userdata = $userdata += ['name' => $data['name']];
		}
		if ($data['address']) {
			$userdata = $userdata += ['address' => $data['address']];
		}
		if ($data['phonenumber']) {
			/* Apakah contact yang diupdate masih sama dengan yang lama?
			* kalau sama biarkan
			* & kalau enggak Buat contact baru ke tabel contacts
			*/ $phonenumber = $data['phonenumber'];
			
			$existed = $user['phonenumber'] !== $phonenumber && $user['phonenumber'] !== null;
			if ($existed) {
				$oldContact = $this->db->get_where('contacts', ['user_id' => $user['id'], 'phonenumber' => $phonenumber])->num_rows();
				
				if ($oldContact < 1) {
					$contact = [
						'phonenumber' => $phonenumber,
						'user_id' => $user['id'],
						'created' => time()
					];
					$this->db->insert('contacts', $contact);

					$subject = "Kamu baru saja menambahkan Nomor baru " . $data['phonenumber'];
					$message = "Nomor sebelumnya tetap disimpan sebagai Nomor utama, adalah " . $user['phonenumber'] . ".";
					notification($subject, $message, 2, $user['id']);
				}

				$userdata = $userdata += ['phonenumber' => $user['phonenumber']];
			}
			else
			{
				$userdata = $userdata += ['phonenumber' => $data['phonenumber']];
			}
		}

		$this->db->update('users', $userdata, ['username' => $username]);
		return $this->db->affected_rows();
	}
	
}