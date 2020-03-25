<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Product extends REST_Controller
{

    public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model', 'product');
	}

    public function index_get()
    {
        /* parameter
        */ $id = $this->get('id');
        $suggested = $this->get('suggested');
        $limit = $this->get('limit');

        if ($id === null) {
            if ($suggested) {
                $product = $this->db->order_by('created', 'DESC')->get_where('products', ['suggested' => 1])->result_array();
            } else {
                if ($limit) {
                    $product = $this->db->limit($limit)->get('products')->result_array();
                } else {
                    $product = $this->product->getProduct();
                }
            }
            $i = 0;
            foreach ($product as $key) {
                $product[$i]['price'] = number_format($key['price']);
                $i++;
            }
        }
        else
        {
            $product = $this->product->getProduct($id);
            $product['price'] = number_format($product['price']);
        }

        if ($product) {
            $this->response([
                'status' => true,
                'product' => $product
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'id not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
}