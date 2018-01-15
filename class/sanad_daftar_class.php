<?php
	class sanad_daftar_class
	{
		public $id=-1;
		public $daftar_id=-1;
		public $user_id=-1;
		public $regdat='0000-00-00 00:00:00';
		public $sanad_record = array();
		public $canAccess = TRUE;
		public function __construct($daftar_id,$regdat='')
		{
			$shart = '';
			$conf = new conf;
			if($regdat!='')
			{
				$tmp = explode(" ",$regdat);
				$shart = "and `regdat`>='".$tmp[0]." 00:00:00' and `regdat`<= '".$tmp[0]." 23:59:59' ";
			}
			if($daftar_id>0)
				mysql_class::ex_sql("select * from `sanad_daftar` where `daftar_id` = $daftar_id $shart",$q);
			else if($daftar_id=-2)
				mysql_class::ex_sql("select * from `sanad_daftar` where 1=1 $shart ",$q);
			while($r = mysql_fetch_array($q))
				$this->sanad_record[]=$r['sanad_record_id'];

			$q = null;
			$sanad_record1 = array();
			$regdat = date("Y-m-d 00:00:00",strtotime($regdat.' - 1 day'));
			$tmp = explode(" ",$regdat);
			$shart = "and `regdat`>='".$tmp[0]." 00:00:00' and `regdat`<= '".$tmp[0]." 23:59:59' ";
			if($daftar_id>0)
                                mysql_class::ex_sql("select * from `sanad_daftar` where `daftar_id` = $daftar_id $shart",$q);
                        else if($daftar_id=-2)
                                mysql_class::ex_sql("select * from `sanad_daftar` where 1=1 $shart ",$q);
                        while($r = mysql_fetch_array($q))
                                $sanad_record1[]=$r['sanad_record_id'];
			while(count($sanad_record1)==0 && $regdat >= $conf->daftar_send_date )
			{
				$q = null;
	                        $regdat = date("Y-m-d 00:00:00",strtotime($regdat.' - 1 day'));
        	                $tmp = explode(" ",$regdat);
                	        $shart = "and `regdat`>='".$tmp[0]." 00:00:00' and `regdat`<= '".$tmp[0]." 23:59:59' ";
                        	if($daftar_id>0)
                                	mysql_class::ex_sql("select * from `sanad_daftar` where `daftar_id` = $daftar_id $shart",$q);
	                        else if($daftar_id=-2)
        	                        mysql_class::ex_sql("select * from `sanad_daftar` where 1=1 $shart ",$q);
                	        while($r = mysql_fetch_array($q))
                        	        $sanad_record1[]=$r['sanad_record_id'];
			}
			//$this->canAccess = $this->canAccessF($daftar_id,$regdat);
			
		}
		public function canAccessF($daftar_id,$regdat)
		{
			$out = TRUE;
                        $shart = '';
			$conf = new conf;
                        if($regdat!='')
                        {
                                $tmp = explode(" ",$regdat);
                                $shart = "and `regdat`>='".$tmp[0]." 00:00:00' and `regdat`<= '".$tmp[0]." 23:59:59' ";
                        }
                        if($daftar_id>0)
                                mysql_class::ex_sql("select * from `sanad_daftar` where `daftar_id` = $daftar_id $shart",$q);
                        else if($daftar_id=-2)
                                mysql_class::ex_sql("select * from `sanad_daftar` where 1=1 $shart ",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $qq = null;
                                mysql_class::ex_sql("select `id` from `upload` where `sanad_record_id` = ".$r['sanad_record_id'],$qq);
				$tmp ='';
                                if(mysql_num_rows($qq)==0 && $regdat > $conf->daftar_send_date)
				{
                                        $out = FALSE;
				}
                        }
                        return $out;
		}
	}
?>
