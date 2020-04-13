<?php 

class Notification_model extends CI_Model
{

	public function getNotification($user) {
		$rules = [ 'role_id' => $user['role_id'], 'user_id'	=> $user['id'] ];
		$notification = $this->db->order_by('created', 'DESC')->get_where('notifications', $rules)->result_array();

		$i=0;
		foreach ($notification as $key) {
			$notification[$i]['created'] = $this->_generateTimeDesc($key['created']);
			$i++;
		}

		return $notification;
	}

	private function _generateTimeDesc($time)
	{
		if (time() - $time < (60*60*24))
		{
			return postdate($time) . " yang lalu";
		}else{
			return date('d F Y', $time);
		}
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
