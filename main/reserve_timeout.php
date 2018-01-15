<?php
	//$project_path = "/var/www/html/h_hamze";
	$project_path = dirname(dirname(__FILE__));
	include_once("$project_path/class/conf.php");
	include_once("$project_path/class/mysql_class.php");
        function deleteReserve($reserve_id,$sanad,$mysql_const,$rooms=TRUE)
        {
	        $reserve_id = (int)$reserve_id;
		$mysql_class = new mysql_class;
		$mysql_class->ex_db = TRUE;
		$mysql_class->host = $mysql_const->host;
		$mysql_class->db = $mysql_const->db;
		$mysql_class->user = $mysql_const->user;
		$mysql_class->pass = $mysql_const->pass;
        	$mysql_class->ex_sqlx('delete from `hotel_reserve` where `reserve_id` = '.$reserve_id);
                if($rooms)
                	$mysql_class->ex_sqlx('delete from `room_det` where `reserve_id` = '.$reserve_id);
                $mysql_class->ex_sqlx('delete from `khadamat_det` where `reserve_id` = '.$reserve_id);
                if($sanad)
                {
			$w = $mysql_class->getInArray('sanad_record','sanad_reserve'," `reserve_id` = '$reserve_id'");
			if($w != null)
				$w = "where `id` in ($w)";
			else
				$w = "where 1 = 0";
                	$mysql_class->ex_sqlx("delete from `sanad` $w");
                        $mysql_class->ex_sqlx('delete from `sanad_reserve` where `reserve_id` = '.$reserve_id);
                }
        }
	$mysql_class = new mysql_class;
	$conf = new conf;
	$moshtari = array();
	$mysql_class->ex_sql("select `id` from `moshtari` order by `id`",$res);
	while($r = mysql_fetch_array($res))
		$moshtari[] = $r['id'];
	for($i=0;$i<count($moshtari);$i++)
	{
		$mysql_class->ex_db = TRUE;
		$mysql_class->host = $conf->host;
		$mysql_class->db = $conf->db.$conf->db_eliminator.$moshtari[$i];
		$mysql_class->user = $conf->user;
		$mysql_class->pass = $conf->pass;
		$is_lock = FALSE;
		$mysql_class->ex_sql("select `is_lock` from `cron_lock` limit 1",$q);
		if($r = mysql_fetch_array($q))
			$is_lock = ((int)$r['is_lock'] == 1)?TRUE:FALSE;
		$q = null;
		if(!$is_lock)
		{
			$reserve_timeout = 10;
			$mysql_class->ex_sqlx("update `cron_lock` set `is_lock` = 1");
			$mysql_class->ex_sql("select `value` from `conf` where `key`='reserve_timeout' ",$qq);
			if($r = mysql_fetch_array($qq))
				$reserve_timeout = (int)$r['value'];
			$tarikh = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s").' - '.$reserve_timeout.' minute'));
			$mysql_class->ex_sql("select `reserve_id` from `reserve_tmp`  where `tarikh` < '$tarikh'",$q);
			while($r = mysql_fetch_array($q))
				deleteReserve((int)$r['reserve_id'],TRUE,$mysql_class,TRUE);
		        $mysql_class->ex_sqlx("delete from `reserve_tmp`  where `tarikh` < '$tarikh'");
			$mysql_class->ex_sqlx("update `cron_lock` set `is_lock` = 0");
		}
		else
			echo 'isLosk FALSE';
	}
?>
