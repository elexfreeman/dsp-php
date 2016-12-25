<?php
/**
 * Created by PhpStorm.
 * User: cod_llo
 * Date: 11.03.16
 * Time: 17:11
 */
/*Модель для работой с Пациентом*/


class Patient_model extends CI_Model
{

    public $patient_table = '[OMS].[dbo].[OMSC_INSURED]';
    public $UsersTable = '[DISP_WEB].[dbo].[users]';
    public $DocTable = '[AKTPAK].[dbo].[AKPC_DOCTORS]';

    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        $this->load->helper('url');
        $this->db_mssql = $this->load->database('default',true);
        $this->load->library('elex');
    }

    public function GetPatients() {

        $sql="select top 100

      [SURNAME]
      ,[NAME]
      ,[SECNAME]
      ,[BIRTHDAY]
      ,[LPUBASE]

 from ".$this->patient_table;

        $query = $this->db_mssql->conn_id->query($sql);
        /*http://proft.me/2008/11/28/primery-ispolzovaniya-pdo/*/

        return $this->elex->result_array($query);

    }

}
