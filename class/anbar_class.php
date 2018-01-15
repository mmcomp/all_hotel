<?php
	class anbar_class
	{
                public $id=-1;
                public $name="";
                public $location="";
                public $en=-1;
                public $moeen_id=-1;
                public $moeen_anbardar_id=-1;
                public function __construct($id=-1)
                {
                        mysql_class::ex_sql("select * from `anbar` where `id` = $id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $this->id=$r['id'];
                                $this->name=$r['name'];
                                $this->location=$r['location'];
                                $this->en=$r['en'];
                                $this->moeen_id=$r['moeen_id'];
                                $this->moeen_anbardar_id=$r['moeen_anbardar_id'];
                        }
                }
                public function loadKala($loadArray = FALSE)
                {
                        $kalas = mysql_class::getInArray('kala_id','anbar_det',' `anbar_id` = '.$this->id);
                        if($loadArray)
                                $kalas = explode(',',$kalas);
                        return($kalas);
                }
	}
?>
