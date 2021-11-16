<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class Timetable extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Timetable_model','Exam_model']);
    } 

    /*
     * Listing of timetable
     */
    function index()
    {
        $data['timetable'] = $this->Exam_model->get_all_Exam();
        $data['time'] = true;
        $messages = array();
        foreach ($data['timetable'] as $key => $value) {
            $messages[] = array(
                'id' => $value['id'],
                'title' => $value['title'],
                'start' => $value['date'],
                'end' => $value['date'],
                'className' => "fc-event-success",
                'description' =>'The examen hours start: '.$value['start'].' and finished '.$value['end'],
                
            );
        }

        $data_value = json_encode($messages);
        $data['value'] = $data_value;
        $this->load->view('header_footer/header_admin');
        $this->load->view('timetable/index',$data);
		$this->load->view('header_footer/footer_admin',$data);
    }



    /*
     * Adding a new timetable
     */
    function add()
    {   
        if(isset($_POST) && count($_POST) > 0)     
        {   
            $params = array(
				'courseID' => $this->input->post('courseID'),
				'examRoom' => $this->input->post('examRoom'),
				'examTime' => $this->input->post('examTime'),
				'paper' => $this->input->post('paper'),
				'exam_date' => $this->input->post('exam_date'),
				'time_stamp' => $this->input->post('time_stamp'),
            );
            
            $timetable_id = $this->Timetable_model->add_timetable($params);
            redirect('timetable/index');
        }
        else
        {            
            $data['_view'] = 'timetable/add';
            $this->load->view('layouts/main',$data);
        }
    }  

    /*
     * Editing a timetable
     */
    function edit($id)
    {   
        // check if the timetable exists before trying to edit it
        $data['timetable'] = $this->Timetable_model->get_timetable($id);
        
        if(isset($data['timetable']['id']))
        {
            if(isset($_POST) && count($_POST) > 0)     
            {   
                $params = array(
					'courseID' => $this->input->post('courseID'),
					'examRoom' => $this->input->post('examRoom'),
					'examTime' => $this->input->post('examTime'),
					'paper' => $this->input->post('paper'),
					'exam_date' => $this->input->post('exam_date'),
					'time_stamp' => $this->input->post('time_stamp'),
                );

                $this->Timetable_model->update_timetable($id,$params);            
                redirect('timetable/index');
            }
            else
            {
                $data['_view'] = 'timetable/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The timetable you are trying to edit does not exist.');
    } 

    /*
     * Deleting timetable
     */
    function remove($id)
    {
        $timetable = $this->Timetable_model->get_timetable($id);

        // check if the timetable exists before trying to delete it
        if(isset($timetable['id']))
        {
            $this->Timetable_model->delete_timetable($id);
            redirect('timetable/index');
        }
        else
            show_error('The timetable you are trying to delete does not exist.');
    }
    
}
