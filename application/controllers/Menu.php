<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller
{

    private $on_session;
    private $requiredparams;

    public function __construct()
	{
		parent::__construct();
        in_session();
        $this->on_session = $this->db->get_where('users', [ 'username' => $_GET['username'] ])->row_array();
        $this->requiredparams = '?session=' . $_GET['session'] . '&username=' . $_GET['username'];
    }

    public function index()
    {
        $data['title'] = 'Menu Management';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $data['user'] = $this->on_session;
        $data['menu'] = $this->db->get('user_menu')->result_array();
        $this->load->view('menu/index', $data);
    }

    public function submenu()
    {
        $data['title'] = 'Sub Menu Management';
        $data['subtitle'] = '';
        $data['metadescription'] = '';
        $data['metakeyword'] = '';

        $this->load->model('Menu_model', 'menu');
        
        $data['menu'] = $this->menu->getMenu();
        $data['subMenu'] = $this->menu->getSubMenu();
        $data['user'] = $this->on_session;
        $this->load->view('menu/submenu', $data);
    }

    public function addsubmenu()
    {
        $this->db->insert('user_sub_menu', [
            'menu_id' => $_POST['menu_id'],
            'name' => $_POST['name'],
            'url' => $_POST['url'],
            'icon' => $_POST['icon'],
            'is_active' => $_POST['is_active']
        ]);
        $menu = $this->db->order_by('id', 'DESC')->get('user_sub_menu')->row_array();
        echo json_encode($menu);
    }

    public function showsubmenu()
    {
        $this->load->model('Menu_model', 'menu');
        
        $menu = $this->menu->getMenu();
        $submenu = $this->menu->getSubMenu($_GET['id']);
        $data = [
            'menu' => $menu,
            'submenu' => $submenu
        ];
        echo json_encode($data);
    }

    public function updatesubmenu()
    {
        $data = [
            'menu_id' => $_POST['menu_id'],
            'name' => $_POST['name'],
            'url' => $_POST['url'],
            'icon' => $_POST['icon'],
            'is_active' => $_POST['is_active']
        ];
        $this->db->update('user_sub_menu', $data, [ 'id' => $_POST['id'] ]);
        echo json_encode($data);
    }

    public function removesubmenu()
    {
        $this->db->delete('user_sub_menu', [ 'id' => $_GET['id'] ]);
        echo json_encode(['id' => $_GET['id']]);
    }

    public function addmenu()
    {
        $this->db->insert('user_menu', [
            'menu' => $_POST['menu']
        ]);
        $menu = $this->db->order_by('id', 'DESC')->get('user_menu')->row_array();
        echo json_encode($menu);
    }

    public function showmenu()
    {
        $menu = $this->db->get_where('user_menu', [ 'id' => $_GET['id'] ])->row_array();
        echo json_encode($menu);
    }

    public function updatemenu()
    {
        $this->db->update('user_menu', [ 'menu' => $_POST['menu'] ], [ 'id' => $_POST['id'] ]);
        $data = [ 'id' => $_POST['id'], 'menu' => $_POST['menu'] ];
        echo json_encode($data);
    }

    public function removemenu()
    {
        $this->db->delete('user_menu', [ 'id' => $_GET['id'] ]);
        echo json_encode(['id' => $_GET['id']]);
    }

}
