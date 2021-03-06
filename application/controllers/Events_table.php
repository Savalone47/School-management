<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Events_table extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Events_table_model','Notification_model']);
    } 

    /*
     * Listing of events_table
     */
    function index()
    {
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('events_table/index?');
        $config['total_rows'] = $this->Events_table_model->get_all_events_table_count();
        $this->pagination->initialize($config);

        $data['events_table'] = $this->Events_table_model->get_all_events_table($params);
        
        $data['_view'] = 'events_table/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new events_table
     */
    function add()
    {   
        $this->load->library('form_validation');

		$this->form_validation->set_rules('title','Title','required|max_length[40]');
		$this->form_validation->set_rules('className','ClassName','required|max_length[40]');
		$this->form_validation->set_rules('start','Start','required');
		$this->form_validation->set_rules('details','Details','required|max_length[250]');
		
		if($this->form_validation->run())     
        {   
            $params = array(
				'title' => $this->input->post('title'),
				'className' => $this->input->post('className'),
				'start' => $this->input->post('start'),
				'end' => $this->input->post('end'),
				'details' => $this->input->post('details'),
				'created_at' => $this->input->post('created_at'),
            );
            
            $events_table_id = $this->Events_table_model->add_events_table($params);
            redirect('events_table/index');
        }
        else
        {            
            $data['_view'] = 'events_table/add';
            $this->load->view('layouts/main',$data);

            $this->load->view('');
        }
    }  

    /*
     * Editing a events_table
     */
    function edit($id)
    {   
        // check if the events_table exists before trying to edit it
        $data['events_table'] = $this->Events_table_model->get_events_table($id);
        
        if(isset($data['events_table']['id']))
        {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('title','Title','required|max_length[40]');
			$this->form_validation->set_rules('className','ClassName','required|max_length[40]');
			$this->form_validation->set_rules('start','Start','required');
			$this->form_validation->set_rules('details','Details','required|max_length[250]');
		
			if($this->form_validation->run())     
            {   
                $params = array(
					'title' => $this->input->post('title'),
					'className' => $this->input->post('className'),
					'start' => $this->input->post('start'),
					'end' => $this->input->post('end'),
					'details' => $this->input->post('details'),
					'created_at' => $this->input->post('created_at'),
                );

                $this->Events_table_model->update_events_table($id,$params);            
                redirect('events_table/index');
            }
            else
            {
                $data['_view'] = 'events_table/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The events_table you are trying to edit does not exist.');
    } 

    /*
     * Deleting events_table
     */
    function remove($id)
    {
        $events_table = $this->Events_table_model->get_events_table($id);

        // check if the events_table exists before trying to delete it
        if(isset($events_table['id']))
        {
            $this->Events_table_model->delete_events_table($id);
            redirect('events_table/index');
        }
        else
            show_error('The events_table you are trying to delete does not exist.');
    }
}
