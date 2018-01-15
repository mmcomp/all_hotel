<?php
	session_start();
	include("../kernel.php");

       /* if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');

	if (isset($_SESSION['user_id']))
		$user_id = (int)$_SESSION['user_id'];
	else
		$user_id = -1;
*/
$roomthis = (isset($_POST['oroomthis']))?$_POST['oroomthis']:"";
$roomthis2 = explode("|",$roomthis);
$count = (count($roomthis2))-1;
$getinput = (isset($_POST['ogetinput']))?$_POST['ogetinput']:"";
$reserve_id = (isset($_POST['oreserve_id']))?$_POST['oreserve_id']:"";
$i=0;
foreach($roomthis2 as $roomthis3){
    $roomthis4 = explode("_",$roomthis3);
    $name = $roomthis4[0];
    $room_id = $roomthis4[1];
    $nafar = $getinput[$i];
    $i++;
   $q =  mysql_class::ex_sqlx("update room_det set nafar='$nafar' where room_id='$room_id' and reserve_id = '$reserve_id'");
}
 if($q)
        echo "1";
    else
        echo "0";
//$m="";
//foreach ($getinput as $getinputs){
    //echo $getinputs."|".$m;
//}
/*
	$msg='';
	if(isset($_POST['reserve_id']))
	{
		foreach($_POST as $key=>$val)
		{
			$tt = explode('_',$key);
			if($tt[0]=='hs')
			{
				$val = (int)$val;
				mysql_class::ex_sqlx("update room_det set nafar = $val where room_id=".$tt[1]." and reserve_id = $reserve_id");
			}
		}
		$msg = "بروز رسانی با موفقیت انجام شد";
	}
	$room = room_det_class::loadDetByReserve_id($reserve_id );
	$rooms = '';
	$sum = 0;
	for($j=0;$j<count($room['rooms']);$j++)
	{
		$tmp_room = new room_class($room['rooms'][$j]['room_id']);
		//$rooms.='<span style="cursor:pointer;" onclick="setNafar('.$tmp_room->id.','.$reserve_id.');" >'.$tmp_room->name.'['.$room['rooms'][$j]['nafar'].']</span>'.(($j<count($room['rooms'])-1)?' , ':'');
		$rooms .='<div>';
		$rooms .='<span>'.$tmp_room->name.'</span> ';
		$rooms .='<input class="room" type="number" name="hs_'.$tmp_room->id.'_'.$reserve_id.'" value="'.$room['rooms'][$j]['nafar'].'">';
		$rooms .='</div>';
		$sum+=$room['rooms'][$j]['nafar'];
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script>
			var jam_kol =<?php echo $sum; ?>;
			function check_go()
			{
				var tmp =0;
				var tt;
				var bool=true;
				$(".room").each(function(id,field){
					tt= parseInt($(field).val(),10);
					if(!isNaN(tt))
					{
						if(tt<0)
						{
							$("#msg").html('تعداد نفرات  در یکی از موارد منفی وارد شده است');				
							bool=false;
						}
						tmp+=tt;
					}
					
				});
				if(bool)
				{
					if(tmp===jam_kol)
					{
						if(confirm("آیا تغییرات ذخیره شود؟"))
						{
							$("#frm").submit();
						}
					}
					else
					{
						$("#msg").html('تعداد نفرات باید '+jam_kol+' باشد درصورتیکه '+tmp+' است');
					}
				}
			}
		</script>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<div style="margin:10px;text-align:center;">
			<div>
				<h3>
					ویرایش نفرات
				</h3>
				شماره رزرو:
				<b> 
				<?php echo $reserve_id; ?>
				</b>
			</div>
			<form id="frm" method="POST" >
			<input type="hidden" name="reserve_id" value="<?php echo $reserve_id; ?>" >
			<?php echo $rooms; ?>
			</form>
			<div id="msg" style="" ><?php echo $msg; ?></div>
			<button class="inp" onclick="check_go();" >ذخیره</button>
		</div>
	</body>
</html>
*/
?>