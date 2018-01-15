<?php
		public function createMoshtari($name,$mob)
		{
			$conf = new conf;
			if(conf->getMoshtari()=='')
			{
				$ln = mysql_class::ex_sqlx("insert into `moshtari` (`name`,`mob`) values ('$name','$mob')",FALSE);
				$moshatri_id = mysql_insert_id($ln);
				mysql_close($ln);
				mysql_class::ex_sqlx("create database `".$conf->db."_$moshtari_id`");
				shell_exec("mysql -u gcom ".$conf->db."_$moshtari_id -p'Tammar666' < ".$conf->db."_empty.sql");
			}
		}
?>
