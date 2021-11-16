<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Event_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Get event by eventID
     */
    function get_event($eventID)
    {
        return $this->db->get_where('events',array('eventID'=>$eventID))->row_array();
    }
    
    /*
     * Get all events count
     */
    function get_all_events_count()
    {
        $this->db->from('events');
        return $this->db->count_all_results();
    }
        
    /*
     * Get all events
     */
    function get_all_events($params = array())
    {
        $this->db->order_by('eventID', 'desc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        return $this->db->get('events')->result_array();
    }
        
    /*
     * function to add new event
     */
    function add_event($params)
    {
        $this->db->insert('events',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update event
     */
    function update_event($eventID,$params)
    {
        $this->db->where('eventID',$eventID);
        return $this->db->update('events',$params);
    }
    
    /*
     * function to delete event
     */
    function delete_event($eventID)
    {
        return $this->db->delete('events',array('eventID'=>$eventID));
    }
}
