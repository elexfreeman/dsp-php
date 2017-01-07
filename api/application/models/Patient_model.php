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

    public function GetPatients($arg,$limit,$offset) {

        $limit = (int)$limit;
        $offset = (int)$offset;
        $lpu = (int)$arg['lpucode'];

        $uch_d='';
        $uch_w='';
        if((isset($arg['uch']))and($arg['uch']!='')){
            $uch_d = "declare @uch int = ".$arg['uch'].";";
            $uch_w = " and i.LPUBASE_U = @uch";
        };

        /*сортировка*/
        $order_by=' order by surname ';
        if(isset($arg['sort']))
        $order_by = ' order by '.$arg['sort']." ".$arg['order'];

        $sql="
declare @month_beg int = ".$arg['month_beg'].";
declare @month_end int = ".$arg['month_end'].";
declare @age_beg int = ".$arg['age_beg'].";
declare @age_end int = ".$arg['age_end'].";
declare @drcode varchar(8);
$uch_d

declare @year int = 3;
declare @lpu int = ".$lpu.";
select *
from
(select year(getdate()) - year(i.BIRTHDAY) as vozr,
 ROW_NUMBER() over(order by i.surname) as rn,
 null as user_id,

 2017 as  disp_year,
 1 as disp_type,
 i.lpuchief as disp_lpu,
 year(getdate()) - year(i.BIRTHDAY) as  age,

 i.lpubase,
 i.lpubase_u,
 i.type_u as typeui,

 i. enp,

(select count(*) from oms..OMSC_INSURED_SREZ
where d_fin is null
  and lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >=21
  ".$uch_w."
  ) as kol,


  (select top 1 drcode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as drcode,
  (select top 1 speccode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as speccode,

  i.surname as surname1, i.name as name1, i.secname as secname1, i.birthday as birthday1

  ,pld.NAME

from oms..OMSC_INSURED_SREZ i

left join [POLYCLINIC_2010].[dbo].[POLM_LPU_DISTRICTS] pld
on (pld.NUM = i.LPUBASE_U)and(pld.LPUCODE = i.LPUBASE)and(pld.D_FIN is null)



where i.d_fin is null
  and i.lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >= 21
  and month(i.birthday) between @month_beg and @month_end
  and (year(getdate()) - year(i.BIRTHDAY)) between  @age_beg and @age_end

 ".$uch_w."

	-- having drcode = @drcode
) x
where rn between ".$offset." and ".($offset+$limit);

        $query = $this->db_mssql->conn_id->query($sql);
        /*http://proft.me/2008/11/28/primery-ispolzovaniya-pdo/*/


        return $this->elex->result_array($query);
    }


    public function GetPatientsTotal($arg) {

        $lpu = (int)$arg['lpucode'];

        $uch_d='';
        $uch_w='';
        if((isset($arg['uch']))and($arg['uch']!='')){
            $uch_d = "declare @uch int = ".$arg['uch'].";";
            $uch_w = " and i.LPUBASE_U = @uch";
        };

        /*сортировка*/
        $order_by=' order by surname ';
        if(isset($arg['sort']))
            $order_by = ' order by '.$arg['sort']." ".$arg['order'];

        $sql="
declare @month_beg int = ".$arg['month_beg'].";
declare @month_end int = ".$arg['month_end'].";
declare @age_beg int = ".$arg['age_beg'].";
declare @age_end int = ".$arg['age_end'].";
declare @drcode varchar(8);
$uch_d

declare @year int = 3;
declare @lpu int = ".$lpu.";
select count(*) cc
from
(select year(getdate()) - year(i.BIRTHDAY) as vozr,
 ROW_NUMBER() over(order by i.surname) as rn,
 null as user_id,

 2017 as  disp_year,
 1 as disp_type,
 i.lpuchief as disp_lpu,
 year(getdate()) - year(i.BIRTHDAY) as  age,

 i.lpubase,
 i.lpubase_u,
 i.type_u as typeui,

 i. enp,


(select count(*) from oms..OMSC_INSURED_SREZ
where d_fin is null
  and lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >=21
  ".$uch_w."
  ) as kol,


  (select top 1 drcode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as drcode,
  (select top 1 speccode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as speccode,

  i.surname as surname1, i.name as name1, i.secname as secname1, i.birthday as birthday1


  ,pld.NAME
from oms..OMSC_INSURED_SREZ i



left join [POLYCLINIC_2010].[dbo].[POLM_LPU_DISTRICTS] pld
on (pld.NUM = i.LPUBASE_U)and(pld.LPUCODE = i.LPUBASE)and(pld.D_FIN is null)



where i.d_fin is null
  and i.lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >= 21
  and month(i.birthday) between @month_beg and @month_end
  and (year(getdate()) - year(i.BIRTHDAY)) between  @age_beg and @age_end

 ".$uch_w."

	-- having drcode = @drcode
) x

";


        $query = $this->db_mssql->conn_id->query($sql);
        /*http://proft.me/2008/11/28/primery-ispolzovaniya-pdo/*/

        $res = $this->elex->result_array($query);

        return (int)$res[0]['cc'];
    }

    /*выдает докторов в лпу*/
    function GetLPUDoctors($lpucode) {
        $lpucode = (int)$lpucode;
        $sql = "
    select * from [AKTPAK].[dbo].[AKPC_DOCTORS] d
    where (d.LPUWORK = ".$lpucode.")AND(d.D_FIN is null)and(d.DBSOURCE = 'D') order by d.SURNAME;";
        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->result_array($query);
    }

    /*выдает участки в лпу*/
    function GetLPUuch($lpucode) {
        $lpucode = (int)$lpucode;
        $sql = "
    select * from [POLYCLINIC_2010].[dbo].[POLM_LPU_DISTRICTS] pld
where (pld.D_FIN is null)and(pld.LPUCHIEF = ".$lpucode.")";
        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->result_array($query);
    }

    /*выдает участки в лпу*/
    function GetDspPlanForYear($lpucode,$year) {
        $lpucode = (int)$lpucode;
        $year = (int)$year;
        $sql = "
    SELECT *
  FROM [DISP_WEB].[dbo].[plan_mzso] plan_m where (plan_m.lpucode = ".$lpucode.") and (plan_m.year = ".$year.")";
        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->row_array($query);
    }


    public function GetPatientByEnp($enp) {

        $sql="SELECT *
          FROM [OMS].[dbo].[OMSC_INSURED] p

          where (p.ENP = '".$enp."') and (p.D_FIN is null)";

        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->row_array($query);
    }

    public function InsertPatientStatus($arg) {

        $now = date('Y-m-d H:i:s');

        $sql="
INSERT INTO [DISP_WEB].[dbo].[disp_plan]
           ([insert_date]
           ,[enp]
           ,[status]
           ,[guid]
           ,[disp_year]
           ,[disp_quarter]
           ,[disp_type]
           ,[disp_lpu]
           ,[age]
           ,[lgg_code]
           ,[drcode]
           ,[speccode]
           ,[refusal_reason]
           ,[disp_start]
           ,[stage_1_result]
           ,[stage_2_result]
           ,[deleted])
     VALUES
           (
           '".$now."'
           ,'".$arg['enp']."'
           ,'".$arg['status']."'
          ,'".$arg['guid']."'
          ,'".$arg['disp_year']."'
          ,'".$arg['disp_quarter']."'
          ,'".$arg['disp_type']."'
          ,'".$arg['disp_lpu']."'
          ,'".$arg['age']."'
          ,'".$arg['lgg_code']."'
          ,'".$arg['drcode']."'
          ,'".$arg['speccode']."'
          ,'".$arg['refusal_reason']."'
          ,'".$arg['disp_start']."'
          ,'".$arg['stage_1_result']."'
          ,'".$arg['stage_2_result']."'
          ,'0');
";


        $query = $this->db_mssql->conn_id->query($sql);
    }


    public function GetPatientStatus($enp) {
        $sql="SELECT TOP 1 [id]
      ,[insert_date]
      ,[enp]
      ,[status]
      ,[guid]
      ,[disp_year]
      ,[disp_quarter]
      ,[disp_type]
      ,[disp_lpu]
      ,[age]
      ,[lgg_code]
      ,[drcode]
      ,[speccode]
      ,[refusal_reason]
      ,[disp_start]
      ,[stage_1_result]
      ,[stage_2_result]
      ,[deleted]
  FROM [DISP_WEB].[dbo].[disp_plan] p

  where p.enp = '".$enp."' order by id desc";
        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->row_array($query);
    }

    public function CheckAllFromFilter($arg) {



        $lpu = (int)$arg['lpucode'];

        $uch_d='';
        $uch_w='';
        if((isset($arg['uch']))and($arg['uch']!='')){
            $uch_d = "declare @uch int = ".$arg['uch'].";";
            $uch_w = " and i.LPUBASE_U = @uch";
        };



        $sql="
declare @month_beg int = ".$arg['month_beg'].";
declare @month_end int = ".$arg['month_end'].";
declare @age_beg int = ".$arg['age_beg'].";
declare @age_end int = ".$arg['age_end'].";
declare @drcode varchar(8);
$uch_d

declare @year int = 3;
declare @lpu int = ".$lpu.";
select *
from
(select year(getdate()) - year(i.BIRTHDAY) as vozr,
 ROW_NUMBER() over(order by i.surname) as rn,
 null as user_id,

 2017 as  disp_year,
 1 as disp_type,
 i.lpuchief as disp_lpu,
 year(getdate()) - year(i.BIRTHDAY) as  age,

 i.lpubase,
 i.lpubase_u,
 i.type_u as typeui,

 i. enp,

(select count(*) from oms..OMSC_INSURED_SREZ
where d_fin is null
  and lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >=21
  ".$uch_w."
  ) as kol,


  (select top 1 drcode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as drcode,
  (select top 1 speccode
      from aktpak..akpc_tmodoc
	  where d_fin is null

		and DBSOURCE = 'D'

		and isnull(LPUTER_U,0) = isnull(i.lpubase_u,0)
		and isnull(LPUTER,0) = isnull(i.lpubase,0)
		and isnull(type_u,0) = isnull(i.type_u,0)
      order by drcode
      )  as speccode,

  i.surname as surname1, i.name as name1, i.secname as secname1, i.birthday as birthday1

  ,pld.NAME

from oms..OMSC_INSURED_SREZ i

left join [POLYCLINIC_2010].[dbo].[POLM_LPU_DISTRICTS] pld
on (pld.NUM = i.LPUBASE_U)and(pld.LPUCODE = i.LPUBASE)and(pld.D_FIN is null)



where i.d_fin is null
  and i.lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >= 21
  and month(i.birthday) between @month_beg and @month_end
  and (year(getdate()) - year(i.BIRTHDAY)) between  @age_beg and @age_end

 ".$uch_w."

	-- having drcode = @drcode
) x"
;

        $query = $this->db_mssql->conn_id->query($sql);
        /*http://proft.me/2008/11/28/primery-ispolzovaniya-pdo/*/


        $rows = $this->elex->result_array($query);
        /*перебераем всех и вставляем записи*/
        echo count($rows);
        foreach($rows as $row){

            $arg1 = array();
            $arg1['enp'] = $row['enp'];
            $arg1['status'] = $arg['status'];
            $arg1['disp_year'] = $arg['disp_year'];
            $arg1['disp_quarter'] = $this->GeQuarterByDate($row['birthday1']);
            $arg1['disp_type'] = 1;
            $arg1['disp_lpu'] = $arg['user']['lpucode'];
            $arg1['age'] = 1;
            $arg1['lgg_code'] = 1;
            $arg1['drcode'] = 1;
            $arg1['refusal_reason'] = 1;
            $arg1['disp_start'] = '';
            $arg1['stage_1_result'] = '';
            $arg1['stage_2_result'] = '';
            $arg1['guid'] = '';
            $arg1['speccode'] = '';

            $this->InsertPatientStatus($arg1);

        }
    }

    public function GeQuarterByDate($date){
        $month = (int)date('m',strtotime($date));
        if(($month>0)and($month<4)) return 1;
        if(($month>3)and($month<7)) return 2;
        if(($month>6)and($month<10)) return 3;
        if($month>9) return 4;
    }


    public function GetCountPatientsInPlan($user,$year){

        $sql="
         select sum([status]) as kol,
        sum(case when [disp_quarter] = 1 then 1 else 0 end) as kol1,
	    sum(case when [disp_quarter] = 2 then 1 else 0 end) as kol2,
	    sum(case when [disp_quarter] = 3 then 1 else 0 end) as kol3,
	    sum(case when [disp_quarter] = 4 then 1 else 0 end) as kol4
from
 (SELECT status, disp_quarter, enp, id,
 	   row_number() over (partition  by enp order by id desc) as rn
  FROM [DISP_WEB].[dbo].[disp_plan] p
  where (disp_year = ".$year.")
  and (p.disp_lpu = ".$user['lpucode']." )
  ) x
  where rn = 1 and status = 1
        ";


        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->row_array($query);

    }

}

