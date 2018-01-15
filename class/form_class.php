<?php
class form_class
{
	public $user_id = -1;
	public $target_user_id = -1;
	public $dore_arzeshyabi_id=-1;
	public $live = FALSE;
	public $gone = FALSE;
	public function __construct($user_id,$target_user_id,$dore_arzeshyabi_id=-1)
	{
		$user_id = (int)$user_id;
		$target_user_id = (int)$target_user_id;
		$dore_arzeshyabi_id = (int)$dore_arzeshyabi_id;
		mysql_class::ex_sql("select id from dore_arzeshyabi  where en=1 and id=$dore_arzeshyabi_id",$q);
		if($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$this->dore_arzeshyabi_id = $dore_arzeshyabi_id;				
		}
		else
		{
			$q = null;
			mysql_class::ex_sql("select id from dore_arzeshyabi  where en=1 order by id desc limit 1",$q);
			if($r=mysql_fetch_array($q,MYSQL_ASSOC))
			{
				$this->dore_arzeshyabi_id = (int)$r["id"];
			}
		}
		$dore_arzeshyabi_id = $this->dore_arzeshyabi_id;
		$this->user_id = $user_id;
		$this->target_user_id = $target_user_id;
		$q = null;
		if($dore_arzeshyabi_id>0)
		{
		//echo "select id,en from arzeshyabi where target_user=$target_user_id and user_id = $user_id and dore_arzeshyabi_id=$dore_arzeshyabi_id and en<>0>br/>\n";
		mysql_class::ex_sql("select id,en from arzeshyabi where target_user=$target_user_id and user_id = $user_id and dore_arzeshyabi_id=$dore_arzeshyabi_id and en<>0",$q);
		if($rr = mysql_fetch_array($q,MYSQL_ASSOC))
		{
			//فرم موجود است
			$this->live = TRUE;
			if((int)$rr["en"]==2)
			{
				$this->gone = TRUE;
			}
		}
		else
		{
			$rade_shoghli_id = -1;
			$q = null;									
			mysql_class::ex_sql("select rade_shoghli_id from user where en=1 and  id=$target_user_id",$q);
			if($r=mysql_fetch_array($q,MYSQL_ASSOC))
			{
				$rade_shoghli_id = (int)$r["rade_shoghli_id"];			
			}
			$q = null;
			$q1 =null;
			if($rade_shoghli_id<=0)
			{
				echo "<script>alert(\""."معاونت حوزه کاری کاربر تعریف نشده است"."\");window.parent.location = \"index.php\";</script>";
			}
			else
			{
				mysql_class::ex_sql("select * from level where en = 1 order by id",$q1);
				while($r1 = mysql_fetch_array($q1,MYSQL_ASSOC))
				{
					$q2= null;
					$level_id =(int) $r1["id"];
					mysql_class::ex_sql("select * from parameter where en = 1 and level_id=$level_id order by id",$q2);
					while($r2= mysql_fetch_array($q2,MYSQL_ASSOC))
					{
						$q3 = null;
						$parameter_id =(int)$r2["id"];
						mysql_class::ex_sql("select * from masadigh where en = 1 and parameter_id=$parameter_id and rade_shoghli_id =$rade_shoghli_id  order by id",$q3);
						//echo "select * from masadigh where parameter_id=$parameter_id and rade_shoghli_id =$rade_shoghli_id  order by id<br/>\n";
						while($r3=mysql_fetch_array($q3,MYSQL_ASSOC))
						{						
							$masadigh_id=(int) $r3["id"];
							mysql_class::ex_sqlx("insert into arzeshyabi (masadigh_id,user_id,target_user,dore_arzeshyabi_id,en) values ('$masadigh_id','$user_id','$target_user_id','$dore_arzeshyabi_id','1' ) ");
						}					
					}
				}
			}
		}
		}
		else
		{
			echo "<script>alert(\"دوره ارزیابی فعال وجود ندارد\"); window.parent.location=\"index.php\";</script>";
		}
	}	
}
?>
