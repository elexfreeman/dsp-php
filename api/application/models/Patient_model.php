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
declare @uch int = 1;

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
  and i.LPUBASE_U = @uch
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

  ,dp.[status],
  pld.NAME
from oms..OMSC_INSURED_SREZ i


left join [DISP_WEB]..disp_plan dp
on dp.enp=i.ENP

left join [POLYCLINIC_2010].[dbo].[POLM_LPU_DISTRICTS] pld
on (pld.NUM = i.LPUBASE_U)and(pld.LPUCODE = i.LPUBASE)and(pld.D_FIN is null)



where i.d_fin is null
  and i.lpuchief = @lpu
  and (year(getdate()) - year(i.BIRTHDAY)) % 3 = 0
  and year(getdate()) - year(i.BIRTHDAY) >= 21
  and month(i.birthday) between @month_beg and @month_end
  and (year(getdate()) - year(i.BIRTHDAY)) between  @age_beg and @age_end

 and i.LPUBASE_U = @uch

	-- having drcode = @drcode
) x
where rn between ".$offset." and ".$limit;

        $query = $this->db_mssql->conn_id->query($sql);
        /*http://proft.me/2008/11/28/primery-ispolzovaniya-pdo/*/


        return $this->elex->result_array($query);
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
    select * from [AKTPAK].[dbo].[AKPC_DOCTORS] d
    where (d.LPUWORK = ".$lpucode.")AND(d.D_FIN is null)and(d.DBSOURCE = 'D') order by d.SURNAME;";
        $query = $this->db_mssql->conn_id->query($sql);
        return $this->elex->result_array($query);
    }

}
