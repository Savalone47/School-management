<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
class Notification extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['Notification_model']);
    }
    /*
     * Listing of notification
     */
    public function index() {
        $params['limit'] = RECORDS_PER_PAGE;
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        $config = $this->config->item('pagination');
        $config['base_url'] = site_url('notification/index?');
        $config['total_rows'] = $this->Notification_model->get_all_notification_count();
        $this->pagination->initialize($config);

        $data['notification'] = $this->Notification_model->get_all_notification();

        $this->load->view('header_footer/header_admin');
        $this->load->view('notification/index', $data);
        $this->load->view('header_footer/footer_admin');
    }

    /*
     * Adding a new notification
     */
    public function add(){
        if(isset($_POST) && count($_POST) > 0)     
        {   
            $params = array(
				'title' => $this->input->post('title'),
				'author' => $this->input->post('author'),
				'notification' => $this->input->post('notification'),
                'created_at' => $this->input->post('created_at'),
            );
            
            $notification_id = $this->Notification_model->add_notification($params);
            redirect('notification/index');
        }
        else {
            $this->load->view('header_footer/header_admin');
            $this->load->view('notification/add');
            $this->load->view('header_footer/footer_admin');
        }
    }  

    /*
     * Editing a notification
     */
    public function edit($id) {
        // check if the notification exists before trying to edit it
        $data['notification'] = $this->Notification_model->get_notifications($id);

        if(isset($data['notification']['id'])) {

            if(isset($_POST) && count($_POST) > 0) {

                $params = [
					'title' => $this->input->post('title'),
					'author' => $this->input->post('author'),
					'notification' => $this->input->post('notification'),
                    'created_at' => $this->input->post('created_at')
                ];
                $this->Notification_model->update_notification($id,$params);
                $this->session->set_flashdata(['error'=>$error['error']]);
                redirect('notification/index');

            } else {
                $this->load->view('header_footer/header_admin');
                $this->load->view('notification/edit', $data);
                $this->load->view('header_footer/footer_admin');
            }
        } else
            show_error('The notification you are trying to edit does not exist.');
    }

    /*
     * Deleting notification
     */
    public function remove($id)
    {
        $notification = $this->Notification_model->get_notifications($id);

        // check if the notification exists before trying to delete it
        if(isset($notification['id']))
        {
            $this->Notification_model->delete_notification($id);
            redirect('notification/index');
        }
        else
            show_error('The notification you are trying to delete does not exist.');
    }

    public function notice_detail($id){
        $data['notification'] =$this->Notification_model->get_notification($id);

        $this->load->view('header_footer/header_admin');
        $this->load->view('notification/notification_detail', $data);
        $this->load->view('header_footer/footer_admin');
    }

    public function notice($id){
        $data['notice'] = $this->Notification_model->get_notifications($id);

        $this->load->view('header_footer/header_admin',);
        $this->load->view('header_footer/notice/notice', $data);
        $this->load->view('header_footer/footer_admin');
    }

    public function viewAnnouncement(){
        $data['Announces'] = $this->Notification_model->get_all_notification();
        $this->load->view('header_footer/header');
        $this->load->view('notification/announcement',$data);
        $this->load->view('header_footer/footer');
    }

}
