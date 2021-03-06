<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Userlog_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get userlog by userID
     */
    function get_userlog($userID)
    {
        return $this->db->get_where('userlog',array('userID'=>$userID))->row_array();
    }
        
    /*
     * Get all userlog
     */
    function get_all_userlog()
    {
        $this->db->order_by('userID', 'desc');
        return $this->db->get('userlog')->result_array();
    }
        
    /*
     * function to add new userlog
     */
    function add_userlog($params)
    {
        $this->db->insert('userlog',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update userlog
     */
    function update_userlog($userID,$params)
    {
        $this->db->where('userID',$userID);
        return $this->db->update('userlog',$params);
    }
    
    /*
     * function to delete userlog
     */
    function delete_userlog($userID)
    {
        return $this->db->delete('userlog',array('userID'=>$userID));
    }
}
