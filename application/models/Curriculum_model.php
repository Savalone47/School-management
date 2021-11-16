<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Curriculum_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get curriculum by id
     */
    function get_curriculum($id)
    {
        return $this->db->get_where('curriculum',array('id'=>$id))->row_array();
    }
    
    /*
     * Get all curriculum count
     */
    function get_all_curriculum_count()
    {
        $this->db->from('curriculum');
        return $this->db->count_all_results();
    }
        
    /*
     * Get all curriculum
     */
    function get_all_curriculum($params = array())
    {
        $this->db->order_by('id', 'desc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        return $this->db->get('curriculum')->result_array();
    }
        
    /*
     * function to add new curriculum
     */
    function add_curriculum($params)
    {
        $this->db->insert('curriculum',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update curriculum
     */
    function update_curriculum($id,$params)
    {
        $this->db->where('id',$id);
        return $this->db->update('curriculum',$params);
    }
    
    /*
     * function to delete curriculum
     */
    function delete_curriculum($id)
    {
        return $this->db->delete('curriculum',array('id'=>$id));
    }
}
