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
        $id = $this->get('id');
        $suggested = $this->get('suggested');
        $limit = $this->get('limit');

        if ($id === null) {
            if ($suggested) {
                $dataQuery = ['suggested' => 1, 'is_ready' => 1, 'deleted' => 0];
                $product = $this->db->order_by('created', 'DESC')->get_where('products', $dataQuery)->result_array();
            } else {
                if ($limit) {
                    $product = $this->db->limit($limit)->get_where('products', ['is_ready' => 1, 'deleted' => 0])->result_array();
                } else {
                    $product = $this->product->getProduct();
                }
            }
        }
        else
        {
            $product = $this->product->getProduct($id);
        }
        
        if (!$product) {
            $this->response([
                'status' => false,
                'message' => 'Products could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if (isset($product['price'])) {
            $product['curs'] = 'Rp.';
            $product['price'] = number_format($product['price'], 0, '.', '.');
            $product['image'] = base_url('src/img/product/' . $product['image']);
        }else{
            $i=0;
            foreach ($product as $key)
            {
                $product[$i]['curs'] = 'Rp.';
                $product[$i]['price'] = number_format($key['price'], 0, '.', '.');
                $product[$i]['image'] = base_url('src/img/product/' . $key['image']);
                $i++;
            }
        }

        $this->response([
            'status' => true,
            'product' => $product
        ], REST_Controller::HTTP_OK);
    }

    public function search_get()
    {
        $keyword = $this->get('q');

        $this->load->model('Search_model', 'search');

        if (strlen($keyword) < 1)
        {
            $this->response([
                'status' => false,
                'message' => 'Provide an keyword'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $search = $this->search->getSearch($keyword);

        if (!$search) {
            $this->response([
                'status' => false,
                'message' => 'Product not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }else{
            $this->response([
                'status' => true,
                'search' => $search,
            ], REST_Controller::HTTP_OK);
        }
    }

    public function statistics_get()
    {
        $this->load->model('Administrator_model', 'admin');

        $data = [
            'basic' => $this->admin->basic_statistics(),
            'product' => $this->admin->product_statistics()
        ];

        $this->response($data, REST_Controller::HTTP_OK);
    }
    
}