<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class User_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get user by ID
     */
    function get_user($ID)
    {
        return $this->db->get_where('users',array('ID'=>$ID))->row_array();
    }
        
    /*
     * Get all users
     */
    function get_all_users()
    {
        $this->db->order_by('ID', 'desc');
        return $this->db->get('users')->result_array();
    }
        
    /*
     * function to add new user
     */
    function add_user($params)
    {
        $this->db->insert('users',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update user
     */
    function update_user($ID,$params)
    {
        $this->db->where('ID',$ID);
        return $this->db->update('users',$params);
    }
    
    /*
     * function to delete user
     */
    function delete_user($ID)
    {
        return $this->db->delete('users',array('ID'=>$ID));
    }
}
