<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Cart_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get cart by cartID
     */
    function get_cart($cartID)
    {
        return $this->db->get_where('cart',array('cartID'=>$cartID))->row_array();
    }
    
    /*
     * Get all cart count
     */
    function get_all_cart_count()
    {
        $this->db->from('cart');
        return $this->db->count_all_results();
    }
        
    /*
     * Get all cart
     */
    function get_all_cart($params = array())
    {
        $this->db->order_by('cartID', 'desc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        return $this->db->get('cart')->result_array();
    }
        
    /*
     * function to add new cart
     */
    function add_cart($params)
    {
        $this->db->insert('cart',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update cart
     */
    function update_cart($cartID,$params)
    {
        $this->db->where('cartID',$cartID);
        return $this->db->update('cart',$params);
    }
    
    /*
     * function to delete cart
     */
    function delete_cart($cartID)
    {
        return $this->db->delete('cart',array('cartID'=>$cartID));
    }
}
