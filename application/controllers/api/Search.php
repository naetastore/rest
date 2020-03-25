<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Search extends REST_Controller
{
	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('Search_model', 'search');
	}

	public function index_get()
	{
		/* parameter
		*/ $keyword = $this->get('q');
		$limit = $this->get('limit');

		if (strlen($keyword) < 1) {
			$this->response([
            	'status' => false,
            	'message' => 'Provide an keyword!'
        	], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			if ($limit) {
				$search = $this->db->like('seo_keyword', $keyword);
				$search = $this->db->or_like('name', $keyword);
				$search = $this->db->limit($limit)->get('products')->result_array();
			} else {
				$search = $this->search->getSearch($keyword);
			}
		}

		if ($search) {
			$this->response([
            	'status' => true,
            	'search' => $search,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'Product not found!'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
	}

}