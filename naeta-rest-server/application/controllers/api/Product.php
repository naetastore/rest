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
        
        // replace: rupiah number formating
        if ($product && isset($product['price']))
        {
            $product['curs'] = 'Rp.';
            $product['price'] = rupiah_format($product['price']);
            $product['image'] = base_url('src/img/product/' . $product['image']);
        }
        if ($product && !isset($product['price']))
        {
            $i=0;
            foreach ($product as $key)
            {
                $product[$i]['curs'] = 'Rp.';
                $product[$i]['price'] = rupiah_format($key['price']);
                $product[$i]['image'] = base_url('src/img/product/' . $key['image']);
                $i++;
            }
        }

        if ($product) {
            $this->response([
                'status' => true,
                'product' => $product
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'no data to display'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
}