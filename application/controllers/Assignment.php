<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Assignment extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Assignment_model','Notification_model']);
    } 

    /*
     * Listing of assignment
     */
    function index()
    {
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('assignment/index?');
        $config['total_rows'] = $this->Assignment_model->get_all_assignment_count();
        $this->pagination->initialize($config);

        $data['assignment'] = $this->Assignment_model->get_all_assignment($params);
        
        $data['_view'] = 'assignment/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new assignment
     */
    function add()
    {   
        $this->load->library('form_validation');

		$this->form_validation->set_rules('assignment','Assignment','required');
		
		if($this->form_validation->run())     
        {   
            $params = array(
				'assignment' => $this->input->post('assignment'),
				'assignmentNo' => $this->input->post('assignmentNo'),
				'marks' => $this->input->post('marks'),
				'topicName' => $this->input->post('topicName'),
				'dueDate' => $this->input->post('dueDate'),
				'moduleID' => $this->input->post('moduleID'),
				'facilitatorID' => $this->input->post('facilitatorID'),
				'deleteStatus' => $this->input->post('deleteStatus'),
				'time_stamp' => $this->input->post('time_stamp'),
				'comment' => $this->input->post('comment'),
            );
            
            $assignment_id = $this->Assignment_model->add_assignment($params);
            redirect('assignment/index');
        }
        else
        {            
            $data['_view'] = 'assignment/add';
            $this->load->view('layouts/main',$data);
        }
    }  

    /*
     * Editing a assignment
     */
    function edit($id)
    {   
        // check if the assignment exists before trying to edit it
        $data['assignment'] = $this->Assignment_model->get_assignment($id);
        
        if(isset($data['assignment']['id']))
        {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('assignment','Assignment','required');
		
			if($this->form_validation->run())     
            {   
                $params = array(
					'assignment' => $this->input->post('assignment'),
					'assignmentNo' => $this->input->post('assignmentNo'),
					'marks' => $this->input->post('marks'),
					'topicName' => $this->input->post('topicName'),
					'dueDate' => $this->input->post('dueDate'),
					'moduleID' => $this->input->post('moduleID'),
					'facilitatorID' => $this->input->post('facilitatorID'),
					'deleteStatus' => $this->input->post('deleteStatus'),
					'time_stamp' => $this->input->post('time_stamp'),
					'comment' => $this->input->post('comment'),
                );

                $this->Assignment_model->update_assignment($id,$params);            
                redirect('assignment/index');
            }
            else
            {
                $data['_view'] = 'assignment/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The assignment you are trying to edit does not exist.');
    } 

    /*
     * Deleting assignment
     */
    function remove($id)
    {
        $assignment = $this->Assignment_model->get_assignment($id);

        // check if the assignment exists before trying to delete it
        if(isset($assignment['id']))
        {
            $this->Assignment_model->delete_assignment($id);
            redirect('assignment/index');
        }
        else
            show_error('The assignment you are trying to delete does not exist.');
    }
}
