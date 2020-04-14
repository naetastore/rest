<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Superuser extends CI_Controller
{

    private $on_session;
    private $requiredparams;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
        $this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
        $this->load->model('Superuser_model', 'superuser');
    }

    public function index()
    {
        $this->webclient();
    }
    
    public function webclient()
    {
        $data['title'] = 'Web Client';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $d = $this->superuser->get();

        $data['user'] = $this->on_session;
        $data['keys'] = $d['keys'];
        $data['users'] = $d['users'];
        $this->load->view('superuser/webclient', $data);
    }

    public function add_webclient()
    {
        $data = [
            'user_id' => $_POST['user_id'],
            'key' => $this->_generate_key(),
            'level' => 1,
            'ignore_limits' => 0,
            'is_private_key' => 0,
            'web_app' => $_POST['web_app'],
            'date_created' => time(),
        ];
        $this->db->insert('keys', $data);

        $user = $this->db->query("SELECT `users`.`username`, `id` FROM `users` WHERE `id` = {$_POST['user_id']}")->row_array();
        $data = $this->db->get_where('keys', [ 'web_app' => $_POST['web_app'] ])->row_array();
        $data['date_created'] = date('d F y', $data['date_created']);
        $data['user'] = $this->superuser->generate_user($user);
        echo json_encode($data);
    }

    public function update_webclient()
    {
        $data = [];
        if (isset($_POST['web_app'])) $data['web_app'] = $_POST['web_app'];
        if (isset($_POST['key'])) $data['key'] = $_POST['key'];

        $this->db->update('keys', $data, [ 'id' => $_POST['id'] ]);
    }

    private function _generate_key()
    {
        do
        {
            $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);
            if ($salt === FALSE)
            {
                $salt = hash('sha256', time() . mt_rand());
            }
            $new_key = substr($salt, 0, 40); //40 is rest_key_length
        }
        while ($this->_key_exists($new_key));
        return $new_key;
    }

    private function _key_exists($key)
    {
        return $this->db->get_where('keys', ['key' => $key ])->num_rows() > 0;
    }

    public function remove_webclient()
    {
        $this->db->delete('keys', [ 'id' => $_GET['id'] ]);
        echo json_encode([ 'id' => $_GET['id'] ]);
    }

    // ======

    public function role()
    {
        $data['title'] = 'Role';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
        
        $data['user'] = $this->on_session;

        // $this->db->where('id !=', 1);
        $data['role'] = $this->db->get('user_role')->result_array();
        $this->load->view('superuser/role', $data);
    }

    public function addrole()
    {
        $this->db->insert('user_role', [ 'role' => $_POST['role'] ]);
        echo json_encode($this->db->get_where('user_role', [ 'role' => $_POST['role'] ])->row_array());
    }

    public function showrole()
    {
        echo json_encode($this->db->get_where('user_role', ['id' => $_GET['id'] ])->row_array());
    }

    public function updaterole()
    {
        $this->db->update('user_role', [ 'role' => $_POST['role'] ], [ 'id' => $_POST['id'] ]);
        echo json_encode([
            'id' => $_POST['id'],
            'role' => $_POST['role']
        ]);
    }

    public function removerole()
    {
        if ($_GET['id'] == 1) {
            echo json_encode([
                'status' => false,
                'message' => "Super User cant to remove."
            ]);
            return;
        }
        $this->db->delete('user_role', [ 'id' => $_GET['id'] ]);
        echo json_encode([
            'status' => true,
            'id' => $_GET['id']
        ]);
    }

    // ======
    
    public function roleaccess()
    {
        $data['title'] = 'User Access Menu';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
        
        $data['user'] = $this->on_session;
        $data['role'] = $this->db->get_where('user_role', [ 'id' => $_GET['role_id'] ])->row_array();
        $data['subtitle'] = 'Role: ' . $data['role']['role'];
        $this->db->where('id !=', 1);
        $data['menu'] = $this->db->get('user_menu')->result_array();

        $data['order_action'] = $this->db->get('order_action')->result_array();
        $data['order_status'] = $this->db->get('order_status')->result_array();
        $this->load->view('superuser/roleaccess', $data);
    }

    public function changeaccess()
    {
        $data = [
            'role_id' => $_POST['roleId'],
            'menu_id' => $_POST['menuId']
        ];
        $result = $this->db->get_where('user_access_menu', $data);
        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
        }else{
            $this->db->delete('user_access_menu', $data);
        }
    }

    // ========
    
    public function orderaccess()
    {
        $data['title'] = 'API Control';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
        
        $data['user'] = $this->on_session;
        $data['role'] = $this->db->get_where('user_role', [ 'id' => $_GET['role_id'] ])->row_array();
        $data['subtitle'] = 'Role: ' . $data['role']['role'] . ', ' . 'API: Order.';
    
        $data['order_action'] = $this->db->get('order_action')->result_array();
        $data['order_status'] = $this->db->get('order_status')->result_array();
        $this->load->view('superuser/orderaccess', $data);
    }

    public function change_orderaccess()
    {
        $data = [
            'role_id' => $_POST['roleId'],
            'status_id' => $_POST['statusId'],
            'action_id' => $_POST['actionId']
        ];
        $result = $this->db->get_where('order_access', $data);
        if ($result->num_rows() < 1) {
            $this->db->insert('order_access', $data);
        }else{
            $this->db->delete('order_access', $data);
        }
    }

    // =======

    public function orderui()
    {
        $data['title'] = 'Order UI Config';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';
        
        $data['user'] = $this->on_session;
        $data['role'] = $this->db->get_where('user_role', [ 'id' => $_GET['role_id'] ])->row_array();
        $data['ui'] = $this->db->get('order_ui')->result_array();
        $data['subtitle'] = 'Role: ' . $data['role']['role'] . ', ' . 'API: Order.';
        $this->load->view('superuser/orderui', $data);
    }

    public function changeorderui()
    {
        $data = [
            'role_id' => $_POST['roleId'],
            'ui_id' => $_POST['uiId']
        ];
        $result = $this->db->get_where('order_ui_config', $data);
        if ($result->num_rows() < 1) {
            $this->db->insert('order_ui_config', $data);
        }else{
            $this->db->delete('order_ui_config', $data);
        }
    }

    // =======

    public function settings()
    {
        $data['title'] = 'Settings';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $data['product_status'] = $this->db->get('product_status')->result_array();
        $data['product_action'] = $this->db->get('product_action')->result_array();
        $this->load->view('superuser/settings', $data);
    }
    
    public function get_current_setting()
    {
        $setting = $_GET['setting'];

        $this->db->where('id !=', 1);
        $role = $this->db->get('user_role')->result_array();
        $data = ['role' => $role];
        
        $current = $this->db->get_where('api_settings', ['setting' => $setting])->row_array();
        if ($current) {
            $queryRole = "SELECT `user_role`.`role` FROM `user_role` WHERE `id` = {$current['value']}";
            $roleName = $this->db->query($queryRole)->row_array()['role'];
            $current['role'] = $roleName;
            
            $data['current'] = $current;
        }
        
        echo json_encode($data);
    }

    public function change_setting()
    {
        $data = [
            'setting' => $_POST['setting'],
            'value' => $_POST['value']
        ];

        $setting = $this->db->get_where('api_settings', [ 'setting' => $data['setting'] ]);
        if ($setting->num_rows() < 1) {
            $this->db->insert('api_settings', $data);
        }else{
            $this->db->update('api_settings', $data, [ 'setting' => $data['setting'] ]);
        }
        
        $affected_rows = $this->db->affected_rows();
        if ($affected_rows > 0) {
            echo json_encode([
                'status' => true, 
                'message' => $data['setting'] . ' has been changed.'
            ]);
        }else{
            echo json_encode([
                'status' => false, 
                'message' => 'failed to changed!'
            ]);
        }
    }

    public function change_product_action_allowed()
    {
        $data = [
            'status_id' => $_POST['status_id'],
            'action_id' => $_POST['action_id']
        ];
        $query = $this->db->get_where('product_action_allowed', $data);
        if ($query->num_rows() > 0) {
            $this->db->delete('product_action_allowed', $data);
        }else{
            $this->db->insert('product_action_allowed', $data);
        }
    }

}
