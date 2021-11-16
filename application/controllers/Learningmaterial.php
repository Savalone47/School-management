<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */

class Learningmaterial extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['Learningmaterial_model','Learning_model']);
    }


    /*
     * Listing of learningmaterial
     */
    function index()
    {
        $data['learningmaterial'] = $this->Learningmaterial_model->get_all_learningmaterial();
        
        $data['_view'] = 'learningmaterial/index';
        $this->load->view('layouts/main',$data);
    }

    /*
     * Adding a new learningmaterial
     */
    function add($moduleID)
    {

        if(isset($_POST) && count($_POST) > 0)     
        {
            //var_dump($_FILES);die;
            $config['upload_path']= './uploads/campus/';
            $config['allowed_types']= 'pdf|docx|mp4|mp3|png|jpg';
            $new_file=uniqid('campus');
            $config['file_name'] = $new_file.".pdf";

            $this->load->library('upload', $config);


            if ( ! $this->upload->do_upload('myfile'))
            {

                $error = array('error' => $this->upload->display_errors());
                //var_dump($error);die;
                $this->session->set_flashdata(['error'=>$error['error']]);
                redirect($_SERVER['HTTP_REFERER']);
            }
            else{
                $params = array(
                    'file' => '/uploads/campus/'. $config['file_name'],
                    'learningID' => $this->input->post('learningID'),
                    'deleteStatus' => $this->input->post('deleteStatus'),
                    'time_stamp' => $this->input->post('time_stamp'),
                );


                $appKey      = 'ci64ibuw2a253yg';
                $appSecret   = 'dot0ytyvmbmusyq';
                $accessToken = 'JovoRveEPlwAAAAAAAAAAW618VldwdB1NfxvZXfeiMQeBz0a1modx3O9Ezn8wair';
                $link = 'uploads/campus/'. $config['file_name'];

                require FCPATH . 'vendor/autoload.php';

                $client = new Spatie\Dropbox\Client($accessToken);

                //$name = $_FILES["myfile"]["name"];
                $file = $_FILES["myfile"];
                var_dump($file);die();
                $myfile = fopen($file,'r');
                $readed = fread($myfile,filesize($file));
                //var_dump($readed);die();
                $client->upload("/bytesizeBW/test/$name",$readed, $mode='add',$autorename = true);


                $learningmaterial_id = $this->Learningmaterial_model->add_learningmaterial($params);
                //echo 4566;
                redirect("learning/get_learning_of_module/$moduleID");
            }

        }
        else
        {            
            //$data['_view'] = 'learningmaterial/add';
            //$this->load->view('layouts/main',$data);

            $data['moduleid'] = $moduleID;
            $data['lessons'] = $this->Learning_model->get_learning_module($moduleID);
            $this->load->view('header_footer/header_admin');
            $this->load->view('learningmaterial/add',$data);
            $this->load->view('header_footer/footer_admin');
        }
    }  

    /*
     * Editing a learningmaterial
     */
    function edit($materialID)
    {   
        // check if the learningmaterial exists before trying to edit it
        $data['learningmaterial'] = $this->Learningmaterial_model->get_learningmaterial($materialID);
        
        if(isset($data['learningmaterial']['materialID']))
        {
            if(isset($_POST) && count($_POST) > 0)     
            {   
                $params = array(
					'file' => $this->input->post('file'),
					'learningID' => $this->input->post('learningID'),
					'deleteStatus' => $this->input->post('deleteStatus'),
					'time_stamp' => $this->input->post('time_stamp'),
                );

                $this->Learningmaterial_model->update_learningmaterial($materialID,$params);            
                redirect('learningmaterial/index');
            }
            else
            {
                $data['_view'] = 'learningmaterial/edit';
                $this->load->view('layouts/main',$data);
            }
        }
        else
            show_error('The learningmaterial you are trying to edit does not exist.');
    } 

    /*
     * Deleting learningmaterial
     */
    function remove($materialID)
    {
        $learningmaterial = $this->Learningmaterial_model->get_learningmaterial($materialID);

        // check if the learningmaterial exists before trying to delete it
        if(isset($learningmaterial['materialID']))
        {
            $this->Learningmaterial_model->delete_learningmaterial($materialID);
            redirect('learningmaterial/index');
        }
        else
            show_error('The learningmaterial you are trying to delete does not exist.');
    }
    
}
