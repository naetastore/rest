<?php

function create_entry() {
	$entry = base64_encode(random_bytes(32));
	$entry = trim($entry, ',');
	$entry = explode('+', $entry);
	
	$result="";
	foreach($entry as $e) {
		$result .= $e;
	}

	$result = explode('/', $result);

	$result1="";
	foreach($result as $e) {
		$result1 .= $e;
	}

	return $result1;
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
