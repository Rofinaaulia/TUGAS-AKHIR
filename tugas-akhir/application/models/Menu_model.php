<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{
    public function getSubMenu()
    {

        // $querySubMenu = "SELECT *  
        //   FROM 'user_sub_menu'
        // WHERE 'menu_id' = $menuId
        //AND 'is_active' = 1
        // "; udah

        //$querySubMenu = $this->db->select('*')
        //->from('user_sub_menu')
        //->where('menu_id', $menuId)
        //->where('is_active', 1)
        //->get()->result_array();

        //$query = "SELECT 'user_sub_menu'.*, 'user_menu'.'menu'
        //      FROM 'user_sub_menu' JOIN 'user_menu'
        //    ON 'user_sub_menu'.'menu_id' = 'user_menu'.'id'

        // $querySubMenu = $this->db->select('*')
        //     ->from('user_sub_menu')
        //     ->get()->result_array();
        // //";
        // return $this->db->query($querySubMenu);
        $query = $this->db->select('user_sub_menu.*, user_menu.menu')
            ->from('user_sub_menu')
            ->join('user_menu', 'user_sub_menu.menu_id = user_menu.id')
            ->get()->result_array();

        return $query;
    }
}
