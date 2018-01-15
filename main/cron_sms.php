<?php
	$project_path = dirname(dirname(__FILE__));
	include_once("$project_path/class/conf.php");
	include_once("$project_path/class/mysql_class.php");
	include_once("$project_path/class/sms_class.php");
	$now_tarikh1 = Date ("Y-m-d");
	if($conf->sms)
	{
		mysql_class::ex_sql("select `reserve_id`,DATE(`aztarikh`) as `aztar` ,DATE(`tatarikh`) as `tatar` from `room_det` where DATE(`tatarikh`)= '$now_tarikh1' or DATE(`aztarikh`)='$now_tarikh1' group by `reserve_id` ",$q);
		while ($r = mysql_fetch_array($q))
		{
			$reserve_id = $r['reserve_id'];
			mysql_class::ex_sql("select `tozih` from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu);
			if ($row = mysql_fetch_array($qu))
			{
				$shomare = $row["tozih"];
				if($r['aztar']==$now_tarikh1)
					sms_class::vorud_text_sms($reserve_id,$shomare);
				if($r['tatar']==$now_tarikh1)
					sms_class::khoruj_text_sms($reserve_id,$shomare);
			}
				
		}
		sms_class::recive_Ajanssms();
		sms_class::recive_Peoplesms();
	}
?>
