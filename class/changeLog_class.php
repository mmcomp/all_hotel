<?php
	class changeLog_class
	{
		public $id=-1;
		public $page_name="";
		public $feild_name="";
		public $pvalue="";
		public $value="";
		public $reserve_id = 0;
		public $user_id = -1;
		public $tarikh=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `changeLog` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->page_name=$r['page_name'];
				$this->feild_name=$r['feild_name'];
				$this->pvalue=$r['pvalue'];
				$this->value=$r['value'];
				$this->tarikh=$r['tarikh'];
			}
		}
		public function add($reserve_id,$user_id,$json_string='')
		{
			$out = FALSE;
			$json_string  = (($json_string=='' && isset($_REQUEST['json_string']))?trim($_REQUEST['json_string']):$json_string);
			if($json_string != '')
			{
				$page_name = security_class::thisPage();
				$json_string = str_replace('\"','"',$json_string);
		                $jso = fromJSON($json_string);
	        	        $object_vars = get_object_vars($jso);
                		foreach($object_vars as $id => $obj)
	        	        {
					mysql_class::ex_sqlx("insert into `changeLog` (`page_name`, `feild_name`, `pvalue`, `value`,`reserve_id`,`user_id`) values ('$page_name','$id','".$obj->pvalue."','".$obj->value."','$reserve_id','$user_id')");
					log_class::add('showreserve',$user_id,'اصلاح رزرو '.$reserve_id.' تغییر '.$obj->pvalue.' به '.$obj->value);
					$out = TRUE;
	                	}
			}
			return($out);
		}
	}
?>
