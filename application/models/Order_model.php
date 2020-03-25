<?php 

class Order_model extends CI_Model
{
	public function getOrder($control, $options)
	{
		if ($control['get_entry'])
		{
			$order = $this->db->get_where('orders', [ 'entry' => $options['entry'] ])->result_array();
		}
		else
		{
			if ($control['is_admin'])
			{
				$rules = [
				];
			}

			if ($control['is_member'])
			{
				$consumer = $this->db->get_where('users', [ 'username' => $options['username'] ])->row_array();

				$rules = [
					'user_id'	=> $consumer['id'],
					'deleted'	=> 0
				];
			}

			$order = $this->db->order_by('created', 'DESC')->group_by('entry')->get_where('orders', $rules)->result_array();
		}


		if (!$order)
		{
			return FALSE;
		}

		$this->load->helper('date');

		$i=0;
		foreach ($order as $key)
		{
			$consumer = $this->db->get_where('users', [ 'id' => $key['user_id'] ])->row_array();

			$deleted = $order[$i]['deleted'] > 0;
			$purchased = $order[$i]['purchased'] > 0;
			$canceled = $deleted && !$purchased;

			if ($control['is_member'])
			{
				$order[$i]['has_avatar'] = FALSE;
				$order[$i]['username'] = "";
			}

			if ($control['is_admin'])
			{
				$order[$i]['has_avatar'] = TRUE;
				$order[$i]['username'] = $consumer['name'];
				$order[$i]['avatar'] = base_url('assets/img/avatar/' . $consumer['avatar']);

				if ($canceled)
				{
					$today = date('d F y', $key['created']) === date('d F y', time());
					if ($today)
					{
						$now = time();
						$units = 2;
						$time_span = timespan($key['deleted'], $now, $units) . " yang lalu.";
					}
					else
					{
						$time_span = date('d F y', $key['deleted']);
					}

					$desc = "Dibatalkan pada " . $time_span . ".";
					$order[$i]['description'] = $desc;
				}
			}

			if ($purchased)
			{
				$order[$i]['description'] = "Telah dikonfirmasi";
			}
			
			if (!$purchased && !$canceled)
			{
				$order[$i]['description'] = "Pending";
			}
            
            $today = date('d F y', $key['created']) === date('d F y', time());
            if ($today)
            {
				$post_date = $key['created'];
				$now = time();
				$units = 2;

				$order[$i]['created'] = timespan($post_date, $now, $units) . " yang lalu.";
			}
			else
			{
				$order[$i]['created'] = date('d F Y', $key['created']);
			}

			$i++;
		}

		return $order;
	}

	public function createOrder($data)
	{
		$this->db->insert('orders', $data);
		return $this->db->affected_rows();
	}

	public function updateOrder($data, $rules)
	{
		$this->db->update('orders', $data, $rules);
		return $this->db->affected_rows();
	}

	public function deleteOrder($rules) {
		$this->db->delete('orders', $rules);
		return $this->db->affected_rows();
	}

}