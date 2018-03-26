<?php
/*
SyjBPg1NutKt5YlWl5bd3A==

6YslY0gFE0xVmsnhn/odl6Q5EUCwt7pu1MSq3oSGljg=
104390
*/
class amaken{
  public $endPoint = 'http://46.105.248.68/';
  public $KelidVahed = '6YslY0gFE0xVmsnhn/odl6Q5EUCwt7pu1MSq3oSGljg=';
  public $CodeVahed = '104390';
  public $mehman_object = NULL;
  public $mehman_params = array();
  public function __construct($end_point,$KelidVahed,$CodeVahed){
    $end_point = trim($end_point);
    $KelidVahed = trim($KelidVahed);
    $CodeVahed = trim($CodeVahed);
    
    if($end_point!=''){
      $this->endPoint = $end_point;
    }
    if($KelidVahed!=''){
      $this->KelidVahed = $KelidVahed;
    }
    if($CodeVahed!=''){
      $this->CodeVahed = $CodeVahed;
    }
    
  }
  public function execFunction($function,$pars=array()){
    $params = '';
    foreach($pars as $par){
      $params .='&'.urlencode($par['key']).'='.urlencode($par['value']);
    }
    $url = $this->endPoint.$function.'?KelidVahed='.$this->KelidVahed.'&CodeVahed='.$this->CodeVahed.$params;
//     echo $url."<br/>\n";
//     die();
    $out = file_get_contents($url,TRUE);
//     $out = @json_decode(file_get_contents($url,TRUE));
    return $out;
  }
  public function getMehman($mehman_object){
    $this->mehman_object = $mehman_object;
    $this->createMehmanParams();
  }
  private function perToEn($inNum){
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
  private function preparePDate($inp){
    $out = jdate("Y/m/d",strtotime($inp));
    return $out;
  }
  private function prepareDate($inp){
    $out = str_replace('-','/',$inp);
    $out = $this->perToEn($out);
    return $out;
  }
  private function prepareTime($inp){
    $tmp = explode(':',trim($inp));
    if(count($tmp)==3){
      $out = $tmp[0].':'.$tmp[1];
    }else if(count($tmp)==2){
      $out = trim($inp);
    }else{
      $out = '00:00';
    }
    return $out;
  }
  private function prepareJob($inp){
    $out = $inp;
    return $out;
  }
  private function prepareNesbat($inp){
    $out = $inp;
    $out = 2;
    return $out;
  }
  private function prepareSafarDalil($inp){
    $out = $inp;
    $out = 1;
    return $out;
  }
  private function prepareMeliat($inp){
    $out = $inp;
    $out = 100038;
    return $out;
  }
  private function prepareCity($inp){
    $out = $inp;
    $out = 1;
    return $out;
  }
  private function prepareGender($inp){
    $out = (int)$inp;
    if($out<10){
      if($out < 0){
        $out = 1;
      }else if($out == 0){
        $out = 2;
      }else if($out!=1 && $out!=2){
        $out = 1;
      }
    }else{
      if($out == 12){
        $out = 2;
      }else{
        $out = 1;
      }
    }
    return $out;
  }
  private function createMehmanParams(){
    $NameMosafer = $this->mehman_object->fname;
    $FamilMosafer = $this->mehman_object->lname;
    $NamePedar = $this->mehman_object->p_name;
    $ShomareShenasaee = $this->mehman_object->ss;
    $TarikhTavalod = $this->prepareDate($this->mehman_object->tt);
    $ID_Jensiat = $this->prepareGender($this->mehman_object->gender);
    $ID_Shoghl = $this->prepareJob($this->mehman_object->job);
    $ID_ElaatSafar = $this->prepareSafarDalil($this->mehman_object->safar_dalil);
    $TedadHamrah = $this->mehman_object->hamrah;
    $ID_Nesbat = $this->prepareNesbat($this->mehman_object->nesbat);
    $MosafereKhareji = 0;
    $ID_Meliat = $this->prepareMeliat($this->mehman_object->melliat);
    $ID_Mabda = $this->prepareCity($this->mehman_object->mabda);
    $ID_Maghsad = $this->prepareCity($this->mehman_object->maghsad);
    $ID_MahaleTavalod = $this->prepareCity($this->mehman_object->ms);
    $TarikhVorod = $this->prepareDate($this->preparePDate($this->mehman_object->vorood));
    $TarikhKhoroj = $this->prepareDate($this->preparePDate($this->mehman_object->khorooj));
    $SaatVorod = $this->prepareTime($this->mehman_object->vorood_h);
    $SaatKhoroj = date("H:s",strtotime($this->mehman_object->khorooj));
    $ShomareOtagh = $this->mehman_object->room_name;
    $RecordMosafer = $this->mehman_object->id;
    $ShomareFaragir = '';
    $ShomarePaziresh = $this->mehman_object->reserve_id;
    $Code_Moaref = 0;
    $Name_Moaref = '';
    $Tel_Moaref = '';
    $NameKarbareSabt = '';
    $ID_NoeDadeh = 1;
    $this->mehman_params = array(
      array(
        "key"=>"NameMosafer",
        "value"=>$NameMosafer
      ),
      array(
        "key"=>"FamilMosafer",
        "value"=>$FamilMosafer
      ),
      array(
        "key"=>"NamePedar",
        "value"=>$NamePedar
      ),
      array(
        "key"=>"ShomareShenasaee",
        "value"=>$ShomareShenasaee
      ),
      array(
        "key"=>"TarikhTavalod",
        "value"=>$TarikhTavalod
      ),
      array(
        "key"=>"ID_Jensiat",
        "value"=>$ID_Jensiat
      ),
      array(
        "key"=>"ID_Shoghl",
        "value"=>$ID_Shoghl
      ),
      array(
        "key"=>"ID_ElaatSafar",
        "value"=>$ID_ElaatSafar
      ),
      array(
        "key"=>"TedadHamrah",
        "value"=>$TedadHamrah
      ),
      array(
        "key"=>"ID_Nesbat",
        "value"=>$ID_Nesbat
      ),
      array(
        "key"=>"MosafereKhareji",
        "value"=>$MosafereKhareji
      ),
      array(
        "key"=>"ID_Meliat",
        "value"=>$ID_Meliat
      ),
      array(
        "key"=>"ID_Mabda",
        "value"=>$ID_Mabda
      ),
      array(
        "key"=>"ID_Maghsad",
        "value"=>$ID_Maghsad
      ),
      array(
        "key"=>"ID_MahaleTavalod",
        "value"=>$ID_MahaleTavalod
      ),
      array(
        "key"=>"TarikhVorod",
        "value"=>$TarikhVorod
      ),
      array(
        "key"=>"TarikhKhoroj",
        "value"=>$TarikhKhoroj
      ),
      array(
        "key"=>"SaatVorod",
        "value"=>$SaatVorod
      ),
      array(
        "key"=>"SaatKhoroj",
        "value"=>$SaatKhoroj
      ),
      array(
        "key"=>"ShomareOtagh",
        "value"=>$ShomareOtagh
      ),
      array(
        "key"=>"RecordMosafer",
        "value"=>$RecordMosafer
      ),
      array(
        "key"=>"ShomareFaragir",
        "value"=>$ShomareFaragir
      ),
      array(
        "key"=>"ShomarePaziresh",
        "value"=>$ShomarePaziresh
      ),
      array(
        "key"=>"Code_Moaref",
        "value"=>$Code_Moaref
      ),
      array(
        "key"=>"Name_Moaref",
        "value"=>$Name_Moaref
      ),
      array(
        "key"=>"Tel_Moaref",
        "value"=>$Tel_Moaref
      ),
      array(
        "key"=>"NameKarbareSabt",
        "value"=>$NameKarbareSabt
      ),
      array(
        "key"=>"ID_NoeDadeh",
        "value"=>$ID_NoeDadeh
      )
    );
  }
  public function sendMehman(){
//     var_dump($this->mehman_params);
    $out = $this->execFunction('Sabt_Mosaferin',$this->mehman_params);
    return $out;
  }
}
// $out = @json_decode(file_get_contents("http://46.105.248.68/GereftanAnavinElaatSafar?KelidVahed=6YslY0gFE0xVmsnhn/odl6Q5EUCwt7pu1MSq3oSGljg=&CodeVahed=104390"),TRUE);//?KelidVahed=6YslY0gFE0xVmsnhn/odl6Q5EUCwt7pu1MSq3oSGljg=&CodeVahed=104390");
// $am = new amaken('','','');
//-------------------------RACK
/*
$rac = "sakhteman:1|tedadotagh:31|0:131|0:132|0:133|0:134|0:135|0:136|0:139|0:140|0:141|0:142|0:143|0:144|0:145|0:146|0:147|0:148|0:151|0:155|0:156|0:157|0:158|";
$rac .= "2:101|2:102|2:103|2:104|2:109|2:110|2:149|2:150|2:152|2:153|";
$params = array(
  array(
    "key"=>"rac",
    "value"=>$rac
  )
);
$out = $am->execFunction('SabtChidemanVahed',$params);//,array(array("key"=>"name","value"=>"abbas"),array("key"=>"age","value"=>"15")));
*/
//-------------------------Mosafer
/*
$NameMosafer = 'تست';
$FamilMosafer = 'تست آبادی';
$NamePedar = 'مهدی';
$ShomareShenasaee = '0938926374';
$TarikhTavalod = '1361/01/14';
$ID_Jensiat = 1;
$ID_Shoghl = 1;
$ID_ElaatSafar = 1;
$TedadHamrah = 0;
$ID_Nesbat = 2;
$MosafereKhareji = 0;
$ID_Meliat = 1;
$ID_Mabda = 1;
$ID_Maghsad = 2;
$ID_MahaleTavalod = 1;
$TarikhVorod = '1396/12/25';
$TarikhKhoroj = '1393/12/26';
$SaatVorod = '14:00';
$SaatKhoroj = '12:00';
$ShomareOtagh = '101';
$RecordMosafer = 0;
$ShomareFaragir = '';
$ShomarePaziresh = '1';
$Code_Moaref = 0;
$Name_Moaref = '';
$Tel_Moaref = '';
$NameKarbareSabt = 'تست ثبت کننده';
$ID_NoeDadeh = 1;
$params = array(
  array(
    "key"=>"NameMosafer",
    "value"=>$NameMosafer
  ),
  array(
    "key"=>"FamilMosafer",
    "value"=>$FamilMosafer
  ),
  array(
    "key"=>"NamePedar",
    "value"=>$NamePedar
  ),
  array(
    "key"=>"ShomareShenasaee",
    "value"=>$ShomareShenasaee
  ),
  array(
    "key"=>"TarikhTavalod",
    "value"=>$TarikhTavalod
  ),
  array(
    "key"=>"ID_Jensiat",
    "value"=>$ID_Jensiat
  ),
  array(
    "key"=>"ID_Shoghl",
    "value"=>$ID_Shoghl
  ),
  array(
    "key"=>"ID_ElaatSafar",
    "value"=>$ID_ElaatSafar
  ),
  array(
    "key"=>"TedadHamrah",
    "value"=>$TedadHamrah
  ),
  array(
    "key"=>"ID_Nesbat",
    "value"=>$ID_Nesbat
  ),
  array(
    "key"=>"MosafereKhareji",
    "value"=>$MosafereKhareji
  ),
  array(
    "key"=>"ID_Meliat",
    "value"=>$ID_Meliat
  ),
  array(
    "key"=>"ID_Mabda",
    "value"=>$ID_Mabda
  ),
  array(
    "key"=>"ID_Maghsad",
    "value"=>$ID_Maghsad
  ),
  array(
    "key"=>"ID_MahaleTavalod",
    "value"=>$ID_MahaleTavalod
  ),
  array(
    "key"=>"TarikhVorod",
    "value"=>$TarikhVorod
  ),
  array(
    "key"=>"TarikhKhoroj",
    "value"=>$TarikhKhoroj
  ),
  array(
    "key"=>"SaatVorod",
    "value"=>$SaatVorod
  ),
  array(
    "key"=>"SaatKhoroj",
    "value"=>$SaatKhoroj
  ),
  array(
    "key"=>"ShomareOtagh",
    "value"=>$ShomareOtagh
  ),
  array(
    "key"=>"RecordMosafer",
    "value"=>$RecordMosafer
  ),
  array(
    "key"=>"ShomareFaragir",
    "value"=>$ShomareFaragir
  ),
  array(
    "key"=>"ShomarePaziresh",
    "value"=>$ShomarePaziresh
  ),
  array(
    "key"=>"Code_Moaref",
    "value"=>$Code_Moaref
  ),
  array(
    "key"=>"Name_Moaref",
    "value"=>$Name_Moaref
  ),
  array(
    "key"=>"Tel_Moaref",
    "value"=>$Tel_Moaref
  ),
  array(
    "key"=>"NameKarbareSabt",
    "value"=>$NameKarbareSabt
  ),
  array(
    "key"=>"ID_NoeDadeh",
    "value"=>$ID_NoeDadeh
  )
);
$out = $am->execFunction('Sabt_Mosaferin',$params);
var_dump($out);
*/