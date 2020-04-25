<?php

function postdate($time) {
	$ci = get_instance();
	$ci->load->helper('date');
	
	$post_date = $time;
	$now = time();
	$units = 1;

	if ($now - $post_date < (60*60*24)) {
		return timespan($post_date, $now, $units) . ' yang lalu';
	}else{
		return date('d F y', $time);
	}
}