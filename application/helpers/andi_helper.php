<?php 

// untuk API
function is_logedin($username, $password)
{
	$ci = get_instance();
	if (strlen($username) < 1) {
    	return false;
	}

	if (strlen($password) < 1) {
		return false;
	}

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	
	if ($user) {
		if (password_verify($password, $user['password'])) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
// untuk API
function is_admin($username, $password)
{
	$ci = get_instance();
	if (strlen($username) < 1) {
    	return false;
	}

	if (strlen($password) < 1) {
		return false;
	}

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	
	if ($user) {
		if ($user['role_id'] > 1) {
			return false;
		} else {
			if (password_verify($password, $user['password'])) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}

// untuk API
function is_member($username, $password)
{
	$ci = get_instance();
	if (strlen($username) < 1) {
    	return false;
	}

	if (strlen($password) < 1) {
		return false;
	}

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	
	if ($user) {
		if ($user['role_id'] < 2) {
			return false;
		} else {
			if (password_verify($password, $user['password'])) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}

// untuk API
function get_user($username, $password)
{
	$ci = get_instance();
	if (strlen($username) < 1) {
    	return false;
	}

	if (strlen($password) < 1) {
		return false;
	}

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	
	if ($user) {
		
		if (password_verify($password, $user['password'])) {
			return $user;
		} else {
			return false;
		}

	} else {
		return false;
	}
}

// untuk API
function create_entry() {
	$entry = base64_encode(random_bytes(32));
	$entry = trim($entry, ',');
	return $entry;
}

// untuk API
function update_automatic($uid, $uentry) {
	$ci = get_instance();

	$options = [
		'user_id'	=> $uid,
		'purchased'	=> 1,
		'entry'		=> $uentry
	];

	$purchased = $ci->db->get_where('orders', $options)->result_array();
	foreach ($purchased as $key) {
		$product = $ci->db->get_where('products', [ 'id' => $key['product_id'] ])->row_array();
		$newQty = $product['qty'] - $key['qty'];
		$ci->db->update('products', ['qty' => $newQty], [ 'id' => $key['product_id'] ]);
	}
}

// untuk API
function delete_automatic($rules) {
	$ci = get_instance();

	$orders = $ci->db->get_where('orders', $rules)->result_array();

	foreach ($orders as $key) {
		$product = $ci->db->get_where('products', [
			'id' => $key['product_id']
		])->row_array();

		if ($product['qty'] < 1) {
			if ($product['image'] !== 'default_image.jpg') {
				unlink(FCPATH . 'assets/img/product/' . $product['image']);
			}

			$ci->db->delete('products', [
				'id' => $product['id']
			]);
			$subject = $product['name'] . " dihapus dari daftar barang";
			$message = "Kamu bisa menonaktifkan fitur ini melalui setelan.";

			$ci->db->insert('notifications', [
				'subject'		=> $subject,
				'message' 		=> $message,
				'role_id'	  	=> 1,
				'user_id'	  	=> 1,
				'readed'	 	=> 0,
				'created'	  	=> time()
			]);
		}
	}
}

function menu_is_active($menu, $match)
{
	$ci = get_instance();
	if ($menu == $match) {
		return 'active';
	}
}

function get_categories_where_product_id($id){
	$ci = get_instance();
	return $ci->db->get_where('categories', ['id' => $id])->row_array();
}

function activities($act, $table, $desc = null) {
	$ci = get_instance();
	$act = strtolower($act);

	if ($act == 'delete') {
		$key = 0;
	}

	if ($act == 'create' | $act == 'update') {
		$key = $ci->db->order_by('id', 'DESC')->get($table)->row_array()['id'];
	}

	if ($desc === null) {
		$desc = $act . ' ' . $table;
	}
	
	$data = [
		'table' 	  => $table,
		'key' 		  => $key,
		'description' => $desc,
		'readed' 	  => 0,
		'created' 	  => time()
	];
	$ci->db->insert('activities', $data);
}

function notification($subject, $message, $role_id, $uid, $topic) {
	$ci = get_instance();
	
	$ci->db->insert('notifications', [
		'subject'		=> $subject,
		'message' 		=> $message,
		'role_id'	  	=> $role_id,
		'user_id'	  	=> $uid,
		'readed'	 	=> 0,
		'topic'			=> $topic,
		'created'	  	=> time()
	]);
}

function postdate($time) {
	$ci = get_instance();
	$ci->load->helper('date');
	$post_date = $time;
	$now = time();
	$units = 2;
	return timespan($post_date, $now, $units);
}
