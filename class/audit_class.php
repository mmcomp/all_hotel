<?php
class audit_class{
	public function upint($inp)
	{
		$out = (int)$inp;
		if($out+.5 < (float)$inp)
			$out++;
		return($out);
	}
	public function isAdmin($typ){
		$out = FALSE;
		if($typ == 0){
			$out = TRUE;
		}
		return $out;
	}
        public function isReallyAdmin(){
		$typ = -1;
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
                $out = FALSE;
                if($isAdmin){
                        $out = TRUE;
                }
                return $out;
        }
	public function hamed_pdateBack($inp,$tim="14:00:00")
        {
		$inp = perToEnNums($inp);
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
			$y=(int)$tmp[2];
			$m=(int)$tmp[1];
			$d=(int)$tmp[0];
			if ($d>$y)
			{
				$tmp=$y;
				$y=$d;
				$d=$tmp;
			}
			if ($y<1000)
			{
				$y=$y+1300;
			}
			$inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." ".$tim;
        }
	public function hamed_pdateBack_1($inp)
        {
		$inp = perToEnNums($inp);
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
			$y=(int)$tmp[2];
			$m=(int)$tmp[1];
			$d=(int)$tmp[0];
			if ($d>$y)
			{
				$tmp=$y;
				$y=$d;
				$d=$tmp;
			}
			if ($y<1000)
			{
				$y=$y+1300;
			}
			$inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out;
        }
        public function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	public function hamed_pdate_2($str)
        {
                $out=jdate('Y/n/j h:i',strtotime($str));
                return $out;
        }
	public function hamed_pdate_1($str)
        {
                $out=jdate('Y/m/d',strtotime($str));
                return $out;
        }
	public function enToPer($inNum){
		$outp = $inNum;
		$outp = str_replace('0', '۰', $outp);
		$outp = str_replace('1', '۱', $outp);
		$outp = str_replace('2', '۲', $outp);
		$outp = str_replace('3', '۳', $outp);
		$outp = str_replace('4', '۴', $outp);
		$outp = str_replace('5', '۵', $outp);
		$outp = str_replace('6', '۶', $outp);
		$outp = str_replace('7', '۷', $outp);
		$outp = str_replace('8', '۸', $outp);
		$outp = str_replace('9', '۹', $outp);
		return($outp);	
	}
	public function perToEn($inNum){
		$outp = $inNum;
		$outp = str_replace('۰', '0', $outp);
		$outp = str_replace('۱', '1', $outp);
		$outp = str_replace('۲', '2', $outp);
		$outp = str_replace('۳', '3', $outp);
		$outp = str_replace('۴', '4', $outp);
		$outp = str_replace('۵', '5', $outp);
		$outp = str_replace('۶', '6', $outp);
		$outp = str_replace('۷', '7', $outp);
		$outp = str_replace('۸', '8', $outp);
		$outp = str_replace('۹', '9', $outp);
		return($outp);	
        }
    public function hamed_jalalitomiladi($str)
	{
		$s=explode('/',$str);
		$out = "";
		if(count($s)==3){
			$miladi=jalali_to_jgregorian($s[0],$s[1],$s[2]);
			if((int)$miladi[1]<10)
				$miladi[1] = "0".$miladi[1];
			if((int)$miladi[2]<10)
                                $miladi[2] = "0".$miladi[2];
			$out=$miladi[0]."-".$miladi[1]."-".$miladi[2];
		}
		return $out;
		//jalali_to_gregorian()
	}    
}
?>
