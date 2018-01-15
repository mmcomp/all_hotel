<?php	session_start();
	include_once("../kernel.php");
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
// 	var_dump($_REQUEST);
	$ps = "<div class=\"row\">";
	$ps_array = $hotel->getProperties();
	foreach($ps_array as $i=>$ps_small){
		if($i!=0 && $i%3==0){
			$ps.= "</div>
						 <div class=\"row\">";
		}
		$ps.="<div class=\"col-md-4\">
							".$ps_small['name']." :  <input type=\"checkbox\" id=\"prop_".$ps_small['eid']."\" name=\"prop_".$ps_small['eid']."\" ".(($ps_small['edid'])?'checked':'')."/>
					</div>";
	}
$pout1="

<div class=\"modal-dialog modal-lg\">
	<div class=\"modal-content\">
	<form class=\"form-horizontal row-border\" id=\"pp\" method=\"post\" >
		<div class=\"modal-header\" style=\"background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;\">
			<button style=\"float:left\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
			<h4 class=\"modal-title\">ویژگی های ".$hotel->name."</h4>
		</div>
		<div class=\"modal-body\" style=\"max-height:300px;overflow-y:scroll\">
			<input type=\"hidden\" id=\"hotel_id\" name=\"hotel_id\" value=\"".$hotel_id."\" />
			<input type=\"hidden\" name=\"set_prop\" value=\"set\" />
			".$ps."
		</div>			
		<div class=\"modal-footer\">
			<a  class=\"btn btn-default\" data-dismiss=\"modal\" href=\"#\">انصراف</a>
			<span class=\"btn btn-warning\" data-dismiss=\"modal\" onclick=\"$('#pp').submit();\" >افزودن</span>
		</div>
	</form>	
	</div>
</div>


";
echo $pout1;
?>


