<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*контроллер главной страницы*/
class Mainnew extends CI_Controller {

    /*тут хранятся настройки, закгружаются в конструкторе*/
    public $settings;
    public $data;

    public function __construct()
    {
        parent::__construct();

        parent::__construct();
        /*Загружаем  библиотеку сессий*/
        $this->load->library('session');


        /*Загружаем модели*/


        /*Закгружаем хелперы*/
        $this->load->helper('form');
        $this->load->helper('url');

        $this->load->model('auth_model');
        $this->load->model('ipre_model');
        $this->load->model('patient_model');
    }

	public function index()
	{
        /*переменные для языков описанны тут: \application\language\*/

        $this->data['patients_link']=site_url('patients');
        $this->data['lpu_link']=site_url('lpu');
        $this->data['page']='main';
        $this->data['auth']=$this->session->userdata('auth');


        if( $this->auth_model->IsLogin())
        {
            if(isset($_POST['patient_id']))
            {
                $this->patient_model->UpdateFDate($_POST['patient_id'],$_POST['f_date']);

                header('Location: '.base_url());
                exit;
            }
            else
            {
                $this->data['auth']=$this->session->userdata('auth');

                $this->data['users'] = $this->auth_model->GetAllUsers();

                $this->data['js'] =  $this->load->view('js',$this->data,true);


                $this->load->view('nf_head',$this->data);
                $this->load->view('navbar/nf_admin_topnav',$this->data);

                /*шаблон страницы*/
                $this->load->view('main_new',$this->data);
                $this->load->view('navbar/nf_admin',$this->data);
                $this->load->view('nf_footer',$this->data);
            }

        }
        else
        {
            header('Location: '.base_url('auth'));
            exit;
        }

	}
}
