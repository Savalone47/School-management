<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Department extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Department_model','Course_model', 'Student_model','Notification_model']);
    } 

    /*
     * Listing of department
     */
    function index()
    {
        $params['limit'] = RECORDS_PER_PAGE; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('department/index?');
        $config['total_rows'] = $this->Department_model->get_all_department_count();
        $this->pagination->initialize($config);

        $data['department'] = $this->Department_model->get_all_department();
        
        $this->load->view('header_footer/header_admin');
        $this->load->view('department/index',$data);
        $this->load->view('header_footer/footer_admin');
    }


    function courses($id){

        $data['courses'] = $this->Course_model->get_departement_courses($id);
        $data['department'] = $this->Department_model->get_department($id);

        $this->load->view('header_footer/header_admin');
        $this->load->view('department/courses_department',$data);
        $this->load->view('header_footer/footer_admin');
    }
    /*
     * Adding a new department
     */
    function add()
    {   
        $this->load->library('form_validation');
		$this->form_validation->set_rules('departmentName','DepartmentName','required|max_length[250]');
		
		if($this->form_validation->run())     
        {
            $config['upload_path']= './uploads/campus/';
            $config['allowed_types']= 'jpg|png|jpeg';
            $new_file=uniqid('campus');
            $config['file_name'] = $new_file.".jpg";

            $params = array(
                'departmentName' => $this->input->post('departmentName'),
                'hodID' => $this->input->post('hodID'),
                'time_stamp' => $this->input->post('time_stamp'),
                'logo' => '/uploads/campus/'. $config['file_name'],
            );

            $this->load->library('upload', $config);

            $department_id = $this->Department_model->add_department($params);
            redirect('department/index');

            if (!$this->upload->do_upload('logo'))
            {
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata(['error'=>$error['error']]);
                redirect($_SERVER['HTTP_REFERER']); 
            }
            else{
                show_error('The upload you are trying to edit does not exist.');
            }
        } else {
            $this->load->view('header_footer/header_admin');
            $this->load->view('department/add');
            $this->load->view('header_footer/footer_admin');

        }
    }  

    /*
     * Editing a department
     */
    function edit($departmentID)
    {   
        // check if the department exists before trying to edit it
        $data['department'] = $this->Department_model->get_department($departmentID);
        
        if(isset($data['department']['departmentID'])) {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('departmentName','DepartmentName','required|max_length[250]');
		
			if($this->form_validation->run()) {
                $params = array(
					'departmentName' => $this->input->post('departmentName'),
					'hodID' => $this->input->post('hodID'),
					'time_stamp' => $this->input->post('time_stamp'),
                );
                $this->Department_model->update_department($departmentID,$params);            
                redirect('department/index');
            }
            else {
                $this->load->view('header_footer/header_admin');
                $this->load->view('department/edit', $data);
                $this->load->view('header_footer/footer_admin');
            }
        }
        else
            show_error('The department you are trying to edit does not exist.');
    } 

    /*
     * Deleting department
     */
    public function remove($departmentID)
    {
        $department = $this->Department_model->get_department($departmentID);

        // check if the department exists before trying to delete it
        if(isset($department['departmentID']))
        {
            $this->Department_model->delete_department($departmentID);
            redirect('department/index');
        }
        else
            show_error('The department you are trying to delete does not exist.');
    }
    
    
    public  function viewCoursesOfDepartement() {

      if($this->session->status and $this->session->type_user == 3){

        $data['courses'] = $this->Course_model->get_course_of_departement($this->session->id_user);

        $this->load->view('header_footer/header_amdin');
        $this->load->view('department/course', $data);
        $this->load->view('header_footer/footer_admin');
      }
      else{
          redirect($_SERVER['HTTP_REFRERE']);
      }
    }

    function course_details($coursesID){
        $data['course'] =$this->Course_model->get_course($coursesID);
        $data['allStudent'] = $this->Student_model->get_all_students();
        $this->load->view('department/course_details',$data);
    }

}
