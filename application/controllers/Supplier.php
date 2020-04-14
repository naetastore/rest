<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller
{
    
    private $on_session;
    private $requiredparams;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
        $this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
        $this->load->model('Supplier_model', 'supplier');
    }

    public function index()
    {
        $data['title'] = 'Supplier';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $this->load->view('blankpage', $data);
    }

    public function upload()
    {
        $data['title'] = 'Upload';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
        
        $data['user'] = $this->on_session;
        $data['statistic'] = $this->supplier->upload_statistics();
        $data['product'] = $this->supplier->products();
        $data['categories'] = $this->db->get('categories')->result_array();
        $this->load->view('supplier/upload', $data);
    }

    public function category()
    {
        $data['title'] = 'Category';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $data['general'] = $this->supplier->category();
        $this->load->view('supplier/category', $data);
    }

    public function product()
    {
        $data['title'] = 'Product';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        if (isset($_GET['cid']))
        {
            $product = $this->db->get_where('products', [ 'category_id' => $_GET['cid'] ])->result_array();
            $data['category'] = $this->db->get_where('categories', [ 'id' => $_GET['cid'] ])->row_array();

            $data['title'] = $data['category']['name'];
            $data['subtitle'] = 'category';
        }else{
            $product = $this->db->get_where('products')->result_array();
            $data['categories'] = $this->db->get('categories')->result_array();
        }
        
        $data['product'] = $this->supplier->rewrapp_product($product);
        $data['user'] = $this->on_session;
        $this->load->view('supplier/product', $data);
    }

    // =====

    public function addcategory()
    {
        $data = $this->_read_data_category();
        $this->db->set('created', time());
        $this->db->insert('categories', $data);
        
        $c = $this->db->order_by('created', 'DESC')->get('categories')->row_array();
        $c = $this->supplier->rewrapp_data_category($c);
        
        echo json_encode($c);
    }

    private function _read_data_category()
    {
        return [
            'name'          => $_POST['name'],
            'description'   => $_POST['description'],
            'global_id'     => $_POST['global_id']
        ];
    }

    public function showcategory()
    {
        $id = $this->input->get('id');
        $category = $this->db->get_where('categories', [ 'id' => $id ])->row_array();
        echo json_encode($category);
    }

    public function updatecategory()
    {
        $id = $this->input->get('id');
        $data = $this->_read_data_category();
        $this->db->set('updated', time());
        $this->db->update('categories', $data, [ 'id' => $id]);
        
        $c = $this->db->get_where('categories', [ 'id' => $id ])->row_array();
        $c = $this->supplier->rewrapp_data_category($c);
        
        echo json_encode($c);
    }

    public function removecategory()
    {
        $id = $this->input->get('id');

		$this->db->delete('categories', ['id' => $id ]);

        $result = $this->db->get_where('products', ['category_id' => $id])->result_array();
        if (!$result) return true;

		foreach ($result as $product)
		{
			$global_id = $product['global_id'];
			
			if ($product['image'] !== 'dummy_image.jpg') {
				unlink(FCPATH . 'src/img/product/' . $product['image']);
			}
			$this->db->delete('products', ['category_id' => $product['category_id']]);

			$this->_update_globals($global_id);
		}
    }

    // =====

    public function addproduct()
    {
        $this->_validity_product();

        $data = $this->_read_data_product();
        $this->db->insert('products', $data);

        $this->_update_globals($data['global_id']);

        $uri_concat="";
        if (isset($_GET['cid'])) $uri_concat = "&cid=" . $data['category_id'];
        if (isset($_GET['redirect'])) {
            redirect($_GET['redirect'] . $this->requiredparams . $uri_concat);
        }else{
            redirect('supplier/upload' . $this->requiredparams . $uri_concat);
        }
    }

    public function showproduct()
    {
		$id = $this->input->get('id');
        $product = $this->db->get_where('products', ['id' => $id])->row_array();
		echo json_encode($product);
    }

    public function updateproduct()
    {
        $id = $this->input->get('id');

        $this->_validity_product();
        
        $data = $this->_read_data_product();
        $this->db->update('products', $data, ['id' => $id]);

        $this->_update_globals($data['global_id']);

        $uri_concat="";
        if ($_GET['cid']) $uri_concat = "&cid=" . $data['category_id'];
        if (isset($_GET['redirect'])) {
            redirect($_GET['redirect'] . $this->requiredparams . $uri_concat);
        }else{
            redirect('supplier/upload' . $this->requiredparams . $uri_concat);
        }
    }

    public function removeproduct()
    {
        $id = $this->input->get('id');
        $global_id = $this->input->get('global_id');
        $image = $this->input->get('image');

		if ($image !== 'dummy_image.jpg') {
			unlink(FCPATH . 'src/img/product/' . $image);
		}
		$this->db->delete('products', [ 'id' => $id ]);
		
        $this->_update_globals($global_id);
    }

    private function _validity_product()
    {
        $this->load->library('form_validation');
		$this->load->model('Rules_model');
		$config = $this->Rules_model->product();
		$this->form_validation->set_rules($config);

        if ($this->form_validation->run() == FALSE) return FALSE;
    }

    private function _read_data_product()
    {
        if ($_FILES['image'] && strlen($_FILES['image']['name']) > 0)
        {
            $image = $_FILES['image']['name'];
            $config['upload_path']      = FCPATH . 'src/img/product/';
            $config['allowed_types']    = 'gif|jpg|png';
            $config['max_size']         = 5120;
            $config['encrypt_name'] 	= TRUE;

            
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image'))
            {
                $error = $this->upload->display_errors();
                var_dump($error);
                die;
            }else{
                if (isset($_GET['oldimage']) && $_GET['oldimage'] !== 'dummy_image.jpg') {
                    unlink(FCPATH . 'src/img/product/' . $_GET['oldimage']);
                }
                $image_name = $this->upload->data('file_name');
                $this->db->set('image', $image_name);
            }
        }

        $category_id = explode(',', $_POST['category'])[0];
        $global_id = explode(',', $_POST['category'])[1];

        $data = [
            'name'          => $_POST['name'],
            'price'         => $_POST['price'],
            'qty'           => $_POST['qty'],
            'global_id'     => $global_id,
            'category_id'   => $category_id,
            'description'   => $_POST['description'],
            'seo_keyword'   => $_POST['seo_keyword'],
            'suggested'     => $_POST['suggested'],
            'is_ready'      => $_POST['is_ready'],
            'created'       => time()
        ];
        return $data;
    }

    private function _update_globals($global_id)
	{
        $query = $this->db->order_by('price', 'ASC')->get_where('products', ['global_id' => $global_id]);
        $start_price = 0;
        $high_price = 0;
        if ($query->num_rows() > 0) {
            $start_price = $query->row_array()['price'];
            $high_price = $query->last_row()->price;
        }
        $data = ['start_price' => $start_price, 'high_price' => $high_price];
        $this->db->update('globals', $data, ['id' => $global_id]);
    }

}
