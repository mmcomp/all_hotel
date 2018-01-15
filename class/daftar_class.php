<?php
	class daftar_class
	{
		public $id=-1;
		public $name="";
		public $toz="";
		public $kol_id = -1;
		public $css_class = '';
		public $sandogh_moeen_id = -1;
		public $takhfif = 0;
		public $protected = 0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `daftar` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->toz=$r['toz'];
				$this->kol_id=(int)$r['kol_id'];
				$this->css_class=$r['css_class'];
				$this->sandogh_moeen_id = (int)$r['sandogh_moeen_id'];
				$this->takhfif = (int)$r['takhfif'];
				$this->protected = (int)$r['protected'];
			}
		}
        	public function loadRoomCss($css_n='')
	        {
                	$out = null;
        	        $class_found = FALSE;
	                $css_class = '';
                	$css_array = array();
        	        $lines = file('../css/style.css');
	                foreach($lines as $line)
                	{
        	                if(strpos($line,'.room_closed_')!==FALSE)
	                        {
                        	        $class_found = TRUE;
                	                $css_class .= $line."\n";
        	                }
	                        else if($class_found)
                        	{
                	                $css_class .= $line."\n";
        	                        if(strpos($line,'}')!==FALSE)
	                                {
                                        	$class_found = FALSE;
                                	        $css_array[] = $css_class;
                        	                $css_class = '';
                	                }
        	                }
	                }
                	for($i = 0;$i < count($css_array);$i++)
        	        {
	                        $tmp = explode('{',$css_array[$i]);
                        	$css_name = explode('.',$tmp[0]);
                	        $css_name = trim($css_name[1]);
        	                $css_color = '#100000';
	                        $tmp = explode(';',$tmp[1]);
                        	for($j=0;$j<count($tmp);$j++)
                	        {
        	                        $tmp1 = explode(':',$tmp[$j]);
	                                if(trim($tmp1[0])=='background-color')
                                	        $css_color = trim($tmp1[1]);
                        	}
                	        if($css_n == '')
        	                        $out[$css_color] = $css_name;
	                        else if($css_n == $css_name)
                        	        $out = $css_color;
                	}
        	        return($out);
	        }
		public function loadByKol($kol_id)
		{
			mysql_class::ex_sql("select * from `daftar` where `kol_id` = $kol_id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $this->id=$r['id'];
                                $this->name=$r['name'];
                                $this->toz=$r['toz'];
                                $this->kol_id=(int)$r['kol_id'];
				$this->css_class=$r['css_class'];
				$this->sandogh_moeen_id = $r['sandogh_moeen_id'];
				$this->protected = (int)$r['protected'];
                        }
		}
		public function getId()
		{
			return($this->id);
		}
		public function loadUsers($daftar_id=-1)
		{
			$out =array();
			if($daftar_id==-1)
				$daftar_id = $this->id;
			else
				$daftar_id = (int)$daftar_id;
			mysql_class::ex_sql("select `id` from `user` where `daftar_id`=$daftar_id order by `lname`",$q);
			while($r=mysql_fetch_array($q))
				$out[]=(int)$r['id'];
			return $out;
		}
		public function hotelList($id = -1)
		{
			$id = (int)$id;
			$out = null;
// 			echo "select `hotel_id` from `hotel_daftar` where `daftar_id` = $id";
			mysql_class::ex_sql("select `hotel_id` from `hotel_daftar` where `daftar_id` = $id",$q);
			while($r = mysql_fetch_array($q))
				$out[] = (int)$r['hotel_id'];
			return($out);
		}
		public function legend($cols = 5)
		{
			$out = "<table>\n";
			mysql_class::ex_sql('select `name`,`css_class` from `daftar` /*'.(($_SESSION['daftar_id']!=49)?' where id = '.$_SESSION['daftar_id']:'').'*/ order by `name`',$q);
			$i = 0;
			$start = TRUE;
			while($r = mysql_fetch_array($q))
			{
				if($i % $cols == 0 && $start)
				{
					$start = FALSE;
					$out .= "<tr>\n";
				}
				else if($i % $cols == 0)
                                        $out .= "</tr><tr>\n";
				$out .= "<td title=\"".$r['name']."\" class=\"".$r['css_class']."\">\n&nbsp;&nbsp;&nbsp;\n</td><td><--".$r['name']."\n</td>\n";
				$i++;
			}
			if($i % $cols != 0)
				$out .= "</tr>\n";
			$out .= "</table>\n";
			return($out);
		}
	}
?>
