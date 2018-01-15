<?php
	class kala_class
	{
                public $id=-1;
                public $name="";
                public $code="";
                public $kala_no_id=-1;
                public $vahed_id=-1;
                public $tedad_dasti = -1;
                public function __construct($id=-1)
                {
                        mysql_class::ex_sql("select * from `kala` where `id` = $id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $this->id=$r['id'];
                                $this->name=$r['name'];
                                $this->code=$r['code'];
                                $this->kala_no_id=$r['kala_no_id'];
                                $this->vahed_id=$r['vahed_id'];
                                $this->tedad_dasti = (int)$r['tedad_dasti'];
                        }
                }
                public function anbarGardaniArray($anbar_id)
                {
                        $anbar = new anbar_class((int)$anbar_id);
                        $kalas = $anbar->loadKala();
                        $out = null;
                        mysql_class::ex_sql("select `id`,`tedad_dasti` from `kala` where `id` in ($kalas) and `tedad_dasti` >= 0",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $tedad = anbar_det_class::getMojoodi((int)$r['id']);
                                $out[] = array('id'=>(int)$r['id'],'tedad_new'=>(int)$r['tedad_dasti'],'tedad'=>$tedad['out']);
                        }
                        return($out);
                }

	}
?>
