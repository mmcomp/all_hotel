<?php
	include("../kernel.php");
	session_start();
	$msg = '';
	if(isset($_REQUEST['tb']) && $_REQUEST['tb']!='')
	{
		$tb = $_REQUEST['tb'];
		$cg = new class_generator($tb);
		$f = fopen('../class/'.$tb.'_class.php','w+');
		fwrite($f,$cg->output);
		fclose($f);
		$msg = "<script> alert('Class Generated as $tb"."_class for $tb'); </script>";
	}

?>

<html>
	<head>
	</head>
	<body>
		<form id="frm1">
			Table Name : <input type="text" name="tb" id="tb" value="" /><br/>
			<input type="submit" value="generate class" />
		</form>
	<?php echo $msg; ?>
	</body>
</html>
