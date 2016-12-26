<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dsp_patients extends CI_Controller {


    public $data;

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

    public function GetPatients() {
        $res = array();
        if($this->auth_model->IsLogin()) {
            $res['auth'] = 1;
            $res['user'] = $this->auth_model->UserInfo();

            $data = $this->input->post('data');
            $patient = $this->input->post('patient');

            if($data!='') {
                $arg=array();

                $arg['lpucode'] = $res['user']['lpucode'];
                if((isset($data['sort']))and($data['sort']!=''))
                    $arg['sort'] = $data['sort'];
                else $arg['sort'] = 'surname';
                $arg['order'] = $data['order'];

                if(!isset($data['limit']))  $data['limit'] = 100;
                if(!isset($data['offset']))  $data['offset'] = 0;

                /*только 400 записей максимум*/
                if((int)$data['limit']>400) $data['limit'] = 400;
                if((int)$data['offset']<0) $data['offset'] = 0;

                $res['patients']['rows'] = $this->patient_model->GetPatients($arg,$data['limit'],$data['offset']);
                if(count($res['patients']['rows'])>0)
                    $res['patients']['total'] = $res['patients']['rows'][0]['total'];
            }

        } else {
            $res['auth'] = 0;
        }

        echo json_encode($res);
    }




}
