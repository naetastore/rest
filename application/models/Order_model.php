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
			if ($control['is_admin']) $rules = [];
			if ($control['is_consumer']) $rules = [ 'user_id' => $options['user_id'], 'deleted' => 0 ];

			$order = $this->db->order_by('created', 'DESC')->group_by('entry')->get_where('orders', $rules)->result_array();
		}
		// ======================================== ======================================

		if (!$order) return FALSE;

		$_get_result=[];
		$_detail=['subtotal'=>0]; // add: order summary detail

		$i=0;
		foreach($order as $key)
		{
			$consumer = $this->db->get_where('users', [ 'id' => $key['user_id'] ])->row_array();

			$purchased 	= $order[$i]['purchased'] > 0;
			$updated 	= $order[$i]['updated'] > 0;
			$canceled 	= $order[$i]['canceled'] > 0 && !$purchased;

			// add: avatar
			$key['avatar'] = NULL; //default
			if (strlen($consumer['avatar']) > 0) $key['avatar'] = base_url('src/img/avatar/' . $consumer['avatar']);

			// replace: username
			$_get_result[$i]['username'] = $consumer['name'];

			// replace: created
			$key['created'] = $this->_generateTimeDesc($key['created']);

			// replace: description
			if ($purchased) {
				$key['description'] = "Telah dikonfirmasi";
				$key['textcolor'] 	= "#20C997";
			}
			if (!$canceled) {
				$key['description'] = "Dalam pemesanan";
				$key['textcolor'] 	= "#17A2B8";
			}
			if ($updated && !$canceled)
			{
				$time_span = $this->_generateTimeDesc($key['updated']);
				$key['description'] = "Diperbarui " . $time_span;
				$key['textcolor']	= "#6F42C1";
			}

			if ($control['is_admin'])
			{
				// replace: description
				if ($canceled)
				{
					$time_span = $this->_generateTimeDesc($key['deleted']);
					$key['description'] = "Dibatalkan " . $time_span;
					$key['textcolor'] 	= "#DC3545";
				}
			}

			// replace final
			if ($control['get_entry'])
			{
				// for: full order details
				$_get_result[$i] = $key;
				$_detail['subtotal'] += $key['price'] * $key['qty'];
			}else{
				// for: order listing (minimized) (rewrapping)
				$_get_result[$i]['user_id'] = $key['user_id'];
				if (isset($key['avatar']) && $key['avatar'] !== NULL) $_get_result[$i]['avatar'] = $key['avatar'];
				$_get_result[$i]['entry'] = $key['entry'];
				$_get_result[$i]['description'] = $key['description'];
				$_get_result[$i]['textcolor'] = $key['textcolor'];
				$_get_result[$i]['created'] = $key['created'];
				$_get_result[$i]['readed'] = $key['readed'];
			}

			$i++;
		}

		// all now returning (clear data)
		if (!$control['get_entry']) return $_get_result;
		if ($control['get_entry'])
		{
			// add: order summary detail
			$_detail['shipping'] = 0;
			$_detail['total'] = $_detail['subtotal'] + $_detail['shipping'];
			
			// replace: rupiah number formating
			$i=0;
			foreach($_get_result as $key)
			{
				$_get_result[$i]['price'] = rupiah_format($key['price']);
				$_get_result[$i]['curs'] = 'Rp.';
				$i++;
			}
			$_detail['subtotal'] = rupiah_format($_detail['subtotal']);
			$_detail['shipping'] = rupiah_format($_detail['shipping']);
			$_detail['total'] 	 = rupiah_format($_detail['total']);

			return [ 'product' => $_get_result, 'detail' => $_detail ];
		}
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