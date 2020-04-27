<?php

function create_entry() {
	$ci = get_instance();
	
	do {
		$new_entry = base_convert(bin2hex($ci->security->get_random_bytes(64)), 16, 36);
	} while ($ci->db->get_where('users', ['entry' => $new_entry])->num_rows() > 0);

	return $new_entry;
}

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
		$data['phone'] = $scnd['phonenumber'];
	}

	if ($userdata['address'] !== NULL) $data['address'] = $userdata['address'];

	if ( strlen($userdata['avatar'])  > 0)
	{
		$data['avatar'] = base_url('src/img/avatar/' . $userdata['avatar']);
	}

	return $data;
}
