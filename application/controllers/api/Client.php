<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Client extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Client_model', 'client');
	}

	public function index_post()
	{
		$data = [
			'mont' => date('F', time()),
			'year' => date('y', time())
		];
		$visitor = $this->db->get_where('visitors', $data);
		if ($visitor->num_rows() < 1) {
			$data['count'] = 1;
			$this->db->insert('visitors', $data);
		}else{
			$data['count'] = $visitor->row_array()['count'] + 1;
			$this->db->update('visitors', $data, [
				'mont' => date('F', time()),
				'year' => date('y', time())
			]);
		}

		$data = [
			'userAgent'   => $this->post('userAgent'),
			'geoLocation' => $this->post('geoLocation'),
			'created' 	  => time()
		];

		if ($this->client->createClient($data) > 0) {
			$this->response([
            	'status' => true,
            	'message' => 'new data has been created.'
        	], REST_Controller::HTTP_CREATED);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'failed to created new data!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}