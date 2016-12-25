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

    public $patient_table = 'ipre_patients';
    public $patient_xml_table = 'ipre_xml';

    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        $this->load->helper('url');
        $this->dbMySQL = $this->load->database('mysql', TRUE);
        $this->load->library(array('elex'));
    }


    /*Выдает ID пациента по ФИО и дате рожднния*/
    function GetPatientID($arg)
    {
        /*
         $arg = array(
         'MIP'=>'123456678',
         'LastName'=>'Помидоров',
         'FirstName'=>'Антон',
         'SecondName'=>'Витальевич',
         'RegAddress'=>'АДРЕс',
         'SNILS'=>'12435345345345',
         'SNILS'=>'12435345345345',
         'BirthDate'=>'2012-25-23',
         'XML'=>'Строка XML'
        )
         **/

        $res=0;

        if ((isset($arg['LastName'])) and (isset($arg['FirstName'])) and (isset($arg['SecondName']))
            and (isset($arg['BirthDate'])))
        {
            /* 1 - ищем такого пациента в базе*/
            $sql = 'select * from ' . $this->patient_table . "
        where
        (FirstName='" . $arg['FirstName'] . "')
        and(LastName='" . $arg['LastName'] . "')
        and(SecondName='" . $arg['SecondName'] . "')
        and(BirthDate='" . $arg['BirthDate'] . "')
        ";
            $query = $this->dbMySQL->query($sql);
            $row = $query->result_array();
            if(count($row)>0)
            {
                $res=$row[0]['id'];
                unset($row);

            }

        else $res = 0;

        } else
            $res = 0;
        return $res;

    }

    /*Вставляет/обновляет данные об апциенте*/
    public function insert($arg)
    {
        /*
         $arg = array(
         'MIP'=>'123456678',
         'LastName'=>'Помидоров',
         'FirstName'=>'Антон',
         'SecondName'=>'Витальевич',
         'RegAddress'=>'АДРЕс',
         'SNILS'=>'12435345345345',
         'SNILS'=>'12435345345345',
         'BirthDate'=>'2012-25-23',
         'xml'=>'Строка XML'
        )
         **/

        $patient_id = (int)$this->GetPatientID($arg);
        echo 'patient_id=' . $patient_id . "\n \r";
        if ($patient_id > 0) {

        } else {

            if($arg['BirthDate']=='') $arg['BirthDate']="NULL";
            else $arg['BirthDate']="'".$arg['BirthDate']."'";

            $sql = "
            insert into " . $this->patient_table . " (`id`
            ,`MIP`
            ,`LastName`
            ,`FirstName`
            ,`SecondName`
            ,`RegAddress`
            ,`SNILS`
            ,`BirthDate`
            ,`LPUCODE`

            ,`dateload`
            )
            values (NULL
            ,'" . $arg['MIP'] . "'
            ,'" . $arg['LastName'] . "'
            ,'" . $arg['FirstName'] . "'
            ,'" . $arg['SecondName'] . "'
            ,'" . $arg['RegAddress'] . "'
            ,'" . $arg['SNILS'] . "'
            ," . $arg['BirthDate'] . "
            ,'" . $arg['LPUCODE'] . "'

            , NOW()
            )
            ";

            $this->dbMySQL->query($sql);
            $patient_id = $this->dbMySQL->insert_id();
            echo "patient  INCERT ";
        }

        /*Выбираем по номеру*/
      /*  $sql="select * from " . $this->patient_xml_table." where number='".$arg['NUMBER']."'";
        $query = $this->dbMySQL->query($sql);
        $row = $query->result_array();*/

        //if( count($row)==0)
        {
            if((isset($arg['DevelopDate']))and($arg['DevelopDate'] !=''))
            {
                $sql = "insert into " . $this->patient_xml_table . " (
            `id`
            ,`patient_id`
            ,`number`
            ,`insert_date`
            ,`DevelopDate`
            ,`xml`)
            values(
            NULL
            , '" . $patient_id . "'
            , '" . $arg['NUMBER'] . "'
            , NOW()
            , '" . $arg['DevelopDate'] . "'
            , '" . $arg['xml'] . "'
            )
            ";

                echo "XML  INCERT DevelopDate ";
            }
            else
            {
                $sql = "insert into " . $this->patient_xml_table . " (
            `id`
            ,`patient_id`
            ,`number`
            ,`insert_date`
            ,`DevelopDate`
            ,`xml`)
            values(
            NULL
            , '" . $patient_id . "'
            , '" . $arg['NUMBER'] . "'
            , NOW()
            , NOW()
            , '" . $arg['xml'] . "'
            )
            ";
                echo "XML  INCERT ";
            }
            $this->dbMySQL->query($sql);

        }
    }

    /*Выдает пациентов из ЛПУ*/
    public function GetByLPU($LPUCODE)
    {
        $LPUCODE = $this->security->xss_clean($LPUCODE);
        $sql = "SELECT p.id, p.MIP, p.LastName,
 p.FirstName, p.SecondName, p.RegAddress, p.SNILS,
  p.BirthDate, p.LPUCODE,
  p.dateload,
  xm.DevelopDate,
  xm.ProtocolDate,
  p.f_date
FROM ipre_patients p
JOIN ipre_xml xm ON p.id=xm.patient_id
where LPUCODE='" . $LPUCODE . "' order by LastName limit 200";

        $sql="
        SELECT p.id, p.MIP, p.LastName,
 p.FirstName, p.SecondName, p.RegAddress, p.SNILS,
  p.BirthDate, p.LPUCODE,
  p.dateload,
  xm.DevelopDate,
  xm.ProtocolDate,
  p.f_date
FROM ipre_patients p
JOIN ipre_xml xm ON p.id=xm.patient_id
where (LPUCODE='" . $LPUCODE . "' )
or(LPUCODE in (
select r.user_id from ipre_users u

join users_roots r
on u.id=r.user_root_id
where u.login='" . $LPUCODE . "'
))


order by LastName
        ";

        //echo $sql;
        $query = $this->dbMySQL->query($sql);
        $qq = $query->result_array();
        foreach($qq as $key=>$patient)
        {
            $qq[$key]['xml'] = $this->GetXml($qq[$key]['id']);

        }
        return $qq;
    }

    public function GetAll()
    {

        $sql = "
SELECT p.id, p.MIP, p.LastName,
 p.FirstName, p.SecondName, p.RegAddress, p.SNILS,
  p.BirthDate, p.LPUCODE,
  p.dateload,xm.DevelopDate,
  p.dateload,xm.ProtocolDate,
  p.f_date
FROM ipre_patients p
JOIN ipre_xml xm ON p.id=xm.patient_id
limit 200";
        $query = $this->dbMySQL->query($sql);
        $qq = $query->result_array();
        foreach($qq as $key=>$patient)
        {
            $qq[$key]['xml'] = $this->GetXml($qq[$key]['id']);

        }
        return $qq;
    }

    function GetXml($patient_id)
    {
        $sql="select * from ipre_xml xml
where xml.patient_id=".$patient_id."
limit 1";

        $query = $this->dbMySQL->query($sql);
        $row = $query->result_array();
        if(count($row)>0) return $row[0]['xml'];
        else return '';

    }

    public function Info($patient_id)
    {
        $patient_id = $this->security->xss_clean($patient_id);
        $sql = "select * from ipre_patients patient

                where patient.id = ".$patient_id."

                limit 1";
        $query = $this->dbMySQL->query($sql);
        return $query->row_array();
    }


    public function Search($arg)
    {

        $search_string = $this->security->xss_clean($arg['search_string']);


        $sql = "SELECT
p.id, p.MIP,
p.LastName, p.FirstName,
p.SecondName, p.RegAddress,
p.SNILS, p.BirthDate,
p.LPUCODE, p.dateload
,xm.DevelopDate
,xm.ProtocolDate
,xm.insert_date
,p.f_date
FROM ipre_patients p

join ipre_xml xm
on p.id=xm.patient_id
where (1=1)  ";

        if($arg['search_string']!='')
        {
            $sql .= "
                 and ((p.LastName like '%".$search_string."%')or
                (p.FirstName like '%".$search_string."%')or
                (p.SecondName like '%".$search_string."%')or
                (p.RegAddress like '%".$search_string."%')or
                (p.SNILS like '%".$search_string."%'))
                ";
        }


        /*Дата протокола*/
        /*напутанно с оозначением*/
        if((isset($arg['dateload1']))and($arg['dateload1']!='')and(isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $arg['dateload1'] = strtotime( $arg['dateload1'] );
            $arg['dateload1'] = date( 'Y-m-d', $arg['dateload1'] );
            $arg['dateload2'] = strtotime( $arg['dateload2'] );
            $arg['dateload2'] = date( 'Y-m-d', $arg['dateload2'] );

            $sql .= " and (xm.ProtocolDate BETWEEN '".$arg['dateload1']." 00:00:00'AND '".$arg['dateload2']." 23:59:59')";
           // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload1']))and($arg['dateload1']!=''))
        {
            $sql .= " AND (xm.ProtocolDate>='" . $arg['dateload1'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $sql .= " AND (xm.ProtocolDate<='" . $arg['dateload2'] ." 23:59:59')";
        }


        /*дата загрузки в базу*/
        if((isset($arg['datel_1']))and($arg['datel_1']!='')and(isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $arg['datel_1'] = strtotime( $arg['datel_1'] );
            $arg['datel_1'] = date( 'Y-m-d', $arg['datel_1'] );

            $arg['datel_2'] = strtotime( $arg['datel_2'] );
            $arg['datel_2'] = date( 'Y-m-d', $arg['datel_2'] );

            $sql .= " and (xm.insert_date BETWEEN '".$arg['datel_1']." 00:00:00'AND '".$arg['datel_2']." 23:59:59')";
            // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_1']))and($arg['datel_1']!=''))
        {
            $sql .= " AND (xm.insert_date>='" . $arg['datel_1'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $sql .= " AND (xm.insert_date<='" . $arg['datel_2'] ." 23:59:59')";
        }


        if($arg['user']=='admin')
        {
            if(isset($arg['lpu']))
            {
                if(($arg['lpu']!='-'))  $sql .= " and (p.LPUCODE='')";
                elseif(($arg['lpu']!=''))  $sql .= " and (p.LPUCODE='" . $arg['lpu'] . "')";
            }

        }
        /*для lpu 3422*/
        elseif(($arg['user']=='3422')or($arg['user']=='5715'))
        {
            $sql .= " and ((p.LPUCODE='" . $arg['user'] . "')or(p.LPUCODE='20200'))";
        }
        else
        {
            $sql .= " and (p.LPUCODE='" . $arg['user'] . "')";
        }

        if((isset($arg['lpu']))and($arg['lpu']!='')) $sql .= " order by LastName limit 500";


        $query = $this->dbMySQL->query($sql);
        $qq = $query->result_array();
        foreach($qq as $key=>$patient)
        {
            $qq[$key]['xml'] = $this->GetXml($qq[$key]['id']);
            $qq[$key]['sql']=$sql;

        }
        return $qq;


    }

    function GetEventGroup($xml,$group_id)
    {

        $res = new stdClass();
        $res->Need = false;

        foreach($xml->MedSection->EventGroups->Group as $EventGroup)
        {

            if((int)$EventGroup->GroupType->Id==$group_id) $res =  $EventGroup;
        }
        return $res;
    }

    function GetDate($d)
    {
        if($d!='')
        {
            $BirthDate = strtotime($d );
            $b_m = date( 'm', $BirthDate );
            $b_d = date( 'd', $BirthDate );
            $b_Y = date( 'Y', $BirthDate );
            return $b_d." ". $this->ConvertMonth($b_m)." ".$b_Y;
        }
        else return '';



    }


    function ConvertMonth($month)
    {
        if($month=='01') return 'января';
        if($month=='02') return 'февраля';
        if($month=='03') return 'марта';
        if($month=='04') return 'апреля';
        if($month=='05') return 'майя';
        if($month=='06') return 'июня';
        if($month=='07') return 'июля';
        if($month=='08') return 'августа';
        if($month=='09') return 'сентября';
        if($month=='10') return 'октября';
        if($month=='11') return 'ноября';
        if($month=='12') return 'декабря';
    }

    function ConvertHelpItems($HelpItems)
    {

        foreach($HelpItems as $HelpItem)
        {


            $res["A".$HelpItem->HelpCategory->Id] = $HelpItem;
        }
        ksort($res);

        return $res;

    }


    function UpdateLpu($patient_id,$lpu)
    {
            $sql_u="UPDATE ".$this->patient_table." SET LPUCODE ='".$lpu."' where (id=".$patient_id.")";
            $this->dbMySQL->query($sql_u);
    }



    public function get_stat()
    {
        $sql="select a.insert_date, a.xml_count, l.files_count, p.p_count from (
select i_xml.insert_date, count(*) xml_count from ipre_xml i_xml

group by i_xml.insert_date

) a

left join ipre_logs l
on l.insert_date=a.insert_date

left join (
select i_xml.insert_date, count(*) p_count from ipre_xml i_xml
join ipre_patients patients

on i_xml.patient_id=patients.id

where patients.LPUCODE=20200

group by i_xml.insert_date

) p

on p.insert_date=a.insert_date

where a.insert_date> '2016-06-22'
order by insert_date
";
        $query=$this->dbMySQL->query($sql);
        return $query->result_array();

    }

    public function UpdateFDate($patient_id,$f_date)
    {
        $auth=$this->session->userdata('auth');

        $f_date = strtotime( $f_date );
        $f_date = date( 'Y-m-d',$f_date );
        $sql_u="UPDATE ".$this->patient_table." SET f_date ='".$f_date."' where (id=".$patient_id.")";

        $this->dbMySQL->query($sql_u);
    }


    public function SearchNew($arg,$limit=true)
    {


        $search_string = $this->security->xss_clean($arg['search']);


        $sql = "SELECT DISTINCT
p.id, p.MIP,
p.LastName, p.FirstName,
p.SecondName, p.RegAddress,
p.SNILS, p.BirthDate,
p.LPUCODE, p.dateload
,p.dateload insert_date
,p.DevelopDate
,p.ProtocolDate

,p.f_date
FROM ipre_patients p

where (1=1)  ";

        if((isset($arg['search']))and($arg['search']!=''))
        {

            $sql .= "
                 and ((p.LastName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.FirstName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.SecondName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.RegAddress like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.SNILS like ". $this->dbMySQL->escape("%".$arg['search']."%")."))
                ";
        }


        /*Дата протокола*/
        /*напутанно с оозначением*/
        if((isset($arg['dateload1']))and($arg['dateload1']!='')and(isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $arg['dateload1'] = strtotime( $arg['dateload1'] );
            $arg['dateload1'] = date( 'Y-m-d', $arg['dateload1'] );
            $arg['dateload2'] = strtotime( $arg['dateload2'] );
            $arg['dateload2'] = date( 'Y-m-d', $arg['dateload2'] );

            $sql .= " and (p.IssueDate BETWEEN '".$arg['dateload1']." 00:00:00' AND '".$arg['dateload2']." 23:59:59')";
            // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload1']))and($arg['dateload1']!=''))
        {
            $sql .= " AND (p.IssueDate>='" . $arg['dateload1'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $sql .= " AND (p.IssueDate<='" . $arg['dateload2'] ." 23:59:59')";
        }


        /*дата загрузки в базу*/
        if((isset($arg['datel_1']))and($arg['datel_1']!='')and(isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $arg['datel_1'] = strtotime( $arg['datel_1'] );
            $arg['datel_1'] = date( 'Y-m-d', $arg['datel_1'] );

            $arg['datel_2'] = strtotime( $arg['datel_2'] );
            $arg['datel_2'] = date( 'Y-m-d', $arg['datel_2'] );

            $sql .= " and (p.dateload BETWEEN '".$arg['datel_1']." 00:00:00' AND '".$arg['datel_2']." 23:59:59')";
            // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_1']))and($arg['datel_1']!=''))
        {
            $sql .= " AND (p.dateload>='" . $arg['datel_1'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $sql .= " AND (p.dateload<='" . $arg['datel_2'] ." 23:59:59')";
        }


        if($arg['user']=='admin')
        {
            if(isset($arg['lpu']))
            {
                if(($arg['lpu']!='-'))  $sql .= " and (p.LPUCODE='')";
                elseif(($arg['lpu']!=''))  $sql .= " and  (p.LPUCODE='" . $arg['lpu'] . "')
                ";
            }

        }
        /*для lpu 3422*/
        elseif($arg['user']=='3422')
        {
            $sql .= " and ((p.LPUCODE='" . $arg['user'] . "')or(p.LPUCODE='20200'))";
        }
        else
        {
            $sql .= " and (
            (p.LPUCODE='" . $arg['user'] . "')
             or (p.LPUCODE in
(

select uru.login  from users_roots ur
join ipre_users uru
on ur.user_id=uru.login

join ipre_users uru2
on uru2.id=ur.user_root_id

where uru2.login='" . $arg['user'] . "'
order by ur.user_root_id

)) )
             ";
        }
        $sql.=" group by p.LastName,p.FirstName,p.SecondName,p.BirthDate ";
        $sql_count="select count(*) cc from (".$sql.") a ";



           // $sql .= " order by LastName ".$_GET['order']. " limit ".$_GET['offset'].", ".$_GET['limit'];
        if($limit)
           $sql .= "
 order by LastName limit 200";
        else
            $sql .= "
 order by LastName limit 200";



        $query = $this->dbMySQL->query($sql);
        $qq = $query->result_array();
        $res = array();
        foreach($qq as $key=>$patient)
        {

            unset($p);
            $p=array();
            $p['id']=$patient['id'];
            $p['LastName']=$patient['LastName'].":".$patient['id'];
            $p['FirstName']=$patient['FirstName'].":".$patient['id'];
            $p['SecondName']=$patient['SecondName'].":".$patient['id'];
            $p['RegAddress']=$patient['RegAddress'];
            $p['SNILS']=$patient['SNILS'];

            $patient['BirthDate'] = strtotime($patient['BirthDate']);
            $patient['BirthDate'] = date('d.m.Y', $patient['BirthDate']);
            $p['BirthDate']=$patient['BirthDate'];

            $p['LPUCODE']=$patient['LPUCODE'].":".$patient['id'];

            $patient['dateload'] = strtotime($patient['dateload']);
            $patient['dateload'] = date('d.m.Y', $patient['dateload']);
            $p['dateload']=$patient['dateload'];

            $patient['DevelopDate'] = strtotime($patient['DevelopDate']);
            $patient['DevelopDate'] = date('d.m.Y', $patient['DevelopDate']);
            $p['DevelopDate']=$patient['DevelopDate'];

            $patient['ProtocolDate'] = strtotime($patient['ProtocolDate']);
            $patient['ProtocolDate'] = date('d.m.Y', $patient['ProtocolDate']);
            $p['ProtocolDate']=$patient['ProtocolDate'];

            $patient['insert_date'] = strtotime($patient['insert_date']);
            $patient['insert_date'] = date('d.m.Y', $patient['insert_date']);
            $p['insert_date']=$patient['insert_date'];

            if(strlen($patient['f_date'])<3)
            {
                $p['f_date']=$patient['f_date']."&".$patient['id'];

            }
            else
            {
                $patient['f_date'] = strtotime($patient['f_date']);
                $patient['f_date'] = date('d.m.Y', $patient['f_date']);
                $p['f_date']=$patient['f_date']."&".$patient['id'];

            }


            $res[]=$p;
            //$qq[$key]['xml'] = $this->GetXml($qq[$key]['id']);
           // $qq[$key]['sql']=$sql;

        }

        $query = $this->dbMySQL->query($sql_count);
        unset($qq);
        $qq = $query->result_array();

     /*   $a=array();
        $a['total']=$qq[0]['cc'];
        $a['rows']=$res;
        $a['sql']=$sql;*/
        return $res;


    }




    public function SearchExel($arg,$limit=true)
    {
        $search_string = $this->security->xss_clean($arg['search']);


        $sql = "SELECT
p.*
,xm.DevelopDate
,xm.ProtocolDate
,xm.insert_date

FROM ipre_patients p

join ipre_xml xm
on p.id=xm.patient_id
where (1=1)  ";

        if((isset($arg['search']))and($arg['search']!=''))
        {

            $sql .= "
                 and ((p.LastName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.FirstName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.SecondName like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.RegAddress like ". $this->dbMySQL->escape("%".$arg['search']."%").")or
                (p.SNILS like ". $this->dbMySQL->escape("%".$arg['search']."%")."))
                ";
        }


        /*Дата протокола*/
        /*напутанно с оозначением*/
        if((isset($arg['dateload1']))and($arg['dateload1']!='')and(isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $arg['dateload1'] = strtotime( $arg['dateload1'] );
            $arg['dateload1'] = date( 'Y-m-d', $arg['dateload1'] );
            $arg['dateload2'] = strtotime( $arg['dateload2'] );
            $arg['dateload2'] = date( 'Y-m-d', $arg['dateload2'] );

            $sql .= " and (xm.ProtocolDate BETWEEN '".$arg['dateload1']." 00:00:00'AND '".$arg['dateload2']." 23:59:59')";
            // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload1']))and($arg['dateload1']!=''))
        {
            $sql .= " AND (xm.ProtocolDate>='" . $arg['dateload1'] ." 00:00:00')";
        }
        elseif((isset($arg['dateload2']))and($arg['dateload2']!=''))
        {
            $sql .= " AND (xm.ProtocolDate<='" . $arg['dateload2'] ." 23:59:59')";
        }


        /*дата загрузки в базу*/
        if((isset($arg['datel_1']))and($arg['datel_1']!='')and(isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $arg['datel_1'] = strtotime( $arg['datel_1'] );
            $arg['datel_1'] = date( 'Y-m-d', $arg['datel_1'] );

            $arg['datel_2'] = strtotime( $arg['datel_2'] );
            $arg['datel_2'] = date( 'Y-m-d', $arg['datel_2'] );

            $sql .= " and (xm.insert_date BETWEEN '".$arg['datel_1']." 00:00:00'AND '".$arg['datel_2']." 23:59:59')";
            // $sql .= " AND (xm.DevelopDate='" . $arg['dateload'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_1']))and($arg['datel_1']!=''))
        {
            $sql .= " AND (xm.insert_date>='" . $arg['datel_1'] ." 00:00:00')";
        }
        elseif((isset($arg['datel_2']))and($arg['datel_2']!=''))
        {
            $sql .= " AND (xm.insert_date<='" . $arg['datel_2'] ." 23:59:59')";
        }


        if($arg['user']=='admin')
        {
            if(isset($arg['lpu']))
            {
                if(($arg['lpu']!='-'))  $sql .= " and (p.LPUCODE='')";
                elseif(($arg['lpu']!=''))  $sql .= " and (p.LPUCODE='" . $arg['lpu'] . "')";
            }

        }
        /*для lpu 3422*/
        elseif($arg['user']=='3422')
        {
            $sql .= " and ((p.LPUCODE='" . $arg['user'] . "')or(p.LPUCODE='20200'))";
        }
        else
        {
            $sql .= " and (
            (p.LPUCODE='" . $arg['user'] . "')
             or (p.LPUCODE in
(

select uru.login  from users_roots ur
join ipre_users uru
on ur.user_id=uru.login

join ipre_users uru2
on uru2.id=ur.user_root_id

where uru2.login='" . $arg['user'] . "'
order by ur.user_root_id

)) )
             ";
        }
        $sql.=" group by p.LastName,p.FirstName,p.SecondName,p.BirthDate ";
        $sql_count="select count(*) cc from (".$sql.") a";



        // $sql .= " order by LastName ".$_GET['order']. " limit ".$_GET['offset'].", ".$_GET['limit'];
        if($limit)
            $sql .= " order by LastName limit 400";
        else
            $sql .= " order by LastName";



        $query = $this->dbMySQL->query($sql);

        $qq = $query->result_array();

        $res = array();

     /*   foreach($qq as $key=>$patient)
        {

            unset($p);
            $p=array();
            $p['id']=$patient['id'];
            $p['LastName']=$patient['LastName'];
            $p['FirstName']=$patient['FirstName'];
            $p['SecondName']=$patient['SecondName'];
            $p['RegAddress']=$patient['RegAddress'];
            $p['SNILS']=$patient['SNILS'];

            $patient['BirthDate'] = strtotime($patient['BirthDate']);
            $patient['BirthDate'] = date('d.m.Y', $patient['BirthDate']);
            $p['BirthDate']=$patient['BirthDate'];

            $p['LPUCODE']=$patient['LPUCODE'];

            $patient['dateload'] = strtotime($patient['dateload']);
            $patient['dateload'] = date('d.m.Y', $patient['dateload']);
            $p['dateload']=$patient['dateload'];

            $patient['DevelopDate'] = strtotime($patient['DevelopDate']);
            $patient['DevelopDate'] = date('d.m.Y', $patient['DevelopDate']);
            $p['DevelopDate']=$patient['DevelopDate'];

            $patient['ProtocolDate'] = strtotime($patient['ProtocolDate']);
            $patient['ProtocolDate'] = date('d.m.Y', $patient['ProtocolDate']);
            $p['ProtocolDate']=$patient['ProtocolDate'];

            $patient['insert_date'] = strtotime($patient['insert_date']);
            $patient['insert_date'] = date('d.m.Y', $patient['insert_date']);
            $p['insert_date']=$patient['insert_date'];

            if(strlen($patient['f_date'])<3)
            {
                $p['f_date']=$patient['f_date'];

            }
            else
            {
                $patient['f_date'] = strtotime($patient['f_date']);
                $patient['f_date'] = date('d.m.Y', $patient['f_date']);
                $p['f_date']=$patient['f_date'];
            }
            $res[]=$p;


        }

        $query = $this->dbMySQL->query($sql_count);
        unset($qq);
        $qq = $query->result_array();*/

        /*   $a=array();
           $a['total']=$qq[0]['cc'];
           $a['rows']=$res;
           $a['sql']=$sql;*/
        return $qq;


    }

    public function addFiles($patient_id)
    {
        $patient_id=(int)$patient_id;
        $arg=array();
        $arg['description']=$_POST['description'];
        $arg['n_date']=date('Y-m-d H:m:s');
        $arg['patient_id']=$patient_id;


        $this->dbMySQL->reset_query();
        $this->dbMySQL->insert('ipre_patient_files', $arg);
        $id=$this->dbMySQL->insert_id();

        if(((isset($_FILES['doc']['name'])))and($_FILES['doc']['name']!=''))
        {
            $rnd=$this->elex->PassGen();
            $uploadfile = $_SERVER['DOCUMENT_ROOT']."/files/". $rnd.'_'.$this->elex->rus2translit(basename($_FILES['doc']['name']));
            $doc = $rnd.'_'.$this->elex->rus2translit(basename($_FILES['doc']['name']));
            if (move_uploaded_file($_FILES['doc']['tmp_name'],$uploadfile))
            {
                $this->dbMySQL->reset_query();
                $this->dbMySQL->where('id', $id);
                $this->dbMySQL->update('ipre_patient_files', array('file_name'=>$doc));
            }else
            {
                //error
            }
        }
    }

    public function GetPatientFiles($patient_id)
    {
        $patient_id=(int)$patient_id;
        $this->dbMySQL->reset_query();
        $query = $this->dbMySQL->get_where('ipre_patient_files',
            array('patient_id' => $patient_id));
        return $query->result_array();
    }


    public function Info2($patient_id)
    {
        $patient_id = $this->security->xss_clean($patient_id);
        $sql = "select * from ipre_patients patient

join ipre_xml i_xml
on i_xml.patient_id=patient.id

where patient.id = ".$patient_id."

order by i_xml.insert_date desc
limit 1";
        $query = $this->dbMySQL->query($sql);
        return $query->row_array();

    }

    public function GetAllp()
    {
        $sql = "select * from ipre_patients p
where p.xml_file <> ''

";
        $query = $this->dbMySQL->query($sql);
        return $query->result_array();
    }

    public function update($id,$data)
    {

        $this->dbMySQL->where('id', $id);
        $this->dbMySQL->update('ipre_patients', $data);
    }

    public function insert_prg_rhb($data)
    {
        /*
        ID    	SERIAL NOT NULL PRIMARY KEY	Ключ
        PRGID 	int not null	PRG.ID Ключ из таблицы PRG
        TYPEID	int not null	RHB_TYPE.ID Тип мероприятия
        EVNTID	int      	RHB_EVNT.ID Подтип мероприятия
        DICID 	int      	RHB_DIC.ID Мероприятие из справочника
        TSRID 	int      	RHB_TSR.ID Мероприятие из справочника
        NAME  	char(128)	Название мероприятия (если нет в справочнике)
        DT_EXC	date     	Дата выполнения мероприятия
        EXCID 	int      	RHB_EXC.ID Исполнитель мероприятия из справочника
        EXECUT	char(128)	Исполнитель мероприятия (если нет в справочнике)
        RESID 	int      	RHB_RES.ID Результат выполнения мероприятия из справочника
        PAR1	int	Параметр 1 (в резерве)
        PAR2	int	Параметр 2 (в резерве)
        PAR3	int	Параметр 3 (в резерве)
        RESULT	char(128)	Результат выполнения мероприятия (+ Реквизиты контракта)
        NOTE  	char(64) 	Примечание
        UDT	TIMESTAMP with time zone	Метка времени изменения записи
        ADT	TIMESTAMP with time zone	Метка времени скачивания записи
        */

        $this->dbMySQL->insert('prg_rhb', $data);
        return $this->dbMySQL->insert_id();
    }

    public function Get_prg_rhb($id)
    {
        $id=(int)$id;
        $this->dbMySQL->reset_query();
        $sql = "
        select
            pr.`*`,
            rt.NAME rt_name,
            re.NAME re_name
            from prg_rhb pr

            join rhb_type rt
            on rt.ID = pr.typeid

            join  rhb_evnt re
            on re.ID = pr.evntid

            where pr.prgid = ".$id;
        $query = $this->dbMySQL->query($sql);
        return $query->result_array();
    }

    public function Get_prg_rhb_for_edit($id)
    {
        $id=(int)$id;
        $this->dbMySQL->reset_query();
        $sql = "
        select
            pr.`*`
            from prg_rhb pr

            where id = ".$id;
        $query = $this->dbMySQL->query($sql);
        return $query->row_array();
    }
}
