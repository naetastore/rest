<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Category extends REST_Controller
{
	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('Category_model', 'category');
	}

	public function index_get()
	{
		$id = $this->get('id');

		if ($id === null) {
			$category = $this->category->getCategory();
		} else {
			$category = $this->category->getCategory($id);
		}

		if ($category) {
			$i=0;
			foreach ($category as $key) {
				$queryProduct = $this->db->get_where('products', ['category_id' => $key['id']]);
				if ($queryProduct->num_rows() < 1) {
					unset($queryProduct[$i]);
				}
				$i++;
			}

			$this->response([
            	'status' => true,
            	'category' => $category,
        	], REST_Controller::HTTP_OK);
		} else {
			$this->response([
            	'status' => false,
            	'message' => 'id not found!'
        	], REST_Controller::HTTP_NOT_FOUND);
		}
	}

}