<?php 

function check_ui_config($role_id, $ui_id)
{
	$ci = get_instance();
	
	$dataConfig = [
		'role_id' => $role_id,
		'ui_id' => $ui_id
	];
	$result = $ci->db->get_where('order_ui_config', $dataConfig);
	if ($result->num_rows() > 0) {
		return "checked=checked";
	}
}

function get_ui_config($array)
{
	$ci = get_instance();

	$role_id = $array['role_id'];
	$ui_id = $ci->db->get_where('order_ui', [ 'ui' => $array['ui'] ])->row_array()['id'];

	$dataConfig = [
		'role_id' 	=> $role_id,
		'ui_id' => $ui_id
	];
	$queryConfig = $ci->db->get_where('order_ui_config', $dataConfig);
	return $queryConfig;
}

// =====

function check_order_access($role_id, $status_id, $action_id)
{
	$ci = get_instance();

	$dataAccess = [
		'role_id' 	=> $role_id,
		'status_id' => $status_id,
		'action_id' => $action_id
	];
	$result = $ci->db->get_where('order_access', $dataAccess);
	if ($result->num_rows() > 0) {
		return "checked=checked";
	}
}

function get_order_access($array)
{
	$ci = get_instance();

	$role_id = $array['role_id'];
	$action_id = $ci->db->get_where('order_action', [ 'action' => $array['action'] ])->row_array()['id'];
	$status_id = $ci->db->get_where('order_status', [ 'status' => $array['status'] ])->row_array()['id'];

	$dataAccess = [
		'role_id' 	=> $role_id,
		'action_id' => $action_id,
		'status_id' => $status_id
	];
	$queryAccess = $ci->db->get_where('order_access', $dataAccess);
	return $queryAccess;
}

// =====

function check_menu_access($role_id, $menu_id)
{
	$ci = get_instance();

	$result = $ci->db->get_where('user_access_menu', [ 'role_id' => $role_id, 'menu_id' => $menu_id ]);
	if ($result->num_rows() > 0) {
		return "checked=checked";
	}
}

// =====

function in_session()
{
	$ci = get_instance();

	if (!isset($_GET['session']) | !isset($_GET['username'])) {
		echo "<h2 style='color: #777'>no session! please <a href=" . base_url('auth') . ">login</a></h2>";
		die();
	}

	$session = $_GET['session'];
	$username = $_GET['username'];
	$_session = $ci->db->get_where('sessions', [ 'session' => $session, 'username' => $username ])->row_array();
	
	if (!$_session) {
		echo "<h2 style='color: #777'>session invalid! go back to the <a href=" . base_url('auth') . ">login</a> page</h2>";
		echo "<script>window.localStorage.removeItem('naetastore_sess'); window.localStorage.removeItem('naetastore_name')</script>";
		
		$query = $ci->db->like('session', $session)->or_like('username', $username)->get('sessions')->row_array();
		$ci->db->delete('sessions', [ 'session' => $query['session'] ]);
		die();
	}

	if (time() - $_session['created'] > (60*60*24))
	{
		echo "<h2 style='color: #777'>session expired <span style='text-transform: lowercase'>" . date('d F y', $_session['created']) . "</span>! please <a href=" . base_url('auth') . ">login</a> again</h2>";
		echo "<script>window.localStorage.removeItem('naetastore_sess'); window.localStorage.removeItem('naetastore_name')</script>";
		
		$ci->db->delete('sessions', [ 'session' => $session ]);
		die();
	}

	$on_session = $ci->db->get_where('users', [ 'username' => $_session['username'] ])->row_array();
	$role_id = $on_session['role_id'];

	$menu = $ci->uri->segment(1);
	$queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
	$menu_id = $queryMenu['id'];

	$userAccess = $ci->db->get_where('user_access_menu', ['role_id' => $role_id, 'menu_id' => $menu_id]);
	if ($userAccess->num_rows() < 1) {
		$menu = strtolower($queryMenu['menu']);
		redirect("$menu?session=$session&username=$username");
	}

}



function create_session() {
	$session = base64_encode(random_bytes(32));
	$session = explode('+', $session);

	$result = "";
	foreach ($session as $key => $value) $result .= $value;
	return $result;
}

function requiredparams()
{
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (!isset($_GET['session']) | !isset($_GET['username'])) return FALSE;
		$session = $_GET['session'];
		$username = $_GET['username'];
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if (!isset($_POST['session_id']) | !isset($_POST['username'])) return FALSE;
		$session = $_POST['session_id'];
		$username = $_POST['username'];
	}

	return [ 'session' => $session, 'username' => $username ];
}

// untuk API
function is_logedin($username, $password)
{
	if (strlen($username) < 1 | strlen($password) < 1) return false;

	$ci = get_instance();

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	if (!$user) return false;

	if (password_verify($password, $user['password']))
	{
		return true;
	}else{
		return false;
	}
}
// untuk API
function is_admin($username, $password)
{
	if (strlen($username) < 1 | strlen($password) < 1) return false;

	$ci = get_instance();
	
	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	if (!$user) return false;

	if ($user['role_id'] > 1)
	{
		return false;
	}else{
		if (password_verify($password, $user['password'])) {
			return true;
		}else{
			return false;
		}
	}
}

// untuk API
function get_user($username, $password)
{
	$ci = get_instance();
	
	if (strlen($username) <= 0 | strlen($password) <= 0) return false;

	$user = $ci->db->get_where('users', ['username' => $username])->row_array();
	if (!$user) return false;
		
	if (password_verify($password, $user['password'])) {
		return $user;
	}else{
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

	$rules = [
		'user_id'	=> $uid,
		'purchased'	=> 1,
		'entry'		=> $uentry
	];
	$purchased = $ci->db->get_where('orders', $rules)->result_array();

	foreach ($purchased as $key)
	{
		$product = $ci->db->get_where('products', [ 'id' => $key['product_id'] ])->row_array();
		$newQty = $product['qty'] - $key['qty'];
		$ci->db->update('products', ['qty' => $newQty], [ 'id' => $key['product_id'] ]);
	}
}

// untuk API
function delete_automatic($rules) {
	$ci = get_instance();

	$orders = $ci->db->get_where('orders', $rules)->result_array();

	foreach ($orders as $key)
	{
		$product = $ci->db->get_where('products', [ 'id' => $key['product_id'] ])->row_array();
		if ($product['qty'] > 0) return;
		
		if ($product['image'] !== 'default_image.jpg') unlink(FCPATH . 'src/img/product/' . $product['image']);
		$ci->db->delete('products', [ 'id' => $product['id'] ]);

		$subject = $product['name'] . " dihapus dari daftar barang";
		$message = "Kamu dapat menonaktifkan fitur ini melalui setelan.";
		
		$data = [
			'subject'		=> $subject,
			'message' 		=> $message,
			'role_id'	  	=> 1,
			'user_id'	  	=> 1,
			'readed'	 	=> 0,
			'created'	  	=> time()
		];
		$ci->db->insert('notifications', $data);
	}
}

// untuk API
function rewrapp($userdata)
{
	$ci = get_instance();
	
	$data = [
		'id'				=> $userdata['id'],
    	'username' 			=> $userdata['username'],
    	'role'	 			=> $userdata['role_id'],
    	'created'	 		=> date('d F Y', $userdata['created'])
	];

	if ($userdata['name'] !== NULL) $data['name'] = $userdata['name'];

	if ($userdata['phonenumber'] !== NULL)
	{
		$scnd = $ci->db->order_by('created', 'DESC')->get_where('contacts', [ 'user_id' => $userdata['id'] ]);
		if ($scnd->num_rows() < 1)
		{
			$data['phone'] = $userdata['phonenumber'];
		}else{
			$scnd = $scnd->row_array();
			$data['phone'] = $scnd['phonenumber'];
		}
	}

	if ($userdata['address'] !== NULL) $data['address'] = $userdata['address'];

	if ( strlen($userdata['avatar'])  > 0)
	{
		$data['avatar'] = base_url('src/img/avatar/' . $userdata['avatar']);
	}

	return $data;
}

function menu_is_active($menu, $match)
{
	$ci = get_instance();
	if ($menu == $match) return 'active';
}

function activities($act, $table, $desc = null) {
	$ci = get_instance();

	$act = strtolower($act);
	if ($act == 'delete') $key = 0;
	if ($act == 'create' | $act == 'update') {
		$key = $ci->db->order_by('id', 'DESC')->get($table)->row_array()['id'];
	}
	if ($desc === null) $desc = $act . ' ' . $table;
	
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
	$data = [
		'subject'		=> $subject,
		'message' 		=> $message,
		'role_id'	  	=> $role_id,
		'user_id'	  	=> $uid,
		'readed'	 	=> 0,
		'topic'			=> $topic,
		'created'	  	=> time()
	];
	$ci->db->insert('notifications', $data);
}

function postdate($time) {
	$ci = get_instance();
	$ci->load->helper('date');
	$post_date = $time;
	$now = time();
	$units = 1;
	return timespan($post_date, $now, $units);
}
