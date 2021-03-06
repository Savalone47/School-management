<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Management extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Management_model');
    } 

    /*
     * Listing of management
     */
    function index()
    {
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('management/index?');
        $config['total_rows'] = $this->Management_model->get_all_management_count();
        $this->pagination->initialize($config);

        $data['management'] = $this->Management_model->get_all_management($params);
        
        $data['_view'] = 'management/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new management
     */
    function add()
    {   
        if(isset($_POST) && count($_POST) > 0)     
        {   
            $params = array(
				'managementName' => $this->input->post('managementName'),
				'lastName' => $this->input->post('lastName'),
				'title' => $this->input->post('title'),
				'managementEmail' => $this->input->post('managementEmail'),
				'managementContact' => $this->input->post('managementContact'),
				'managementPassword' => $this->input->post('managementPassword'),
				'managementPhoto' => $this->input->post('managementPhoto'),
				'managementLevel' => $this->input->post('managementLevel'),
				'otp' => $this->input->post('otp'),
				'address' => $this->input->post('address'),
				'securityAns' => $this->input->post('securityAns'),
				'managementStatus' => $this->input->post('managementStatus'),
				'gender' => $this->input->post('gender'),
				'programme' => $this->input->post('programme'),
				'managementBio' => $this->input->post('managementBio'),
            );
            
            $management_id = $this->Management_model->add_management($params);
            redirect('management/index');
        }
        else
        {            
            $data['_view'] = 'management/add';
            $this->load->view('layouts/main',$data);
        }
    }  

    /*
     * Editing a management
     */
    function edit($managementID)
    {   
        // check if the management exists before trying to edit it
        $data['management'] = $this->Management_model->get_management($managementID);
        
        if(isset($data['management']['managementID']))
        {
            if(isset($_POST) && count($_POST) > 0)     
            {   
                $params = array(
					'managementName' => $this->input->post('managementName'),
					'lastName' => $this->input->post('lastName'),
					'title' => $this->input->post('title'),
					'managementEmail' => $this->input->post('managementEmail'),
					'managementContact' => $this->input->post('managementContact'),
					'managementPassword' => $this->input->post('managementPassword'),
					'managementPhoto' => $this->input->post('managementPhoto'),
					'managementLevel' => $this->input->post('managementLevel'),
					'otp' => $this->input->post('otp'),
					'address' => $this->input->post('address'),
					'securityAns' => $this->input->post('securityAns'),
					'managementStatus' => $this->input->post('managementStatus'),
					'gender' => $this->input->post('gender'),
					'programme' => $this->input->post('programme'),
					'managementBio' => $this->input->post('managementBio'),
                );

                $this->Management_model->update_management($managementID,$params);            
                redirect('management/index');
            }
            else
            {
                $data['_view'] = 'management/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The management you are trying to edit does not exist.');
    } 

    /*
     * Deleting management
     */
    function remove($managementID)
    {
        $management = $this->Management_model->get_management($managementID);

        // check if the management exists before trying to delete it
        if(isset($management['managementID']))
        {
            $this->Management_model->delete_management($managementID);
            redirect('management/index');
        }
        else
            show_error('The management you are trying to delete does not exist.');
    }
    
}
