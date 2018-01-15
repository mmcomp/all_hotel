<?php
session_start();
include('../kernel.php');
if(!isset($_SESSION['user_vorood']) || !isset($_SESSION['vorood']))
	die("<script language=\"javascript\">window.close();</script>");
$user_id = $_SESSION['user_vorood'];
$vorood = $_SESSION['vorood'];
error_reporting(0);
/**
 * Get the width and height of the destination image
 * from the POST variables and convert them into
 * integer values
 */
$w = (int)$_POST['width'];
$h = (int)$_POST['height'];

// create the image with desired width and height

$img = imagecreatetruecolor($w, $h);

// now fill the image with blank color
// do you remember i wont pass the 0xFFFFFF pixels 
// from flash?
imagefill($img, 0, 0, 0xFFFFFF);

$rows = 0;
$cols = 0;

// now process every POST variable which
// contains a pixel color
for($rows = 0; $rows < $h; $rows++){
	// convert the string into an array of n elements
	$c_row = explode(",", $_POST['px' . $rows]);
	for($cols = 0; $cols < $w; $cols++){
		// get the single pixel color value
		$value = $c_row[$cols];
		// if value is not empty (empty values are the blank pixels)
		if($value != ""){
			// get the hexadecimal string (must be 6 chars length)
			// so add the missing chars if needed
			$hex = $value;
			while(strlen($hex) < 6){
				$hex = "0" . $hex;
			}
			// convert value from HEX to RGB
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
			// allocate the new color
			// N.B. teorically if a color was already allocated 
			// we dont need to allocate another time
			// but this is only an example
			$test = imagecolorallocate($img, $r, $g, $b);
			// and paste that color into the image
			// at the correct position
			imagesetpixel($img, $cols, $rows, $test);
		}
	}
}

// print out the correct header to the browser
//header("Content-type:image/jpeg");
// display the image

$dat = date("Y-m-d H:i:s");
$file_dat = date("Y-m-d_H-i-s",strtotime($dat));
$folder = $vorood ? 'vorood_img' : 'khorooj_img';
$moshtari_id = $conf->getMoshtari();
imagejpeg($img, "$folder/$moshtari_id"."_$user_id"."_$file_dat.png", 90);
$vr = new vorood_class($user_id,$dat,"$folder/$moshtari_id"."_$user_id"."_$file_dat.png",$vorood);
$user = new user_class($user_id);
//imagejpeg($img, "", 90);
$alert_msg = 'ثبت مجدد امکان پذیر نمی باشد';
if($vr->user_id > 0 && $vorood)
	$alert_msg = 'ورود کاربر به شماره پرسنلی '.$user_id.' '.$user->fname.' '.$user->lname.' در تاریخ '.jdate("Y/m/d",strtotime($vr->dat)).' ساعت '.jdate("H:i:s",strtotime($vr->dat)).' ثبت شد.';
else if($vr->user_id > 0 && !$vorood)
	$alert_msg = 'خروج کاربر به شماره پرسنلی '.$user_id.' '.$user->fname.' '.$user->lname.' در تاریخ '.jdate("Y/m/d",strtotime($vr->dat)).' ساعت '.jdate("H:i:s",strtotime($vr->dat)).' ثبت شد.';

?>
<html>
	<head>
                <!-- Style Includes -->
                <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

                <link type="text/css" href="../css/style.css" rel="stylesheet" />

                <!-- JavaScript Includes -->
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>

                <script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                </title>		
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<script language="javascript">
			alert('<?php echo $alert_msg; ?>');
			window.parent.location=window.parent.location;
		</script>
	</body>
</html>
