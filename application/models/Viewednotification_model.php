<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Viewednotification_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get viewednotification by id
     */
    function get_viewednotification($id)
    {
        return $this->db->get_where('viewednotification',array('id'=>$id))->row_array();
    }
        
    /*
     * Get all viewednotification
     */
    function get_all_viewednotification()
    {
        $this->db->order_by('id', 'desc');
        return $this->db->get('viewednotification')->result_array();
    }
        
    /*
     * function to add new viewednotification
     */
    function add_viewednotification($params)
    {
        $this->db->insert('viewednotification',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update viewednotification
     */
    function update_viewednotification($id,$params)
    {
        $this->db->where('id',$id);
        return $this->db->update('viewednotification',$params);
    }
    
    /*
     * function to delete viewednotification
     */
    function delete_viewednotification($id)
    {
        return $this->db->delete('viewednotification',array('id'=>$id));
    }
}
