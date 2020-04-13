<?php

class Menu_model extends CI_Model
{

    public function getSubMenu($id=NULL) {
        if ($id !== NULL) {
            $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
                        FROM `user_sub_menu` JOIN `user_menu`
                        ON `user_sub_menu`.`menu_id` = `user_menu`.`id`
                        WHERE `user_sub_menu`.`id` = $id
            ";
            return $this->db->query($query)->row_array();
        }else{
            $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu`
                        FROM `user_sub_menu` JOIN `user_menu`
                        ON `user_sub_menu`.`menu_id` = `user_menu`.`id`
            ";
            return $this->db->query($query)->result_array();
        }
    }

    public function getMenu()
    {
        return $this->db->query("SELECT `user_menu`.`id`, `menu` FROM `user_menu`")->result_array();
    }

}