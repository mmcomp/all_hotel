<?php	session_start();
	include_once("../kernel.php");
	function loadhotelTyp($selected){
		global $hotel;
		$out = '<option value="0"></option>';
		$typs = $hotel->getTyps();
		foreach($typs as $typ){
			$out .= '<option value="'.$typ['id'].'"'.(($typ['id']==$selected)?' selected':'').'>'.$typ['name'].'</option>';
		}
		return $out;
	}
	function loadhotelStar($selected){
		global $hotel;
		$out = '';
		$stars = array(
			'',
			'۱ ستاره',
			'۲ ستاره',
			'ستاره ۳',
			'ستاره ۴',
			'ستاره ۵',
			'درجه ۱',
			'درجه ۲',
			'درجه ۳',
			'ممتاز',
		);
		foreach($stars as $i=>$star){
			$out .= '<option value="'.$i.'"'.(($i==$selected)?' selected':'').'>'.$star.'</option>';
		}
		return $out;
	}
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$pout1 = "";
	if(!isset($_REQUEST['hotel_id']) && (int)$_REQUEST['hotel_id']>0)
		die("<script language=\"javascript\">window.opener.location = window.opener.location;window.close();</script>");
	$hotel_id = (int)$_REQUEST['hotel_id'];
	$hotel = new hotel_class($hotel_id);

$pout1="

<div class=\"modal-dialog modal-lg\">
            <div class=\"modal-content\">
			<form class=\"form-horizontal row-border\" id=\"hh\" method=\"post\" >
                <div class=\"modal-header\" style=\"background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;\">
                    <button style=\"float:left\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
                    <h4 class=\"modal-title\">مشخصات ".$hotel->name."</h4>
                </div>
                <div class=\"modal-body\" style=\"max-height:300px;overflow-y:scroll\">
                    
                        <input type=\"hidden\" id=\"hotel_id\" name=\"hotel_id\" value=\"".$hotel_id."\" />
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">تاریخ ساخت:  </label>
															<input class=\"form-control inp\" type=\"text\" id=\"tarikh_sakht\" name=\"tarikh_sakht\" value=\"".(isset($hotel->tarikh_sakht)?$hotel->tarikh_sakht:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> وضعیت دید:  </label>
															<textarea class=\"form-control\"   id=\"vaziat_did\" name=\"vaziat_did\" >".(isset($hotel->vaziat_did)?$hotel->vaziat_did:'')."</textarea>
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">تعداد اتاق  : </label>
															<input class=\"form-control inp\"  type=\"text\" id=\"tedad_otagh\" name=\"tedad_otagh\" value=\"".(isset($hotel->tedad_otagh)?$hotel->tedad_otagh:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">تعداد طبقات:  </label>
															<input class=\"form-control inp\"  type=\"text\" id=\"tedad_tabaghat\" name=\"tedad_tabaghat\" value=\"".(isset($hotel->tedad_tabaghat)?$hotel->tedad_tabaghat:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> تعداد تخت:  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"tedad_takht\" name=\"tedad_takht\" value=\"".(isset($hotel->tedad_takht)?$hotel->tedad_takht:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">ظرفیت لابی: </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_lobby\" name=\"zarfiat_lobby\" value=\"".(isset($hotel->zarfiat_lobby)?$hotel->zarfiat_lobby:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">وضعیت ترافیک:  </label>
															<textarea class=\"form-control\" id=\"vaziat_terafik\" name=\"vaziat_terafik\">".(isset($hotel->vaziat_terafik)?$hotel->vaziat_terafik:'')."</textarea>
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> ظرفیت سالن همایش:  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_salon_hamayesh\" name=\"zarfiat_salon_hamayesh\" value=\"".(isset($hotel->zarfiat_salon_hamayesh)?$hotel->zarfiat_salon_hamayesh:0)."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">ظرفیت سالن عروسی: </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_salon_aroosi\" name=\"zarfiat_salon_aroosi\" value=\"".(isset($hotel->zarfiat_salon_aroosi)?$hotel->zarfiat_salon_aroosi:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">ظرفیت سالن کنفرانس:  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_salon_confrance\" name=\"zarfiat_salon_confrance\" value=\"".(isset($hotel->zarfiat_salon_confrance)?$hotel->zarfiat_salon_confrance:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">ظرفیت رستوران :  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_resturan\" name=\"zarfiat_resturan\" value=\"".(isset($hotel->zarfiat_resturan)?$hotel->zarfiat_resturan:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> ظرفیت پارکینگ : </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"zarfiat_parking\" name=\"zarfiat_parking\" value=\"".(isset($hotel->zarfiat_parking)?$hotel->zarfiat_parking:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">فاصله تا فرودگاه(کیلومتر):  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_foroodgah\" name=\"fasele_ta_foroodgah\" value=\"".(isset($hotel->fasele_ta_foroodgah)?$hotel->fasele_ta_foroodgah:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> فاصله تا راه آهن(کیلومتر):  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_rahahan\" name=\"fasele_ta_rahahan\" value=\"".(isset($hotel->fasele_ta_rahahan)?$hotel->fasele_ta_rahahan:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">فاصله تا پایانه مسافربری(کیلومتر)  : </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_payane_mosaferbari\" name=\"fasele_ta_payane_mosaferbari\" value=\"".(isset($hotel->fasele_ta_payane_mosaferbari)?$hotel->fasele_ta_payane_mosaferbari:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">فاصله تا حرم(کیلومتر):  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_haram\" name=\"fasele_ta_haram\" value=\"".(isset($hotel->fasele_ta_haram)?$hotel->fasele_ta_haram:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> فاصله تا مراکز خرید(کیلومتر):  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_marakez_kharid\" name=\"fasele_ta_marakez_kharid\" value=\"".(isset($hotel->fasele_ta_marakez_kharid)?$hotel->fasele_ta_marakez_kharid:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">فاصله تا خیابان اصلی(کیلومتر)  : </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_khiaban_asli\" name=\"fasele_ta_khiaban_asli\" value=\"".(isset($hotel->fasele_ta_khiaban_asli)?$hotel->fasele_ta_khiaban_asli:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">فاصله تا مراکز تاریخی(کیلومتر):  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_marakez_tarikhi\" name=\"fasele_ta_marakez_tarikhi\" value=\"".(isset($hotel->fasele_ta_marakez_tarikhi)?$hotel->fasele_ta_marakez_tarikhi:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> فاصله تا مراکز تفریحی:  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"fasele_ta_marakez_tafrihi\" name=\"fasele_ta_marakez_tafrihi\" value=\"".(isset($hotel->fasele_ta_marakez_tafrihi)?$hotel->fasele_ta_marakez_tafrihi:'')."\" />
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\">ساعت تحویل اتاق  : </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"saate_tahvil_otagh\" name=\"saate_tahvil_otagh\" value=\"".(isset($hotel->saate_tahvil_otagh)?$hotel->saate_tahvil_otagh:'')."\" />
													</div>
												</div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">ساعت تخلیه اتاق:  </label>
															<input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"saate_takhlie_otagh\" name=\"saate_takhlie_otagh\" value=\"".(isset($hotel->saate_takhlie_otagh)?$hotel->saate_takhlie_otagh:'')."\" />
													</div>
													<div class=\"col-md-4\">
															<label for=\"name\">  آدرس:  </label>
															<textarea class=\"form-control\"   id=\"address\" name=\"address\" >".(isset($hotel->address)?$hotel->address:'')."</textarea>
													</div>

													<div class=\"col-md-4\">
															<label for=\"name\"> تلفن ها  : </label>
															<input class=\"form-control inp\"  type=\"text\" id=\"tel\" name=\"tel\" value=\"".(isset($hotel->tel)?$hotel->tel:'')."\" />
													</div>
                        </div>
												<div class=\"row\">
													<div class=\"col-md-4\">
															<label for=\"name\">تعداد ستاره:  </label>
															<select class=\"form-control inp\"  id=\"star\" name=\"star\" >".loadhotelStar((isset($hotel->star)?$hotel->star:-1))."</select>
 															<!-- <input class=\"form-control inp\" style=\"direction:ltr;\" type=\"text\" id=\"star\" name=\"star\" value=\"".(isset($hotel->star)?$hotel->star:'')."\" /> -->
													</div>
													<div class=\"col-md-4\">
															<label for=\"name\"> نوع اقامتگاه:  </label>
															<select class=\"form-control inp\"  id=\"typ_id\" name=\"typ_id\" >".loadhotelTyp((isset($hotel->typ_id)?$hotel->typ_id:-1))."</select>
													</div>
												</div>
                </div>
			
                <div class=\"modal-footer\">
				    				<a  class=\"btn btn-default\" data-dismiss=\"modal\" href=\"#\">انصراف</a>
                    <span class=\"btn btn-warning\" data-dismiss=\"modal\" onclick=\"$('#hh').submit();\" >افزودن</span>
                </div>
            </form>	
        </div>
    </div>


";
echo $pout1;
?>


