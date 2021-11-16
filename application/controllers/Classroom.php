<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Classroom extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Classroom_model','Notification_model']);
    } 

    /*
     * Listing of classroom
     */
    function index()
    {
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('classroom/index?');
        $config['total_rows'] = $this->Classroom_model->get_all_classroom_count();
        $this->pagination->initialize($config);

        $data['classroom'] = $this->Classroom_model->get_all_classroom($params);
        
        $data['_view'] = 'classroom/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new classroom
     */
    function add()
    {   
        if(isset($_POST) && count($_POST) > 0)     
        {   
            $params = array(
				'roomID' => $this->input->post('roomID'),
				'roomOTP' => $this->input->post('roomOTP'),
				'roomName' => $this->input->post('roomName'),
				'roomPin' => $this->input->post('roomPin'),
				'userID' => $this->input->post('userID'),
				'studentPin' => $this->input->post('studentPin'),
				'scheduledDate' => $this->input->post('scheduledDate'),
				'startTime' => $this->input->post('startTime'),
				'endTime' => $this->input->post('endTime'),
				'status' => $this->input->post('status'),
				'time_stamp' => $this->input->post('time_stamp'),
            );
            
            $classroom_id = $this->Classroom_model->add_classroom($params);
            redirect('classroom/index');
        }
        else
        {            
            $data['_view'] = 'classroom/add';
            $this->load->view('layouts/main',$data);
        }
    }  

    /*
     * Editing a classroom
     */
    function edit($Id)
    {   
        // check if the classroom exists before trying to edit it
        $data['classroom'] = $this->Classroom_model->get_classroom($Id);
        
        if(isset($data['classroom']['Id']))
        {
            if(isset($_POST) && count($_POST) > 0)     
            {   
                $params = array(
					'roomID' => $this->input->post('roomID'),
					'roomOTP' => $this->input->post('roomOTP'),
					'roomName' => $this->input->post('roomName'),
					'roomPin' => $this->input->post('roomPin'),
					'userID' => $this->input->post('userID'),
					'studentPin' => $this->input->post('studentPin'),
					'scheduledDate' => $this->input->post('scheduledDate'),
					'startTime' => $this->input->post('startTime'),
					'endTime' => $this->input->post('endTime'),
					'status' => $this->input->post('status'),
					'time_stamp' => $this->input->post('time_stamp'),
                );

                $this->Classroom_model->update_classroom($Id,$params);            
                redirect('classroom/index');
            }
            else
            {
                $data['_view'] = 'classroom/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The classroom you are trying to edit does not exist.');
    } 

    /*
     * Deleting classroom
     */
    function remove($Id)
    {
        $classroom = $this->Classroom_model->get_classroom($Id);

        // check if the classroom exists before trying to delete it
        if(isset($classroom['Id']))
        {
            $this->Classroom_model->delete_classroom($Id);
            redirect('classroom/index');
        }
        else
            show_error('The classroom you are trying to delete does not exist.');
    }
}
