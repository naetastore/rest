<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class General extends REST_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Global_model', 'global');
	}

	public function index_get()
	{
		$id = $this->get('id');

		if ($id === null) {
			$general = $this->global->getGlobal();
			$i = 0;
			foreach ($general as $key) {
				$general[$i]['curs'] = 'Rp.';
				$general[$i]['start_price'] = rupiah_format($key['start_price']);
				$general[$i]['high_price'] = rupiah_format($key['high_price']);
				$general[$i]['image'] = base_url('src/img/global/' . $key['image']);
				$i++;
			}
		}
		else
		{
			$general = $this->global->getGlobal($id);
		}

		if ($general) {
			$this->response([
            	'status' => true,
            	'general' => $general,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'id not found',
        	], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

}