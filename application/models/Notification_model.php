<?php 

class Notification_model extends CI_Model {

	public function getNotification($username) {
		$user = $this->db->get_where('users', ['username' => $username])->row_array();

		if ($user['role_id'] > 1) {
			$options = [
				'role_id' 	=> $user['role_id'],
				'user_id' 	=> $user['id']
			];
		} else {
			$options = [
				'role_id' 	=> $user['role_id']
			];
		}
		$notification = $this->db->order_by('created', 'DESC')->get_where('notifications', $options)->result_array();

		$this->load->helper('date');

		$i=0;
		foreach ($notification as $key) {
			if ( date('d F y', $key['created']) === date('d F y', time()) ) {
				$post_date = $key['created'];
				$now = time();
				$units = 2;
				$notification[$i]['created'] = timespan($post_date, $now, $units) . ' yang lalu.';
			} else {
				$notification[$i]['created'] = date('d F Y', $key['created']);
			}
			$i++;
		}

		return $notification;
	}

	public function updateNotification($id) {
		$this->db->update('notifications', ['readed' => 1], ['id' => $id]);
		return $this->db->affected_rows();
	}

	public function deleteNotification($id) {
		$this->db->delete('notifications', ['id' => $id]);
		return $this->db->affected_rows();
	}

}
