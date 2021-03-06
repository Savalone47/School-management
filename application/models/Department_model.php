<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Department_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get department by departmentID
     */
    function get_departments($departmentID)
    {
        return $this->db->get_where('department',array('departmentID'=>$departmentID))->result_array();
    }

    function get_department($departmentID)
    {
        return $this->db->get_where('department',array('departmentID'=>$departmentID))->row_array();
    }

    
    /*
     * Get all department count
     */
    function get_all_department_count()
    {
        $this->db->from('department');
        return $this->db->count_all_results();
    }

    function auth_departement($email,$password) {
		
		$this->db->where(['username'=>$email,'password'=>$password]);
		return $this->db->get('department')->result();
	}

    /*
     * Get all department
     */
    function get_all_department($params = array())
    {
        $this->db->order_by('departmentID', 'desc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        return $this->db->get('department')->result_array();
    }
        
    /*
     * function to add new department
     */
    function add_department($params)
    {
        $this->db->insert('department',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update department
     */
    function update_department($departmentID,$params)
    {
        $this->db->where('departmentID',$departmentID);
        return $this->db->update('department',$params);
    }
    
    /*
     * function to delete department
     */
    function delete_department($departmentID)
    {
        return $this->db->delete('department',array('departmentID'=>$departmentID));
    }
}
