<?php /* In The Name Of Allah */

/*New Software Hijri_Shamsi,Solar(Jalali) Date and Time
Copyright(C)2011, Reza Gholampanahi , http://jdf.scr.ir
version 2.10 :: 1390/01/04 = 1432/04/19 = 2011/03/24 */

/* F */
function jdate($format,$timestamp='',$none='',$time_zone='Asia/Tehran',$tr_num='fa'){

$T_sec='0';/* <= رفع خطای زمان سرور ، با اعداد '+' و '-' بر حسب ثانیه */

if($time_zone=='')$time_zone='Asia/Tehran';date_default_timezone_set($time_zone);
$ts=($timestamp==''or$timestamp=='now')?time()+$T_sec:tr_num($timestamp)+$T_sec;
$date=explode('_',date('a_d_m_N_w_Y',$ts));
list($j_y,$j_m,$j_d)=jgregorian_to_jalali($date[5],$date[2],$date[1]);
$doy=($j_m<7)?((($j_m-1)*31)+$j_d-1):((($j_m-7)*30)+$j_d+185);
$kab=($j_y%4==3)?1:0;$out='';

for($i=0;$i<strlen($format);$i++){$sub=substr($format,$i,1);
if($sub=='\\'){$out.=substr($format,($i+1),1);$i++;}
switch($sub){

case'C':case'E':case'R':case'x':case'X':$out.='<a href="http:/
/jdf.scr.ir/">دریافت نسخه ی جدید http://jdf.scr.ir</a>';break;

case'\\':$out.='';break;

case'B':case'e':case'g':case'G':case'h':case'H':case'i':case'I':case'O':
case'P':case's':case'T':case'u':case'Z':/* */$out.=date($sub,$ts);break;

case'a':
$out.=($date[0]=='pm')?'ب.ظ':'ق.ظ';
break;

case'A':
$out.=($date[0]=='pm')?'بعد از ظهر':'قبل از ظهر';
break;

case'b':
$out.=ceil($j_m/3);
break;

case'c':
$out.=jdate('Y/n/j ,H:i:s P',$ts,'',$time_zone,$tr_num);
break;

case'd':
$out.=($j_d<10)?'0'.$j_d:$j_d;
break;

case'D':
$key=array('ی','د','س','چ','پ','ج','ش');
$out.=$key[$date[4]];
break;

case'f':
$key=array('بهار','تابستان','پاییز','زمستان');
$out.=$key[ceil($j_m/3)-1];
break;

case'F':
$key=array(
'فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
$out.=$key[$j_m-1];
break;

case'j':
$out.=$j_d;
break;

case'J':
$key=array('یک','دو','سه','چهار','پنج','شش','هفت','هشت','نه','ده','یازده','دوازده','سیزده',
'چهارده','پانزده','شانزده','هفده','هجده','نوزده','بیست','بیست و یک','بیست و دو','بیست و سه',
'بیست و چهار','بیست و پنج','بیست و شش','بیست و هفت','بیست و هشت','بیست و نه','سی','سی و یک');
$out.=$key[$j_d-1];
break;

case'k';
$out.=100-round(($doy/($kab+365)*100),1);
break;

case'K':
$out.=round(($doy/($kab+365)*100),1);
break;

case'l':
$key=array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه');
$out.=$key[$date[4]];
break;

case'L':
$out.=$kab;
break;

case'm':
$out.=($j_m<10)?'0'.$j_m:$j_m;
break;

case'M':
$key=array('فر','ار','خر','تی‍','مر','شه‍','مه‍','آب‍','آذ','دی','به‍','اس‍');
$out.=$key[$j_m-1];
break;

case'n':
$out.=$j_m;
break;

case'N':
$out.=($date[3]!=7)?$date[3]+1:1;
break;

case'o':
$jdw=jdate('w',$ts,'',$time_zone,'en');$dny=364+$kab-$doy;
$out.=($doy<3 and$jdw>($doy+3))?$j_y-1:(($dny<3 and(3-$dny)>$jdw)?$j_y+1:$j_y);
break;

case'p':
$key=array('حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت');
$out.=$key[$j_m-1];
break;

case'q':
$key=array('مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ');
$out.=$key[$j_y%12];
break;

case'Q':
$out.=$kab+364-$doy;
break;

case'r':
$out.=jdate('H:i:s O l, j F Y',$ts,'',$time_zone,$tr_num);
break;

case'S':
$out.='ام';
break;

case't':
$out.=($j_m!=12)?(31-(int)($j_m/6.5)):($kab+29);
break;

case'U':
$out.=$ts;
break;

case'v':
$xy3=substr($j_y,2,1);$xy34=substr($j_y,2,2);$xy4=substr($j_y,3,1);
$h3=$h34=$h4='';if($xy3==1){$p34='';
$k34=array('ده','یازده','دوازده','سیزده','چهارده','پانزده','شانزده','هفده','هجده','نوزده');
$h34=$k34[$xy34-10];}else{$p34=($xy3==0 or$xy4==0)?'':' و ';
$k3=array('','','بیست','سی','چهل','پنجاه','شصت','هفتاد','هشتاد','نود');$h3=$k3[$xy3];
$k4=array('','یک','دو','سه','چهار','پنج','شش','هفت','هشت','نه');$h4=$k4[$xy4];}
$out.=$h3.$p34.$h34.$h4;
break;

case'V':
$xy12=substr($j_y,0,2);$xy34=substr($j_y,2,2);
$n12=array('00','13','14');$k12=array('','هزار و سیصد','هزار و چهارصد');
$h12=str_ireplace($n12,$k12,$xy12);$p23=($xy34=='00')?'':' و ';
$h34=jdate('v',$ts,'',$time_zone);
$out.=$h12.$p23.$h34;
break;

case'w':
$out.=($date[4]!=6)?$date[4]+1:0;
break;

case'W':
$avs=jdate('w',$ts-($doy*86400),'',$time_zone,'en');$num=(int)(($doy+$avs)/7);
if($avs<4){$num++;}elseif($num<1){$num=($avs==(($j_y%4==0)?5:4)or$avs==4)?53:52;}
$aks=$avs+$kab;if($aks==7)$aks=0;
$out.=($aks<3 and($kab+363-$doy)<$aks)?'01':(($num<10)?'0'.$num:$num);
break;

case'y':
$out.=substr($j_y,2,2);
break;

case'Y':
$out.=$j_y;
break;

case'z':
$out.=$doy;
break;

default:$out.=$sub;}}
return($tr_num=='fa'or$tr_num=='')?(tr_num($out,'fa')):$out;}

/* F */
function jmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst='-1'){
$h=tr_num($h);$m=tr_num($m);$s=tr_num($s);
if($h==''and$m==''and$s==''and$jm==''and$jm==''and$jd==''and$jy==''){
return mktime();}else{list($year,$month,$day)=jalali_to_jgregorian($jy,$jm,$jd);
return mktime($h,$m,$s,$month,$day,$year,$is_dst);}}

/* F */
function jgetdate($timestamp='',$none='',$tz='Asia/Tehran',$tn='en'){
$ts=($timestamp=='')?time():tr_num($timestamp);
return array('seconds'=>tr_num((int)jdate('s',$ts,'',$tz,'en'),$tn),
'minutes'=>tr_num((int)jdate('i',$ts,'',$tz,'en'),$tn),
'hours'=>jdate('G',$ts,'',$tz,$tn),'mday'=>jdate('j',$ts,'',$tz,$tn),
'wday'=>jdate('w',$ts,'',$tz,$tn),'mon'=>jdate('n',$ts,'',$tz,$tn),
'year'=>jdate('Y',$ts,'',$tz,$tn),'yday'=>jdate('z',$ts,'',$tz,$tn),
'weekday'=>jdate('l',$ts,'',$tz,$ts),'month'=>jdate('F',$ts,'',$tz,$ts),
0=>tr_num($ts,$tn));}

/* F */
function jcheckdate($jm,$jd,$jy){
$jm=tr_num($jm);$jd=tr_num($jd);
$l_d=($jm!=12)?(31-(int)($jm/6.5)):(($jy%4==3)?30:29);
return($jm<13 and$jm>0 and$l_d>=$jd and$jd>0)?true:false;}

/* Convertor from and to Gregorian and Jalali (Hijri_Shamsi,Solar) Functions
Copyright(C)2011,Reza Gholampanahi [ http://jdf.scr.ir/jdf ] version 2.00 */
/* [ http://jdf.scr.ir/jdf ] : جهت کسب اطّلاعات بيشتر در مورد توابع اصلي زير */

/* F */
function jgregorian_to_jalali($g_y,$g_m,$g_d,$mod=''){/* [ http://jdf.scr.ir/jdf ] */
$g_y=tr_num($g_y);$g_m=tr_num($g_m);$g_d=tr_num($g_d);/*<= :اين جزء تابع اصلي نيست*/
$d_m_g_a= array(0,31,(($g_y%4==0)?29:28),31,30,31,30,31,31,30,31,30,31);$res=0;$i=1;
foreach($d_m_g_a as$i=>$v){if($i<$g_m){$res+=$v;$i++;}} $doy_g=($res+$g_d); $jy=/**/
($doy_g<80)?$g_y-622:$g_y-621;$jd=($doy_g>79)?$doy_g-79:$doy_g+(($jy%4==3)?287:286);
$d_m_j_a=array(0,31,31,31,31,31,31,30,30,30,30,30,(($jy%4==3)?30:29));$jdi=1;
foreach($d_m_j_a as$i=>$v){if(($jdi-$v)>0){$jd-=$v;$jdi=$jd;$jm=($i+1);$i++;}
else{$jdi=0;}}/**/return($mod=='')?array($jy,$jm,$jd):$jy.$mod.$jm.$mod.$jd;}

/* F */
function jalali_to_jgregorian($j_y,$j_m,$j_d,$mod=''){ /* => => [ http://jdf.scr.ir/jdf ] */
$j_y=tr_num($j_y);$j_m=tr_num($j_m);$j_d=tr_num($j_d);/*<= <= :اين جزء تابع اصلي نيست <= */
$doy_j=($j_m<7)?( ( ($j_m-1)*31 )+$j_d):((($j_m-7)*30)+$j_d)+186;if($doy_j>(($j_y%4==3)/**/
?287:286)){$gy=$j_y+622;$gd=$doy_j-(($j_y%4==3)?287:286);}else{$gy=$j_y+621;$gd=$doy_j+79;}
$d_m_g_a=array(0,31,(($gy%4==0)?29:28),31,30,31,30,31,31,30,31,30,31);$gdi=1;
foreach($d_m_g_a as$i=>$v){if(($gdi-$v)>0){$gd-=$v;$gdi=$gd;$gm=($i+1);$i++;}
else{$gdi=0;}}/**/return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;}

/* F */
function tr_num($str,$mod='en'){
$num_a=array('0','1','2','3','4','5','6','7','8','9');
$key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
return($mod=='fa')?str_ireplace($num_a,$key_a,$str):str_ireplace($key_a,$num_a,$str);}

/* [ jdf.php ] version 2.10 :: Download new version from [ http://jdf.scr.ir ] */
