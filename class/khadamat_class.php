<?php
	class khadamat_class
	{
		public $id=-1;
		public $hotel_id=-1;
		public $name="";
		public $ghimat_def=-1;
		public $typ=-1;
		public $en=-1;
		public $voroodi_darad=FALSE;
		public $khorooji_darad=FALSE;
		public $aval_ekhtiari = 1;
		public function __construct($id=-1)
		{
			$id = (int)$id;
			if($id > 0)
			{
				mysql_class::ex_sql("select * from `khadamat` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->hotel_id=$r['hotel_id'];
					$this->name=$r['name'];
					$this->ghimat_def=$r['ghimat_def'];
					$this->typ=$r['typ'];
					$this->en=$r['en'];
					$this->voroodi_darad=(((int)$r['voroodi_darad']==1)?TRUE:FALSE);
					$this->khorooji_darad=(((int)$r['khorooji_darad']==1)?TRUE:FALSE);
					$this->aval_ekhtiari=$r['aval_ekhtiari'];
					$this->motefareghe=$r['motefareghe'];
					$this->tedadDarRuz=$r['tedadDarRuz'];
				}
			}
		}
		public function loadKhadamats($hotel_id,$typ = -1)
		{
			$out = array();
			$hotel_id =(int)$hotel_id;
			$typ_shart = '';
			if($typ >= 0)
			$typ_shart = " and `typ` = $typ";
			mysql_class::ex_sql("select `name`,`id`,`typ`,`ghimat_def`,`voroodi_darad`,`khorooji_darad`,`aval_ekhtiari`,`aval_ekhtiari`,`motefareghe`,`tedadDarRuz` from `khadamat` where `name` <> '' and not(`name` is null) and  `en` = 1 and `hotel_id` = $hotel_id $typ_shart",$q);
//echo "select `name`,`id`,`typ`,`ghimat_def`,`voroodi_darad`,`khorooji_darad`,`aval_ekhtiari`,`aval_ekhtiari`,`motefareghe`,`tedadDarRuz` from `khadamat` where `name` <> '' and not(`name` is null) and  `en` = 1 and `hotel_id` = $hotel_id $typ_shart";
			while($r = mysql_fetch_array($q))
							$out[] = array('name'=>$r['name'],'id'=>(int)$r['id'],'typ'=>(int)$r['typ'],'ghimat'=>(int)$r['ghimat_def'],'voroodi'=>(((int)$r['voroodi_darad']==1)?TRUE:FALSE),'khorooji'=>(((int)$r['khorooji_darad']==1)?TRUE:FALSE),'aval_ekhtiari'=>(int)$r['aval_ekhtiari'],'motefareghe'=>(int)$r['motefareghe'],'tedadDarRuz'=>(int)$r['tedadDarRuz']);
			return($out);
		}
		public function loadKhadamat_name($khadamat_id)
                {
                        $out = '';
			$id = (int)$khadamat_id;
                        mysql_class::ex_sql("select `name` from `khadamat` where `name` <> '' and `id` ='$id'",$q);
                        if($r = mysql_fetch_array($q))
                                $out = $r["name"];
                        return($out);
                }
		public function isMotefareghe($khadamat_id)
                {
                        $out = FALSE;
			$id = (int)$khadamat_id;
                        mysql_class::ex_sql("select `motefareghe` from `khadamat` where `id` ='$id'",$q);
                        if($r = mysql_fetch_array($q))
			{
				if ($r["motefareghe"]==0)
	                                $out = FALSE;
				else
					$out = TRUE;
			}
                        return($out);
                }
		public function loadFake($startId = 1)
		{
			$startId = $startId;
			$startIdd = $startId+1;
			$startIddd = $startId+2;
			$startIdddd = $startId+3;
			$out = <<<kh
			<table width="100%" id="fake">
			<tr>
				<th colspan="4">
					خدمات به صورت فول برد 
					<input type="checkbox" checked="checked" onclick="resetOrNotKh(this);" >
				</th>
			</tr>
			<tr>
				<td>
					صبحانه:
				</td>
				<td>
					تعدادروزانه:
					<input type='text' class='inp' style='width:30px;' name='kh_txt_$startId' id='kh_txt_$startId'  value='0' >
					<span>اول</span>
					<input    type='checkbox' name='kh_v_$startId' id='kh_v_$startId' >
					<span>آخر</span>
					<input checked="checked"  type='checkbox' name='kh_kh_$startId' id='kh_kh_$startId' >
				</td>
				<td>
					ناهار:
				</td>
				<td>
					تعدادروزانه:
					<input type='text' class='inp' style='width:30px;' name='kh_txt_$startIdd' id='kh_txt_$startIdd'  value='0' >
					<span>اول</span>
					<input    type='checkbox' name='kh_v_$startIdd' id='kh_v_$startIdd' >
					<span>آخر</span>
					<input checked="checked"  type='checkbox' name='kh_kh_$startIdd' id='kh_kh_$startIdd' >
				</td>
			</tr>
			<tr>
				<td>
					شام:
				</td>
				<td>
					تعدادروزانه:
					<input type='text' class='inp' style='width:30px;' name='kh_txt_$startIddd' id='kh_txt_$startIddd'  value='0' >
					<span>اول</span>
					<input checked="checked"   type='checkbox' name='kh_v_$startIddd' id='kh_v_$startIddd' >
					<span>آخر</span>
					<input   type='checkbox' name='kh_kh_$startIddd' id='kh_kh_$startIddd' >
				</td>
				<td>
					ترانسفر :
				</td>
				<td>
					<input checked="checked" style='display:none;' type='checkbox' name='kh_ch_$startIdddd' id='kh_ch_$startIdddd' >
					<span>اول</span>
					<input checked="checked"  onclick='kh_check("4");' type='checkbox' name='kh_v_$startIdddd' id='kh_v_$startIdddd' >
					<span>آخر</span>
					<input checked="checked" onclick='kh_check("4");' type='checkbox' name='kh_kh_$startIdddd' id='kh_kh_$startIdddd' >
				</td>
			</tr>
			</table>
kh;
			return($out);
		}
	}
?>
