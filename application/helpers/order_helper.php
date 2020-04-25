<?php

function get_access($array)
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