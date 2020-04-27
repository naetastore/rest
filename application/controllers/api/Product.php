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
        if ($this->get('delete')) {
            $this->_delete();
            return;
        }

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
            $product['selled'] = $this->product->selled($product['id']);
        }else{
            $i=0;
            foreach ($product as $key)
            {
                $product[$i]['curs'] = 'Rp.';
                $product[$i]['price'] = number_format($key['price'], 0, '.', '.');
                $product[$i]['image'] = base_url('src/img/product/' . $key['image']);
                $product[$i]['selled'] = $this->product->selled($key['id']);
                $i++;
            }
        }

        $this->response([
            'status' => true,
            'product' => $product
        ], REST_Controller::HTTP_OK);
    }

    private function _delete()
    {
        $id = (int) $this->get('id');

        if ($id <= 0)
        {
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->helper('client');

        $user = basic_auth();

        if ($user['role_id'] == 2) {
            $this->response([
                'status' => false,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $queryProduct = "SELECT `products`.`image` FROM `products` WHERE `products`.`id` = $id";
        $product = $this->db->query($queryProduct)->row_array();

        unlink(FCPATH . 'src/img/product/' . $product['image']);

        $this->db->delete('products', ['id' => $id]);

        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        $this->load->helper('client');

        $user = basic_auth();

        if ($user['role_id'] == 2) {
            $this->response([
                'status' => false,
                'message' => 'Something went wrong'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $category_id = $this->post('category_id');

        $queryCategory = "SELECT `categories`.`name`,`global_id` FROM `categories` WHERE `categories`.`id` = $category_id";
        $category = $this->db->query($queryCategory)->row_array();

        $queryGlobal = "SELECT `globals`.`name` FROM `globals` WHERE `globals`.`id` = {$category['global_id']}";
        $global_name = $this->db->query($queryGlobal)->row_array()['name'];

        $name = $this->post('name');

        $seo_keyword = $global_name . ', ' . $category['name'] . ', ' . $name;

        $data = [
            'name' => $name,
            'price' => $this->post('price'),
            'qty' => $this->post('qty'),
            'global_id' => $category['global_id'],
            'category_id' => $category_id,
            'description' => $this->post('description'),
            'seo_keyword' => $seo_keyword,
            'suggested' => $this->post('suggested'),
            'is_ready' => $this->post('is_ready'),
            'created' => time()
        ];

        $id = $this->post('id');

        $uploadImage = TRUE;
        if (isset($id)) {
            $queryProduct = "SELECT `products`.`image` FROM `products` WHERE `products`.`id` = $id";
            $product = $this->db->query($queryProduct)->row_array();

            $image = trim($this->post('image'), base_url());
            if ('img/product/' . $product['image'] === $image) {
                $uploadImage = FALSE;
            }
        }
        if ($uploadImage) {
            $config['upload_path']          = FCPATH . 'src/img/product/';
            $config['allowed_types']        = 'jpg|jpeg|png|svg';
            $config['encrypt_name']         = TRUE;
            
            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload('image'))
            {
                $this->response([
                    'status' => false,
                    'message' => 'Image failed to uploaded'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
            else
            {
                if (isset($id)) {
                    unlink(FCPATH . 'src/img/product/' . $product['image']);
                }

                $image_name = $this->upload->data('file_name');
                $this->db->set('image', $image_name);
            }
        }

        if (isset($id)) {
            $this->db->update('products', $data, ['id' => $id]);
        }else{
            $this->db->insert('products', $data);
        }

        if ($this->db->affected_rows() > 0) {
            $product = $this->db->get_where('products', ['name' => $name])->row_array();
            $product['selled'] = 0;

            if (isset($id)) {
                $message = [
                    'product' => $product,
                    'message' => 'Update a resource'
                ];
                $code = 200;
            } else {
                $message = [
                    'product' => $product,
                    'message' => 'Added a resource'
                ];
                $code = 201;
            }

            $this->set_response($message, $code);
        }else{
            if (isset($id)) {
                $message = [
                    'status' => TRUE,
                    'message' => 'Everything is up to date'
                ];
                $code = 200;
            } else {
                $message = [
                    'status' => FALSE,
                    'message' => 'Product failed to uploaded'
                ];
                $code = 400;
            }

            $this->set_response($message, $code);
        }
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