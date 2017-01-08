<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dsp_patients extends CI_Controller {


    public $data;

    public $root = '/dsp/';

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('functions');
        $this->load->library('form_validation');
        $this->load->model('auth_model');
        $this->load->model('patient_model');
        $this->load->helper('form');
        $this->load->helper('url');

        $this->data['root'] = $this->root;
    }




    public function index()
    {

    }



    /*выдат исходные настройки*/
    public function GetDashboard() {
        /*проверяем аториз*/
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
        } else {
            $res['auth'] = 0;
        }

        echo json_encode($res);
    }

    /*формирует парамтры фильтра из post*/
    public function GetFilterParams($data,$patient){
        $arg=array();

        if((isset($data['sort']))and($data['sort']!=''))
            $arg['sort'] = $data['sort'];
        else $arg['sort'] = 'surname';
        if(isset($data['order']))$arg['order'] = $data['order'];

        if(!isset($data['limit']))  $data['limit'] = 100;
        if(!isset($data['offset']))  $data['offset'] = 0;

        $arg['age_beg'] = 21;
        $arg['age_end'] = 99;
        if(isset($patient['age_beg'])) $arg['age_beg'] = $patient['age_beg'];
        if(isset($patient['age_end'])) $arg['age_end']= $patient['age_end'];


        $arg['month_beg'] = 1;
        $arg['month_end'] = 12;
        if(isset($patient['month_beg'])) $arg['month_beg'] = $patient['month_beg'];
        if(isset($patient['month_end'])) $arg['month_end']= $patient['month_end'];


        if(isset($patient['uch'])) $arg['uch']= $patient['uch'];

        /*только 400 записей максимум*/
        if((int)$data['limit']>400) $data['limit'] = 400;
        if((int)$data['offset']<0) $data['offset'] = 0;

        return array('arg'=>$arg,'data'=>$data);
    }

    public function GetPatients() {
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();

            $data = $this->input->post('data');
            $patient = $this->input->post('patient');

            if($data!='') {

                $d = $this->GetFilterParams($data,$patient);
                $data = $d['data'];
                $arg = $d['arg'];
                $arg['lpucode'] = $res['user']['lpucode'];
                $res['patients']['rows'] = $this->patient_model->GetPatients($arg,$data['limit'],$data['offset']);
                if(count($res['patients']['rows'])>0)
                {
                    /*проставляем статус*/
                    foreach ($res['patients']['rows'] as $key=>$p) {
                        $res['patients']['rows'][$key]['status'] = $this->patient_model->GetPatientStatus($p['enp']);
                    }


                    $res['patients']['total'] = $this->patient_model->GetPatientsTotal($arg);
                }

            }

        } else {
            $res['auth'] = 0;
        }

        echo json_encode($res);
    }

    /*Побщее ко-во подлежащих дисп на лпу*/
    public function GetDspTotalByLPU() {
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();

            $arg=array();

            $arg['lpucode'] = $res['user']['lpucode'];
            $arg['sort'] = 'surname';

            $arg['order'] = 'desc';
            $arg['age_beg'] = 21;
            $arg['age_end'] = 99;

            $arg['month_beg'] = 1;
            $arg['month_end'] = 12;

            $res['total'] = $this->patient_model->GetPatientsTotal($arg);

        } else {
            $res['auth'] = 0;
        }

        echo json_encode($res);
    }

    /*доктора*/
    public function GetRegLPUDoctors(){
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $res['doctors'] = $this->patient_model->GetLPUDoctors($res['user']['lpucode']);

        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*доктора*/
    public function GetRegLPUuch(){
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $res['uch'] = $this->patient_model->GetLPUuch($res['user']['lpucode']);

        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*План дисп на год по зареганому лу*/
    public function GetDspPlanForYear($year){
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $res['DspPlanForYear'] = $this->patient_model->GetDspPlanForYear($res['user']['lpucode'],$year);

        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*выдает пациента по его енп привзанного к зареганному юзеру*/
    public function get_patient($enp)
    {
        $res = array();
        if ($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $res['patient'] = $this->patient_model->GetPatientByEnp($enp);
            $res['test'] = date('Y-m-d',strtotime($res['patient']['BIRTHDAY']));

        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*вставляет статус пациетна о дисп*/
    public function setstatus(){
        $res = array();
        if ($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $arg = array();
            $arg['enp'] = $this->input->post('patient_enp');
            $arg['status'] = $this->input->post('status');
            $arg['disp_year'] = $this->input->post('disp_year');
            $arg['disp_quarter'] = $this->input->post('disp_quarter');
            $arg['disp_type'] = 1;
            $arg['disp_lpu'] = $res['user']['lpucode'];
            $arg['age'] = 1;
            $arg['lgg_code'] = 1;
            $arg['drcode'] = 1;
            $arg['refusal_reason'] = 1;
            $arg['disp_start'] = '';
            $arg['stage_1_result'] = '';
            $arg['stage_2_result'] = '';
            $arg['guid'] = '';
            $arg['speccode'] = '';

            $this->patient_model->InsertPatientStatus($arg);


        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }


    public function GetCountPatientsInPlan() {
        $res = array();
        if ($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();

            $res['plan'] = $this->patient_model->GetCountPatientsInPlan($res['user'],$this->input->post('disp_year'));


        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*чекает всех в фильтре*/
    public function CheckAllFromFilter(){
        $res = array();
        if ($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            $patient = $this->input->post('patient');


            $d = $this->GetFilterParams(array(), $patient);

            $arg = $d['arg'];
            $arg['lpucode'] = $res['user']['lpucode'];
            $arg['user'] = $res['user'];

            $arg['status'] = $this->input->post('status');
            $arg['disp_year'] = $this->input->post('disp_year');
            $this->patient_model->CheckAllFromFilter($arg);
        } else {
            $res['auth'] = 0;
        }
        echo json_encode($res);
    }

    /*загрузка срезов*/
    public function LoadSections(){
        $res = array();
        if ($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();
            if(((isset($_FILES['f']['name'])))and($_FILES['f']['name']!=''))
            {
                $rnd=$this->elex->PassGen();
                $uploadfile = $_SERVER['DOCUMENT_ROOT'].$this->root."img/sections/". $rnd.'_'.$this->elex->rus2translit(basename($_FILES['f']['name']));

                if (move_uploaded_file($_FILES['f']['tmp_name'],$uploadfile))
                {
                    /*вставляем в таблицу*/
                    $arg = array();
                    $arg['lpu'] =  $res['user']['lpucode'];
                    $arg['filename'] =  $uploadfile;
                    $this->patient_model->InsertUpoadStatus($arg);

                }
                else
                {
                    //error
                }
            }
        } else {
                 //header('Location: '.$this->root);
                // exit;
        }
       // header('Location: '.$this->root."/");
      //  exit;



    }


    public function PassGenAll(){
        $users = $this->auth_model->GetAllUsers();
        foreach($users as $key=>$u){
            //$this->auth_model->UpdateUserPassword($u['username'],$this->elex->PassGen());

        }
    }

}
