<?php 

class Auth extends CI_Controller
{
	
	public function index()
	{
		echo 
		"<script>const session = window.localStorage.getItem('naetastore_sess'); 
			const name = window.localStorage.getItem('naetastore_name'); 
			if (session) { window.location.href = '" . base_url('user/profile') . "?session=' + session + '&username=' + name }
		</script>";

		$data['metadescription'] = 'Naeta Rest Server';
		$data['metaauthor'] = 'Andi Jatmiko';
		$this->load->view('auth/index', $data);
	}

	public function signin()
	{
		$username = $this->input->get('username');
		$password = $this->input->get('password');
		
		$user = get_user($username, $password);

		if ($user) {
			$session = create_session();
			$this->db->insert('sessions', [
				'session' 	=> $session,
				'username' 	=> $username,
				'created' 	=> time()
			]);

			$role_id = $user['role_id'];
			$queryMenu = "SELECT `user_menu`.`menu` 
							FROM `user_menu` 
							JOIN `user_access_menu` 
							  ON `user_menu`.`id` = `user_access_menu`.`menu_id` 
						   WHERE `user_access_menu`.`role_id` = $role_id
			";
			$menu = $this->db->query($queryMenu)->row_array();
			$menu = strtolower($menu['menu']);

			echo json_encode([
				'status'	=> true,
				'session'	=> $session,
				'username'	=> $username,
				'redirect'	=> base_url("$menu?session=$session&username=$username")
			]);
		} else {
			echo json_encode([
				'status'	=> false,
				'message'	=> 'Please try again'
			]);
		}
	}

}
