<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Learningmaterial_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get learningmaterial by materialID
     */
    function get_learningmaterial($materialID)
    {
        return $this->db->get_where('learningmaterial',array('materialID'=>$materialID))->row_array();
    }
        
    /*
     * Get all learningmaterial
     */
    function get_all_learningmaterial()
    {
        $this->db->order_by('materialID', 'desc');
        return $this->db->get('learningmaterial')->result_array();
    }
        
    /*
     * function to add new learningmaterial
     */
    function add_learningmaterial($params)
    {
        $this->db->insert('learningmaterial',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update learningmaterial
     */
    function update_learningmaterial($materialID,$params)
    {
        $this->db->where('materialID',$materialID);
        return $this->db->update('learningmaterial',$params);
    }
    
    /*
     * function to delete learningmaterial
     */
    function delete_learningmaterial($materialID)
    {
        return $this->db->delete('learningmaterial',array('materialID'=>$materialID));
    }
}
