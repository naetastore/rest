<?php

/**
 * @param $array(
 *  'subject' => $subject,
 *  'message' => $message,
 *  'user' => $user
 * );
 */
function send_notification($array) {
    $ci = get_instance();

	$data = [
		'subject'		=> $array['subject'],
		'message' 		=> $array['message'],
		'role_id'	  	=> $array['user']['role_id'],
		'user_id'	  	=> $array['user']['id'],
		'readed'	 	=> 0,
		'topic'			=> $array['user']['entry']
    ];
    
    if ($ci->db->get_where('notifications', $data)->num_rows() > 0)
    {
        return;
    }

    $data['created'] = time();

	$ci->db->insert('notifications', $data);
}
